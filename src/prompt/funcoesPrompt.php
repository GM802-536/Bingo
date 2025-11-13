<?php
require_once __DIR__ . "/../../database/database.php";

function buscarPromptsTabela($tabela_id)
{
    try {
        if (empty($tabela_id)) {
            throw new Exception("Tabela não selecionada.");
        }

        $database = new Database();

        //Eu tinha feito assim mas achei meio confuso 
        //$prompts = $database->read('tabela_prompts tp JOIN prompts p ON tp.prompt_id = p.id', ['tp.tabela_id' => $tabela_id]);

        $prompts = $database->readCustom("
            SELECT p.id, p.texto
            FROM tabela_prompts tp
            JOIN prompts p ON tp.prompt_id = p.id
            WHERE tp.tabela_id = ?
        ", [$tabela_id]);

        if (!$prompts['success']) {
            throw new Exception($prompts['message']);
        }

        return $prompts['data'];

    } catch (Exception $e) {
        error_log("Erro ao buscar prompts: " . $e->getMessage());
        return [];
    }
}

function buscarPrompt($id)
{
    try {
        $database = new Database();
        $result = $database->read('prompts', ['id' => $id], 1);

        if (!$result['success'] || $result['count'] === 0) {
            throw new Exception("Prompt não encontrado.");
        }

        return $result['data'][0];
    } catch (Exception $e) {
        error_log("Erro ao buscar prompt: " . $e->getMessage());
        return ['texto' => ''];
    }
}
