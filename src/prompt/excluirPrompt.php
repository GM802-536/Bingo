<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../database/database.php";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $prompt_id = $_POST['prompt_id'] ?? null;
        $tabela_id = $_POST['tabela_id'] ?? null;

        if (empty($prompt_id)) {
            throw new Exception("Prompt nao selecionado");
        }

        if (empty($tabela_id)) {
            throw new Exception("Tabela nao selecionada");
        }


        $database = new Database();
        $result = $database->delete("prompts",  ['id' => $prompt_id]);

        if (!$result['success']) {
            throw new Exception("Prompt nao deletado: " . $result['message']);
        }

        $_SESSION['msg'] = "Prompt deletado com sucesso";
        header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
        exit;

    }

} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao editar prompt: " . $e->getMessage();
    header("Location: ../../pages/menu.php?tabela=" . ($_POST['tabela_id'] ?? ''));
    exit;
}