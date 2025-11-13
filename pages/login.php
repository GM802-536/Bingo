<?php
//Redireciona caso já esteja logado:
session_start();
if (isset($_SESSION['usuario']))
    header("Location: ../index.php");
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bingo</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

    <div class="container">
        <div class="login-box">
            <h2>Login</h2>
            <?php if ($erro): ?>
                <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            <form action="../src/user/validarLogin.php" method="POST">
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" name="username" id="username" >
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" name="password" id="password" >
                </div>

                <button type="submit" class="btn-login">Entrar</button>
            </form>
        </div>
    </div>

</body>

</html>