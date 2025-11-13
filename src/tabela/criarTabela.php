<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../../database/database.php";
try {
    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $nome = trim($_POST['nome']);
        $tamanho = intval($_POST['tamanho']);
        $user_id = $_SESSION['usuario'] ?? null;

        if (!$user_id) {
            throw new Exception("Usuário não autenticado.");
        }

        if (empty($nome) || $tamanho < 3) {
            throw new Exception("Dados inválidos.");
        }

        $database = new Database();
        $result = $database->create("tabelas_bingo", ['nome' => $nome,'user_id' => $user_id, 'tamanho' => $tamanho, 'vitoria' => 0]);

        if(!$result['success']){
            throw new Exception($result['message']);
        }

        $_SESSION['msg'] = 'Tabela criada com sucesso';
        header("Location: ../../pages/menu.php?tabela=". $result['id']);
        exit;
    }
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao criar tabela: " . $e->getMessage();
    header("Location: ../../pages/menu.php");
    exit;
}
?>