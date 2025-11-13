<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../../database/database.php";
try {
    $tabela_id = $_GET['tabela'] ?? null;
    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $excluirTabela_id = $_POST['excluirTabela_id'] ?? null;

        if (empty($excluirTabela_id)) {
            throw new Exception("Tabela a ser excluida nao selecionada.");
        }

        $database = new Database();

        //deleta os prompts da tabela
        $sql = "DELETE p, tp
                FROM prompts p
                JOIN tabela_prompts tp ON tp.prompt_id =p.id
                WHERE tp.tabela_id = ?";
        $resultDelPrompt = $database->deleteCustom($sql, [$excluirTabela_id]);

        if (!$resultDelPrompt['success']) {
            throw new Exception($resultDelPrompt['message']);
        }

        //deleta tabela
        $resultDelTabela = $database->delete("tabelas_bingo", ['id' => $excluirTabela_id]);

        if (!$resultDelTabela['success']) {
            throw new Exception($resultDelTabela['message']);
        }


        $_SESSION['msg'] = 'Tabela deletada com sucesso';
        if ($tabela_id == $excluirTabela_id) {
            // Estava vendo exatamente a tabela que foi apagada
            header("Location: ../../pages/menu.php");
        } else {
            // Continuar na tabela aberta anteriormente
            header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
        }
        exit;
    }
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao deletar tabela: " . $e->getMessage();
    header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
    exit;
}
?>