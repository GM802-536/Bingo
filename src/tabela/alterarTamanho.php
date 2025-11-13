<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../database/database.php";

try{

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $novoTamanho = intval($_POST['tamanho'] ?? 0);
        $tabela_id = $_POST['tabela_id'] ?? null;

        if(empty($tabela_id)){
            throw new Exception("Tabela nao selecionada");
        }

        if(empty($novoTamanho)){
            throw new Exception("Tamanho nao selecionada");
        }

        $database = new Database();
        $result = $database->update('tabelas_bingo', ['tamanho'=> $novoTamanho], ['id'=>$tabela_id]);

        //aqui e so pra limpar as cartelas pois se a quantide de cartelas ja criadas for maior que o tamanho atualizado, a tabela fica zoada
        $database->delete('cartelas', ['tabela_id' => $tabela_id]);


        if(!$result['success']){
            throw new Exception($result['message']);
        }

        $_SESSION['msg'] = "Tamanho da tabela atualizado com sucesso";
        header("Location: ../../pages/menu.php?tabela=" . $tabela_id);
        exit;

    }
} catch(Exception $e){
    $_SESSION['erro'] = "Erro no sistema: " . $e->getMessage();
    header("Location: ../../pages/menu.php");
    exit;
}
?>