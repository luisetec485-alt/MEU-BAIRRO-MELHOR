<?php
require_once 'model/Conexao.php';
try {
    $pdo = Conexao::getConexao();
    $stmt = $pdo->query("SHOW COLUMNS FROM problemas LIKE 'bairro'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE problemas ADD COLUMN bairro VARCHAR(100) DEFAULT NULL");
        echo "Coluna bairro adicionada!\n";
    } else {
        echo "Coluna bairro ja existia.\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
