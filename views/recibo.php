<?php
require_once '../core/db.php';

// Função para renderizar o conteúdo do recibo para evitar duplicação de código
function render_receipt_content($volume, $itens) {
    ob_start();
?>
    <div class="recibo-header">
        <h4><i class="fas fa-hospital-user me-2"></i>Guarda-Volumes Hospitalar</h4>
        <h5>Via de Entrada</h5>
    </div>

    <div class="info-section">
        <p class="lead text-center mb-4"><strong>Armário Nº: <?= htmlspecialchars($volume['armario']) ?></strong></p>
        <div class="row">
            <div class="col-6">
                <p><strong>Paciente:</strong></p>
                <p><?= htmlspecialchars($volume['paciente']) ?></p>
            </div>
            <div class="col-6">
                <p><strong>Data/Hora:</strong></p>
                <p><?= date('d/m/Y H:i', strtotime($volume['data_entrada'])) ?></p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-6">
                <p><strong>Volumes Deixados:</strong></p>
                <p><?= count($itens) ?></p>
            </div>
            <div class="col-6">
                <p><strong>Itens:</strong></p>
                <p><?= htmlspecialchars(implode(', ', $itens)) ?></p>
            </div>
        </div>
    </div>

    <div class="signature-section">
        <div class="mt-4">
            <p><strong>Assinatura do Colaborador:</strong></p>
            <br>
            <p class="signature-line"></p>
            <p class="mt-2 small"><?= htmlspecialchars($volume['colaborador_entrada']) ?></p>
        </div>
        <br>
    </div>

    <p class="disclaimer">
        Eu, ____________________________________________________, portador do CPF _______________________________________, autorizo a doação dos volumes registrados acima, caso não sejam retirados no prazo de 45 dias da data de entrada.
    </p>
    
    <br>
    <br>
    <div class="signature-section" style="margin-top: 2rem;">
        <p class="signature-line" style="width: 80%;"></p>
        <p class="mt-2 small"><strong>Assinatura do Responsável</strong></p>
    </div>
<?php
    return ob_get_clean();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do recibo não fornecido!");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM volumes WHERE id = ?");
    $stmt->execute([$id]);
    $volume = $stmt->fetch();
} catch (PDOException $e) {
    die("Erro ao buscar dados do recibo: " . $e->getMessage());
}

if (!$volume) {
    die("Recibo não encontrado!");
}

$itens = json_decode($volume['itens_originais'], true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Entrada Duplicado - Guarda-Volumes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .page-container {
            max-width: 1600px;
            margin: 1rem auto;
        }
        .recibo-wrapper {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            padding: 2rem;
            height: 100%;
        }
        .recibo-header h4 {
            font-weight: 700;
            color: #0d6efd;
            font-size: 1.2rem;
        }
        .recibo-header h5 {
            font-weight: 600;
            color: #495057;
            font-size: 1rem;
        }
        .info-section p {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .info-section strong {
            color: #343a40;
        }
        .signature-section {
            margin-top: 1.5rem;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #adb5bd;
            margin: 0 auto;
            width: 90%;
        }
        .disclaimer {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #6c757d;
            text-align: justify;
        }
        .vertical-line {
            border-left: 2px dashed #adb5bd;
            min-height: 100%;
        }
        .actions {
            text-align: center;
            margin-top: 2rem;
            padding-bottom: 2rem;
        }
        .actions .btn {
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
        }
        @media print {
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-container {
                margin: 0;
                max-width: 100%;
            }
            .recibo-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 1rem;
                border: 1px solid #ccc; /* Adiciona uma borda para melhor visualização */
            }
            .col-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            .vertical-line {
                display: none; /* Oculta a linha no meio na impressão */
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid page-container">
        <div class="row">
            <!-- Coluna Esquerda -->
            <div class="col-6">
                <div class="recibo-wrapper">
                    <?= render_receipt_content($volume, $itens) ?>
                </div>
            </div>

            <!-- Linha Vertical (apenas visual) -->
            <div class="col-auto p-0 m-0 no-print">
                 <div class="vertical-line h-100"></div>
            </div>

            <!-- Coluna Direita -->
            <div class="col-6">
                <div class="recibo-wrapper">
                    <?= render_receipt_content($volume, $itens) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="actions no-print">
        <button onclick="window.print();" class="btn btn-primary"><i class="fas fa-print me-2"></i>Imprimir Recibos</button>
        <a href="../index.php" class="btn btn-secondary"><i class="fas fa-home me-2"></i>Voltar ao Início</a>
    </div>

</body>
</html>
