<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'db.php';

// Define o fuso horário padrão para todas as operações de data/hora no script
date_default_timezone_set('America/Sao_Paulo');

// Função para redirecionar com segurança
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Roteamento de ações
if (isset($_POST['acao'])) {
    $id = $_POST['id'] ?? null;

    switch ($_POST['acao']) {
        case 'registrar_entrada':            // Validações do lado do servidor            $erros = [];            if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomePaciente'])) {                $erros[] = "Nome do paciente inválido.";            }            if (!filter_var($_POST['numArmario'], FILTER_VALIDATE_INT)) {                $erros[] = "Número do armário inválido.";            }            if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomeResponsavel'])) {                $erros[] = "Nome do responsável inválido.";            }            if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomeColaborador'])) {                $erros[] = "Nome do colaborador inválido.";            }            if (count($erros) > 0) {                die(implode("\n", $erros));            }            // Validação do contato
            $contato = $_POST['contato'];
            if (!preg_match('/^\(\d{2}\)\s\d{4,5}-\d{4}$/', $contato)) {
                die("Formato de contato inválido. Use (DD) 0000-0000 ou (DD) 00000-0000.");
            }

            $itensPost = $_POST['itens'] ?? [];
            $outroItem = !empty(trim($_POST['outroItem'])) ? [htmlspecialchars(trim($_POST['outroItem']), ENT_QUOTES, 'UTF-8')] : [];
            $itens = array_merge($itensPost, $outroItem);

            $dataEntrada = date('Y-m-d H:i:s');

            $sql = "INSERT INTO volumes (armario, paciente, contato, itens_originais, itens_atuais, data_entrada, responsavel_entrada, colaborador_entrada, historico_saidas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $_POST['numArmario'],
                    $_POST['nomePaciente'],
                    $contato,
                    json_encode($itens), // Salva como JSON
                    json_encode($itens), // Salva como JSON
                    $dataEntrada,
                    $_POST['nomeResponsavel'],
                    $_POST['nomeColaborador'],
                    '[]' // Histórico de saídas começa como um array JSON vazio
                ]);
                $newId = $pdo->lastInsertId();
                redirect('../views/recibo.php?id=' . $newId);
            } catch (PDOException $e) {
                die("Erro ao registrar entrada: " . $e->getMessage());
            }
            break;

        case 'registrar_saida_parcial':
            if ($id && !empty($_POST['itens_retirada'])) {
                // Validações para saída parcial
                $erros = [];
                if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomeResponsavel'])) {
                    $erros[] = "Nome do responsável (saída parcial) inválido.";
                }
                if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomeColaborador'])) {
                    $erros[] = "Nome do colaborador (saída parcial) inválido.";
                }
                if (count($erros) > 0) {
                    die(implode("\n", $erros));
                }

                try {
                    // 1. Busca o estado atual do volume
                    $stmt = $pdo->prepare("SELECT itens_atuais, historico_saidas FROM volumes WHERE id = ?");
                    $stmt->execute([$id]);
                    $volume = $stmt->fetch();

                    if ($volume) {
                        $itensAtuais = json_decode($volume['itens_atuais'], true);
                        $historico = json_decode($volume['historico_saidas'], true);
                        $itensRetirada = $_POST['itens_retirada'];

                        // 2. Atualiza os dados
                        $novosItensAtuais = array_diff($itensAtuais, $itensRetirada);
                        $historico[] = [
                            'tipo' => 'Parcial',
                            'data' => date('Y-m-d H:i:s'),
                            'itens' => $itensRetirada,
                            'responsavel' => $_POST['nomeResponsavel'],
                            'colaborador' => $_POST['nomeColaborador']
                        ];
                        
                        $novoStatus = empty($novosItensAtuais) ? 'inativo' : 'ativo';

                        // 3. Salva no banco
                        $sql = "UPDATE volumes SET itens_atuais = ?, historico_saidas = ?, status = ? WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([json_encode($novosItensAtuais), json_encode($historico), $novoStatus, $id]);
                    }
                    redirect('../views/detalhes_volume.php?id=' . $id);
                } catch (PDOException $e) {
                    die("Erro ao registrar saída parcial: " . $e->getMessage());
                }
            }
            break;

        case 'registrar_saida_total':            if ($id) {                // Validações para saída total                $erros = [];                if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomeResponsavel'])) {                    $erros[] = "Nome do responsável (saída total) inválido.";                }                if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['nomeColaborador'])) {                    $erros[] = "Nome do colaborador (saída total) inválido.";                }                if (count($erros) > 0) {                    die(implode("\n", $erros));                }                try {                    // 1. Busca o estado atual do volume
                    $stmt = $pdo->prepare("SELECT itens_atuais, historico_saidas FROM volumes WHERE id = ?");
                    $stmt->execute([$id]);
                    $volume = $stmt->fetch();
                    
                try {
                    if ($volume) {
                        $itensAtuais = json_decode($volume['itens_atuais'], true);
                        $historico = json_decode($volume['historico_saidas'], true);

                        // 2. Atualiza os dados
                        $historico[] = [
                            'tipo' => 'Total',
                            'data' => date('Y-m-d H:i:s'),
                            'itens' => $itensAtuais, // Itens restantes
                            'responsavel' => $_POST['nomeResponsavel'],
                            'colaborador' => $_POST['nomeColaborador']
                        ];

                        // 3. Salva no banco
                        $sql = "UPDATE volumes SET itens_atuais = ?, historico_saidas = ?, status = 'inativo' WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['[]', json_encode($historico), $id]);
                    }
                    redirect('../views/detalhes_volume.php?id=' . $id);
                } catch (PDOException $e) {
                    die("Erro ao registrar saída total: " . $e->getMessage());
                }
            }
            break;
    }
}