<?php
// core/setup.php

// Constantes para a configuração inicial do banco de dados.
// Usadas para criar o banco de dados e a tabela inicial.
const DB_HOST_SETUP = 'localhost';
const DB_NAME_SETUP = 'guarda_volume';
const DB_USER_SETUP = 'root';
const DB_PASS_SETUP = '';

try {
    // Conecta ao MySQL sem especificar um banco de dados para poder criá-lo.
    $pdo_setup = new PDO("mysql:host=" . DB_HOST_SETUP, DB_USER_SETUP, DB_PASS_SETUP);
    $pdo_setup->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria o banco de dados 'guarda_volume' se ele não existir.
    $pdo_setup->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME_SETUP . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    
    // Seleciona o banco de dados recém-criado ou já existente.
    $pdo_setup->exec("USE `" . DB_NAME_SETUP . "`;");

    // SQL para criar a tabela 'volumes' se ela não existir.
    // A estrutura da tabela é baseada nos dados manipulados em 'processa.php' e 'index.php'.
    $sql_create_table = "
    CREATE TABLE IF NOT EXISTS `volumes` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `armario` VARCHAR(50) NOT NULL,
      `paciente` VARCHAR(255) NOT NULL,
      `contato` VARCHAR(50) NOT NULL,
      `itens_originais` JSON NOT NULL,
      `itens_atuais` JSON NOT NULL,
      `data_entrada` DATETIME NOT NULL,
      `responsavel_entrada` VARCHAR(255) NOT NULL,
      `colaborador_entrada` VARCHAR(255) NOT NULL,
      `historico_saidas` JSON NOT NULL,
      `status` VARCHAR(20) NOT NULL DEFAULT 'ativo'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    // Executa o comando para criar a tabela.
    $pdo_setup->exec($sql_create_table);

} catch (PDOException $e) {
    // Em caso de erro, exibe a mensagem e interrompe o script.
    // Em um ambiente de produção, seria ideal logar este erro em vez de exibi-lo.
    die("Erro na configuração do banco de dados: " . $e->getMessage());
}

// Libera a variável de conexão do setup.
unset($pdo_setup);
