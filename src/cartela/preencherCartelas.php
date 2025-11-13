<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../database/database.php";
require_once __DIR__ . "/../prompt/funcoesPrompt.php";
require_once __DIR__ . "/../tabela/funcoesTabela.php";

//Arquivo para atualizar tabela tbm
try {

    $tabela_id = $_GET['tabela'] ?? null;

    if (empty($tabela_id)) {
        throw new Exception("Tabela não selecionada.");
    }

    $database = new Database();

    $tabelaInfo = buscarTabela($tabela_id);
    if (empty($tabelaInfo)) {
        throw new Exception("Tabela inexistente.");
    }

    //Atualiza o campo vitoria pra 0 novamente e deleta as cartelas  caso o usuario queira apenas atualizar.
    marcarVitoria($tabela_id);
    $database->delete('cartelas', ['tabela_id' => $tabela_id]);

    $tamanho = intval($tabelaInfo['tamanho']);
    $qtdNecessaria = $tamanho * $tamanho;

    $prompts = buscarPromptsTabela($tabela_id);

    

    if (count($prompts) < $qtdNecessaria) {
        throw new Exception("Número insuficiente de prompts para preencher a cartela ($qtdNecessaria necessários).");
    }

    shuffle($prompts);
    $promptsSelecionados = array_slice($prompts, 0, $qtdNecessaria);


    $posicao = 0;
    foreach ($promptsSelecionados as $prompt) {
        $x = intdiv($posicao, $tamanho);
        $y = $posicao % $tamanho;

        $database->create('cartelas', [
            'tabela_id' => $tabela_id,
            'prompt_id' => $prompt['id'],
            'marcada' => 0,
            'pos_x' => $x,
            'pos_y' => $y
        ]);

        $posicao++;
    }

    $_SESSION['msg'] = "Cartela preenchida e atualizada com sucesso com $qtdNecessaria prompts.";
    header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
    exit;

} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao preencher cartela: " . $e->getMessage();
    header("Location: ../../pages/menu.php?tabela=" . ($tabela_id ?? ''));
    exit;
}