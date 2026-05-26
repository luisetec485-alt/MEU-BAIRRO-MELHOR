<?php

// Define uma classe chamada Conexao
class Conexao {

    // Método estático (pode ser chamado SEM INSTANCIAR a classe)
    public static function getConexao() {

        // Configurações de conexão com o banco de dados
        $host = "localhost";            // Endereço do servidor (local)
        $port = "3307";                 // Porta do MySQL (3306 nos PCs da escola)
        $dbname = "mbm";                // Nome do banco de dados
        $user = "root";                 // Usuário do banco
        $pass = "";                     // Senha do banco (vazia no XAMPP)

        try {
            // Cria uma nova conexão usando PDO (PHP Data Objects)
            $conexao = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );

            // Define o modo de erro para exceções
            $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Retorna a conexão criada
            return $conexao;

        } catch (PDOException $e) {
            // Caso ocorra erro na conexão, interrompe o programa e mostra a mensagem
            die("Erro na conexão: " . $e->getMessage());
        }
    }
}

// Arquivo com apenas PHP, não precisa fechar a tag ?>