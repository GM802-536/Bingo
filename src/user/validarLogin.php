<?php
session_start();

require_once "../../database/database.php";

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = md5($_POST['password']) ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['erro'] = "Preencha todos os campos";
        header("Location: ../../pages/login.php");
        
    } else {
        try {
            $database = new Database();
            $usuario = $database->read("usuarios", ["username" => "$username", "senha" => "$password"], 1);

            if (count($usuario['data']) > 0) {
                $_SESSION['usuario'] = $usuario['data'][0]['id'];
                unset($_SESSION['erro']);
                header("Location: ../../pages/menu.php");
                exit();

            } else {
                $_SESSION['erro'] = 'email ou senha incorretos';
                header("Location: ../../pages/login.php");
            }

        } catch (\Throwable $th) {
            $erro = 'Erro no sistema: ' . $usuario['message'];
        }
    }
}


?>