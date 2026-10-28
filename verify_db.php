<?php
require_once 'model/Conexao.php';
try {
    $pdo = Conexao::getConexao();
    $stmt = $pdo->query("DESCRIBE problemas");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
