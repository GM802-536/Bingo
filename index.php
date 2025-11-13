<?php
require_once "./database/connect.php";
require_once "./database/database.php";

/*try {
    $conn = Connect::getConnection();

    // Testa se consegue consultar o banco
    $stmt = $conn->query("SELECT DATABASE() AS db;");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "✅ Conectado com sucesso ao banco: " . $result['db'];
} catch (PDOException $e) {
    echo "❌ Erro na conexão: " . $e->getMessage();
}

echo "<br>";echo "<br>";

$database = new Database(); 
//var_dump($database->create("usuarios", ["username" => "mene", "senha" => md5("12345")]));
//var_dump($database->read("usuarios", ["username" => "mene"]));
//var_dump ($database->update("usuarios", ["username" => "menegati"] , ["username" => "mene"]));
//var_dump($database->delete("usuarios",["username"=> 'menetots']));
*/

header("Location: /pages/menu.php");