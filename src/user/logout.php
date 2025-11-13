<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if(isset($_SESSION['usuario'])){
    unset($_SESSION['usuario']);
    session_unset();
    session_destroy();
    header("Location: ../../pages/login.php" . $tabela_id);
    exit;
}

?>
