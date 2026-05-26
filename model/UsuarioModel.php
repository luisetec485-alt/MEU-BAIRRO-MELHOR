<?php
require_once 'Conexao.php';

class UsuarioModel {
    
    // cadastrar usuario
    public function cadastrar($nome, $email, $senha) {
        $conexao = Conexao::getConexao();

        // verificar se o email já existe
        $stmt = $conexao->prepare("SELECT * FROM login WHERE Email = ?");
        $stmt->execute([$email]);

        // Se já existir, retorna erro
        if($stmt->rowCount() > 0){
            return "emailExiste";
        }

        // inserir no banco (senha já vem criptografada do controller)
        $stmt = $conexao->prepare(
            "INSERT INTO login (nomeUsuario, Senha, Email) VALUES (?, ?, ?)"
        );
        $stmt->execute([$nome, $senha, $email]);

        return "ok";
    }
    
    // logar usuario
    public function login($email, $senha) {
        $conexao = Conexao::getConexao();

        // buscar o usuario pelo email
        $stmt = $conexao->prepare("SELECT * FROM login WHERE Email = ?");
        $stmt->execute([$email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // verifica senha com hash
        if($usuario && password_verify($senha, $usuario['Senha'])){
            return [$usuario]; // mantém seu padrão de array
        }

        return [];
    }
    
    // buscar funcionario por id
    public function buscarPorId($id) {
        $conexao = Conexao::getConexao();
        
        $stmt = $conexao->prepare("SELECT * FROM login WHERE idUsuario = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // atualizar funcionario
    public function atualizar($id, $nome, $email, $senha = null) { // senha null para o usuario poder mudar
        $conexao = Conexao::getConexao();

        if ($senha) {
            // atualiza com senha
            $stmt = $conexao->prepare(
                "UPDATE login SET nomeUsuario = ?, Email = ?, Senha = ? WHERE idUsuario = ?"
            );
            $stmt->execute([$nome, $email, $senha, $id]);
        } else {
            // atualiza sem alter senha
            $stmt = $conexao->prepare(
                "UPDATE login SET nomeUsuario = ?, Email = ? WHERE idUsuario = ?"
            );
            $stmt->execute([$nome, $email, $id]);
        }

        return "ok";
    }
    
    // excluir a conta do usuario
    public function excluir($id) {
        $conexao = Conexao::getConexao();
        
        $stmt = $conexao->prepare("DELETE FROM login WHERE idUsuario = ?");
        $stmt->execute([$id]);

        return "ok";
    }
}
?>