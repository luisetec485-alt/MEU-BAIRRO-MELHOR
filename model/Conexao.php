<?php

class Conexao {

    public static function getConexao() {

        $host = "localhost";            
        $port = "3306"; // 3306 nos PCs da escola
        $dbname = "mbm";                
        $user = "root";                
        $pass = "";             

        try {
            $conexao = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );

            $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            return $conexao;

        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
}

?>