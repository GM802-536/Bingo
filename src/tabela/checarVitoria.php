<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../database/database.php";
require_once __DIR__ . "/funcoesTabela.php";
require_once __DIR__ . "/../cartela/funcoesCartela.php";

header("Content-Type: application/json; charset=utf-8");

try {

    $tabela_id = $_GET['tabela'] ?? null;
    if (empty($tabela_id)) {
        throw new Exception("Tabela não selecionada.");
    }

    $cartelas = buscarCartelasTabela($tabela_id);
    if (empty($cartelas)) {
        throw new Exception("Cartelas não encontradas.");
    }

    $tabela = buscarTabela($tabela_id);
    $tamanho = $tabela['tamanho'];

    // ----- Montar matriz -----
    $matriz = [];
    foreach ($cartelas as $c) {
        $x = (int) $c['pos_x'];
        $y = (int) $c['pos_y'];
        $matriz[$x][$y] = (int) $c['marcada'];
    }

    $vitoria = false;

    // ----- Checar Linhas -----
    for ($x = 0; $x < $tamanho; $x++) {
        if (array_sum($matriz[$x]) == $tamanho) {
            $vitoria = true;
        }
    }

    // ----- Checar Colunas -----
    for ($y = 0; $y < $tamanho; $y++) {
        $coluna = array_column($matriz, $y);
        if (array_sum($coluna) == $tamanho) {
            $vitoria = true;
        }
    }

    // ----- Diagonal Principal -----
    $ok = true;
    for ($i = 0; $i < $tamanho; $i++) {
        if ($matriz[$i][$i] == 0) {
            $ok = false;
        }
    }
    if ($ok) $vitoria = true;

    // ----- Diagonal Secundária -----
    $ok = true;
    for ($i = 0; $i < $tamanho; $i++) {
        if ($matriz[$i][$tamanho - 1 - $i] == 0) {
            $ok = false;
        }
    }
    if ($ok) $vitoria = true;

    // ----- Marcar vitória no banco -----
    if ($vitoria) {
        marcarVitoria($tabela_id);
    }

    echo json_encode([
        'success' => true,
        'vitoria' => $vitoria
    ]);
    exit;

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
