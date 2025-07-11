<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Entrada - Guarda-Volumes</title>
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
        <h2>Registro de Novo Volume</h2>
        <hr>

        <form action="../core/processa.php" method="POST">
            <input type="hidden" name="acao" value="registrar_entrada"> <!-- O action será alterado para o script PHP que processará os dados -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nomePaciente" class="form-label">Nome Completo do Paciente</label>
                    <input type="text" class="form-control" id="nomePaciente" name="nomePaciente" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="contato" class="form-label">Nº de Contato</label>
                    <input type="text" class="form-control" id="contato" name="contato" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Itens Recebidos</label>
                <div class="form-check">
                    <input class="form-check-input item-check" type="checkbox" value="Mochila" id="itemMochila" name="itens[]">
                    <label class="form-check-label" for="itemMochila">Mochila</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input item-check" type="checkbox" value="Bolsa" id="itemBolsa" name="itens[]">
                    <label class="form-check-label" for="itemBolsa">Bolsa</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input item-check" type="checkbox" value="Mala" id="itemMala" name="itens[]">
                    <label class="form-check-label" for="itemMala">Mala</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input item-check" type="checkbox" value="Ventilador" id="itemVentilador" name="itens[]">
                    <label class="form-check-label" for="itemVentilador">Ventilador</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input item-check" type="checkbox" value="Aspirador" id="itemAspirador" name="itens[]">
                    <label class="form-check-label" for="itemAspirador">Aspirador</label>
                </div>
                <div class="mt-2">
                    <label for="outroItem" class="form-label">Outro Item (especificar)</label>
                    <input type="text" class="form-control" id="outroItem" name="outroItem">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="numArmario" class="form-label">Nº do Armário</label>
                    <input type="number" class="form-control" id="numArmario" name="numArmario" required>
                </div>
                <div class="col-md-4 mb-3" style="display: none;">
                    <label for="dataEntrada" class="form-label">Data de Entrada</label>
                    <input type="date" class="form-control" id="dataEntrada" name="dataEntrada" required>
                </div>
                <div class="col-md-4 mb-3" style="display: none;">
                    <label for="horaEntrada" class="form-label">Hora de Entrada</label>
                    <input type="time" class="form-control" id="horaEntrada" name="horaEntrada" required>
                </div>
            </div>

            

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nomeResponsavel" class="form-label">Nome do Responsável (quem deixou)</label>
                    <input type="text" class="form-control" id="nomeResponsavel" name="nomeResponsavel" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nomeColaborador" class="form-label">Nome do Colaborador (quem recebeu)</label>
                    <input type="text" class="form-control" id="nomeColaborador" name="nomeColaborador" required>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">Registrar e Gerar Recibo</button>
            <a href="../index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script>
        const contatoInput = document.getElementById('contato');

        contatoInput.addEventListener('input', () => {
            let value = contatoInput.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            }
            if (value.length > 8) {
                value = `${value.substring(0, 9)}-${value.substring(9)}`;
            }
            if (value.length > 14) {
                value = value.substring(0, 14);
            }
            contatoInput.value = value;
        });
    </script>
    <script>
        // Máscaras de campo
        const nomePacienteInput = document.getElementById('nomePaciente');
        const numArmarioInput = document.getElementById('numArmario');
        const nomeResponsavelInput = document.getElementById('nomeResponsavel');
        const nomeColaboradorInput = document.getElementById('nomeColaborador');

        const outroItemInput = document.getElementById('outroItem');

        // Sanitiza o campo "Outro Item"
        const sanitizeInput = (e) => {
            e.target.value = e.target.value.replace(/[<>]/g, '');
        };

        outroItemInput.addEventListener('input', sanitizeInput);

        // Apenas letras e espaços para nomes
        const forceLetters = (e) => {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
        };

        // Apenas números para o armário
        const forceNumbers = (e) => {
            e.target.value = e.target.value.replace(/\D/g, '');
        };

        nomePacienteInput.addEventListener('input', forceLetters);
        nomeResponsavelInput.addEventListener('input', forceLetters);
        nomeColaboradorInput.addEventListener('input', forceLetters);
        numArmarioInput.addEventListener('input', forceNumbers);
    </script>
    <script>
        // Preenche data e hora atuais
        document.getElementById('dataEntrada').valueAsDate = new Date();
        document.getElementById('horaEntrada').value = new Date().toTimeString().slice(0, 5);

        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
