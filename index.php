<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guarda-Volumes Hospitalar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Guarda-Volumes</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Volumes Armazenados</h1>
            <a href="views/entrada.php" class="btn btn-success">Nova Entrada</a>
        </div>

        <!-- Formulário de Busca -->
        <form action="index.php" method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="busca" class="form-control" placeholder="Digite sua busca..." value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>">
                <select name="tipo_busca" class="form-select">
                    <option value="paciente" <?= (($_GET['tipo_busca'] ?? '') == 'paciente') ? 'selected' : '' ?>>Nome do Paciente</option>
                    <option value="responsavel" <?= (($_GET['tipo_busca'] ?? '') == 'responsavel') ? 'selected' : '' ?>>Nome do Responsável</option>
                    <option value="armario" <?= (($_GET['tipo_busca'] ?? '') == 'armario') ? 'selected' : '' ?>>Número do Armário</option>
                </select>
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </form>

        <?php
        require_once 'core/db.php';

        // Configurações de Paginação
        $porPagina = 5;
        $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($paginaAtual - 1) * $porPagina;

        // Lógica de Busca
        $busca = $_GET['busca'] ?? '';
        $tipo_busca = $_GET['tipo_busca'] ?? 'paciente';
        
        $sql_base = "FROM volumes WHERE status = 'ativo'";
        $params = [];

        if (!empty($busca)) {
            $search_column = '';
            switch ($tipo_busca) {
                case 'paciente':
                    $search_column = 'paciente';
                    break;
                case 'responsavel':
                    $search_column = 'responsavel_entrada';
                    break;
                case 'armario':
                    $search_column = 'armario';
                    break;
            }
            if ($search_column) {
                $sql_base .= " AND $search_column LIKE :busca";
                $params[':busca'] = "%$busca%";
            }
        }

        try {
            // Conta o total de registros para a paginação
            $sql_total = "SELECT COUNT(id) " . $sql_base;
            $stmt_total = $pdo->prepare($sql_total);
            $stmt_total->execute($params);
            $totalRegistros = $stmt_total->fetchColumn();
            $totalPaginas = ceil($totalRegistros / $porPagina);

            // Busca os registros da página atual
            $sql_pagina = "SELECT id, armario, paciente, itens_atuais, data_entrada " . $sql_base . " ORDER BY data_entrada DESC LIMIT :offset, :porpagina";
            $stmt_pagina = $pdo->prepare($sql_pagina);
            
            // Bind dos parâmetros da busca (se existirem)
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt_pagina->bindValue($key, $value);
                }
            }
            
            // Bind dos parâmetros de paginação
            $stmt_pagina->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt_pagina->bindValue(':porpagina', $porPagina, PDO::PARAM_INT);
            
            $stmt_pagina->execute();
            $volumes = $stmt_pagina->fetchAll();

        } catch (PDOException $e) {
            die("Erro ao buscar volumes: " . $e->getMessage());
        }
        ?>
        <div class="list-group">
            <?php if (empty($volumes)): ?>
                <p class="text-center">Nenhum volume encontrado para os critérios de busca.</p>
            <?php else: ?>
                <?php foreach ($volumes as $volume): ?>
                    <?php $itens = json_decode($volume['itens_atuais'], true); ?>
                    <a href="views/detalhes_volume.php?id=<?= $volume['id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Armário: <?= htmlspecialchars($volume['armario']) ?></h5>
                            <small>Data Entrada: <?= date('d/m/Y H:i', strtotime($volume['data_entrada'])) ?></small>
                        </div>
                        <p class="mb-1">Paciente: <?= htmlspecialchars($volume['paciente']) ?></p>
                        <small>Volumes: <?= count($itens) ?> (<?= htmlspecialchars(implode(', ', $itens)) ?>)</small>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Navegação da Paginação -->
        <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Navegação de página" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= ($i == $paginaAtual) ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>&tipo_busca=<?= urlencode($tipo_busca) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
