<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../database/database.php";

try {

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $cartela_id = $_POST['cartela_id'] ?? null;
        if (empty($cartela_id)) {
            throw new Exception("Cartela não identificada.");
        }

        $database = new Database();

        $result = $database->read('cartelas', ['id'=>"$cartela_id"], 1);
        if ($result['count'] === 0) {
            throw new Exception("Cartela inexistente: " . $result['message']);
        }

        $marcadaAtual = $result['data'][0]['marcada'];
        $novaMarcacao= $marcadaAtual ? 0 : 1;

        $update = $database->update("cartelas", ['marcada'=>$novaMarcacao], ['id'=>$cartela_id]);
        if(!$update['success']){
            throw new Exception($update['message']);
        }

        echo json_encode([
            'success' => true,
            'marcada' => $novaMarcacao
        ]);
        exit;
    }
    throw new Exception("Requisição inválida.");
}catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
