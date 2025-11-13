<?php
require_once __DIR__ . "/../../database/database.php";

function buscarCartelasTabela($tabela_id)
{
    try {
        $database = new Database();
        $cartelas = $database->read('cartelas', ['tabela_id' => $tabela_id]);

        if (!$cartelas['success']) {
            throw new Exception($cartelas['message']);
        }

        return $cartelas['data'];
    } catch (Exception $e) {
        error_log("Erro ao buscar cartelas: " . $e->getMessage());
        return [];
    }
}
