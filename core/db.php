<?php
// Executa o script de configuração para garantir que o banco de dados e a tabela existam.
require_once 'setup.php';

// Configurações do Banco de Dados
const DB_HOST = 'localhost';
const DB_NAME = 'guarda_volume'; // Nome do seu banco de dados
const DB_USER = 'root';             // Seu usuário do MySQL
const DB_PASS = '';               // Sua senha do MySQL

// Opções do PDO para um comportamento mais seguro
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os resultados como arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desativa a emulação de prepared statements
];

// Tenta estabelecer a conexão
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Em caso de falha na conexão, exibe uma mensagem de erro genérica
    // Em um ambiente de produção, você poderia logar o erro em vez de exibi-lo
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
