<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../database/database.php";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $prompt_id = $_POST['prompt_id'] ?? null;
        $tabela_id = $_POST['tabela_id'] ?? null;
        $novo_texto = $_POST['novo_texto'] ?? '';

        if (empty($prompt_id)) {
            throw new Exception("Prompt nao selecionado");
        }

        if (empty($tabela_id)) {
            throw new Exception("Tabela nao selecionada");
        }

        if (empty($novo_texto)) {
            throw new Exception("Texto invalido");
        }

        $database = new Database();
        $result = $database->update("prompts", ['texto' => $novo_texto], ['id' => $prompt_id]);

        if (!$result['success']) {
            throw new Exception("Prompt nao atualizado: " . $result['message']);
        }

        $_SESSION['msg'] = "Prompt atualizado com sucesso";
        header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
        exit;

    }

} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao editar prompt: " . $e->getMessage();
    header("Location: ../../pages/menu.php?tabela=" . ($_POST['tabela_id'] ?? ''));
    exit;
}