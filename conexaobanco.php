<?php
// Configurações do banco de dados
$host = 'localhost'; // Endereço do servidor
$db = 'vendas'; // Nome do banco de dados
$user = 'host'; // Nome de usuário do banco de dados
$pass = ''; // Senha do banco de dados

// Criando a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

echo "Conexão bem-sucedida!";

// Fechar a conexão
$conn->close();
?>
