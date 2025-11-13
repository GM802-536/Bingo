<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../database/database.php";

try {
    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $texto = trim($_POST['texto'] ?? '');
        $tabela_id = $_POST['tabela_id'] ?? null;

        if (empty($tabela_id)) {
            throw new Exception("Tabela nao selecionada");
        }

        if (empty($texto)) {
            throw new Exception("Texto invalido");
        }

        $database = new Database();

        //inserir o prompt na tabela prompt
        $result_Prompt = $database->create('prompts', ['texto' => $texto]);

        if (!$result_Prompt['success']){
            throw new Exception("Erro ao inserir no prompt: " . $result_Prompt['message']);
        }

        $id_Prompt = $result_Prompt['id'];

        //relacionar tabela e prompt em tabela_prompt
        $resultTabela_Prompt = $database->create('tabela_prompts', ['tabela_id' => $tabela_id, 'prompt_id' => $id_Prompt]);


        if (!$resultTabela_Prompt['success']){
            throw new Exception("Erro ao vincular prompt a tabela: " . $resultTabela_Prompt['message']);
        }

        $_SESSION['msg'] = "Prompt inserido com sucesso";
        header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
        exit;


    }

}catch(Exception $e){
    $_SESSION['erro'] = "Erro no sistema: " . $e->getMessage();
    header("Location: ../../pages/menu.php?tabela=".($_POST['tabela_id'] ?? ''));
    exit;
}
?>