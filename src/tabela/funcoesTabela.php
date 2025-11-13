<?php
require_once __DIR__ . "/../../database/database.php";

function buscarTabelasUsuario($user_id)
{
    try {
        if (!$user_id) {
            throw new Exception("Usuário não autenticado.");
        }

        $database = new Database();
        $tabelas = $database->read('tabelas_bingo', ['user_id' => $user_id]);

        if (!$tabelas['success']) {
            throw new Exception($tabelas['message']);
        }

        return $tabelas['data'];

    } catch (Exception $e) {
        error_log("Erro ao buscar tabelas: " . $e->getMessage());
        return [];
    }
}

function buscarTabela($id)
{
    try {
        $database = new Database();
        $result = $database->read('tabelas_bingo', ['id' => $id], 1);

        if (!$result['success'] || $result['count'] === 0) {
            throw new Exception("Tabela não encontrada.");
        }

        return $result['data'][0];
    } catch (Exception $e) {
        error_log("Erro ao buscar tabela: " . $e->getMessage());
        return null;
    }
}

function marcarVitoria($tabela_id){
    try {
        $tabela = buscarTabela($tabela_id);
        $vitoria = $tabela['vitoria'] ? 0:1;
        
        $database = new Database();
        $result = $database->update('tabelas_bingo', ['vitoria' => $vitoria], ['id'=>$tabela_id]);

        if (!$result['success']) {
            throw new Exception("Tabela não atualizada.");
        }

    } catch (Exception $e) {
        error_log("Erro ao buscar tabela: " . $e->getMessage());
    }
}

