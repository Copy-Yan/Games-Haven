<?php
class Usuario {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    public function buscarPorEmailOuUsuario($login) {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = ? OR nome_usuario = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        return $stmt->fetch();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id_usuarios = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function emailExiste($email) {
        $stmt = $this->db->prepare('SELECT id_usuarios FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function usuarioExiste($nomeUsuario) {
        $stmt = $this->db->prepare('SELECT id_usuarios FROM usuarios WHERE nome_usuario = ?');
        $stmt->execute([$nomeUsuario]);
        return (bool) $stmt->fetch();
    }

    public function criar($dados) {
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nome_usuario, nickname, email, senha, data_nascimento)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $dados['nome_usuario'],
            $dados['nickname'],
            $dados['email'],
            password_hash($dados['senha'], PASSWORD_DEFAULT),
            $dados['data_nascimento'],
        ]);
        return $this->db->lastInsertId();
    }

    public function atualizarPerfil($id, $dados) {
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET nome_usuario = ?, nickname = ?, email = ?, data_nascimento = ? WHERE id_usuarios = ?'
        );
        $stmt->execute([
            $dados['nome_usuario'], $dados['nickname'], $dados['email'], $dados['data_nascimento'], $id,
        ]);
    }

    public function atualizarSenha($id, $novaSenha) {
        $stmt = $this->db->prepare('UPDATE usuarios SET senha = ? WHERE id_usuarios = ?');
        $stmt->execute([password_hash($novaSenha, PASSWORD_DEFAULT), $id]);
    }

    /** Verifica se outro usuário (diferente de $id) já usa este nome de usuário, nickname ou e-mail. */
    public function campoEmUsoPorOutro($id, $campo, $valor) {
        $permitidos = ['nome_usuario', 'nickname', 'email'];
        if (!in_array($campo, $permitidos, true)) return false;
        $stmt = $this->db->prepare("SELECT id_usuarios FROM usuarios WHERE $campo = ? AND id_usuarios <> ?");
        $stmt->execute([$valor, $id]);
        return (bool) $stmt->fetch();
    }

    /** Grava o caminho da foto de perfil (ou null para remover). */
    public function atualizarFotoPerfil($id, $caminho) {
        $stmt = $this->db->prepare('UPDATE usuarios SET foto_perfil = ? WHERE id_usuarios = ?');
        $stmt->execute([$caminho, $id]);
    }
}
