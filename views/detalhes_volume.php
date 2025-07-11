<?php
require_once '../core/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do volume não fornecido!");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM volumes WHERE id = ?");
    $stmt->execute([$id]);
    $volume = $stmt->fetch();
} catch (PDOException $e) {
    die("Erro ao buscar dados do volume: " . $e->getMessage());
}

if (!$volume) {
    die("Volume não encontrado!");
}

$itensAtuais = json_decode($volume['itens_atuais'], true);
$historicoSaidas = json_decode($volume['historico_saidas'], true);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Volume - Guarda-Volumes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">Guarda-Volumes</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Detalhes do Armário: <?= htmlspecialchars($volume['armario']) ?></h2>
        <hr>

        <div class="card mb-4">
            <div class="card-header">Informações de Entrada</div>
            <div class="card-body">
                <p><strong>Paciente:</strong> <?= htmlspecialchars($volume['paciente']) ?></p>
                <p><strong>Contato:</strong> <?= htmlspecialchars($volume['contato']) ?></p>
                <p><strong>Data/Hora de Entrada:</strong> <?= date('d/m/Y H:i', strtotime($volume['data_entrada'])) ?></p>
                <p><strong>Itens Atuais:</strong> <?= !empty($itensAtuais) ? htmlspecialchars(implode(', ', $itensAtuais)) : 'Nenhum item restante.' ?></p>
                <p><strong>Volumes Atuais:</strong> <?= count($itensAtuais) ?></p>
                <p><strong>Responsável (Entrada):</strong> <?= htmlspecialchars($volume['responsavel_entrada']) ?></p>
                <p><strong>Colaborador (Entrada):</strong> <?= htmlspecialchars($volume['colaborador_entrada']) ?></p>
            </div>
        </div>

        <!-- Seção de Saída Parcial -->
        <?php if ($volume['status'] == 'ativo' && count($itensAtuais) > 1): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">Registrar Saída Parcial</div>
            <div class="card-body">
                <form action="../core/processa.php" method="POST">
                    <input type="hidden" name="id" value="<?= $volume['id'] ?>">
                    <input type="hidden" name="acao" value="registrar_saida_parcial">

                    <div class="mb-3">
                        <label class="form-label">Itens a Retirar (selecione ao menos um):</label>
                        <?php foreach ($itensAtuais as $item): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= htmlspecialchars($item) ?>" id="retirar_<?= htmlspecialchars($item) ?>" name="itens_retirada[]">
                            <label class="form-check-label" for="retirar_<?= htmlspecialchars($item) ?>"><?= htmlspecialchars($item) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nomeResponsavelSaidaParcial" class="form-label">Nome do Responsável (quem retira)</label>
                            <input type="text" class="form-control" id="nomeResponsavelSaidaParcial" name="nomeResponsavel" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nomeColaboradorSaidaParcial" class="form-label">Nome do Colaborador (quem entrega)</label>
                            <input type="text" class="form-control" id="nomeColaboradorSaidaParcial" name="nomeColaborador" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">Registrar Saída Parcial</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Seção de Saída Total -->
        <?php if ($volume['status'] == 'ativo'): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">Registrar Saída Total</div>
            <div class="card-body">
                <form action="../core/processa.php" method="POST">
                    <input type="hidden" name="id" value="<?= $volume['id'] ?>">
                    <input type="hidden" name="acao" value="registrar_saida_total">
                    <p>Esta ação registrará a saída de <strong>todos os itens restantes</strong> (<?= htmlspecialchars(implode(', ', $itensAtuais)) ?>) e liberará o armário.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nomeResponsavelSaidaTotal" class="form-label">Nome do Responsável (quem retira)</label>
                            <input type="text" class="form-control" id="nomeResponsavelSaidaTotal" name="nomeResponsavel" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nomeColaboradorSaidaTotal" class="form-label">Nome do Colaborador (quem entrega)</label>
                            <input type="text" class="form-control" id="nomeColaboradorSaidaTotal" name="nomeColaborador" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger">Registrar Saída Total e Liberar Armário</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Histórico de Movimentações -->
        <div class="card">
            <div class="card-header">Histórico de Movimentações</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Entrada:</strong> <?= date('d/m/Y H:i', strtotime($volume['data_entrada'])) ?> - Itens: <?= htmlspecialchars(implode(', ', json_decode($volume['itens_originais'], true))) ?>.</li>
                <?php if (!empty($historicoSaidas)): ?>
                    <?php foreach ($historicoSaidas as $saida): ?>
                    <li class="list-group-item">
                        <strong>Saída <?= htmlspecialchars($saida['tipo']) ?> (<?= date('d/m/Y H:i', strtotime($saida['data'])) ?>):</strong> 
                        Retirada de: <?= htmlspecialchars(implode(', ', $saida['itens'])) ?>.
                        <small class="d-block">Por: <?= htmlspecialchars($saida['responsavel']) ?> / Entregue por: <?= htmlspecialchars($saida['colaborador']) ?></small>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>