<?php
class Suporte {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    public function buscarPorUsuario($idUsuario) {
        $stmt = $this->db->prepare('SELECT * FROM suporte WHERE id_usuarios = ?');
        $stmt->execute([$idUsuario]);
        return $stmt->fetch();
    }

    public function abrirOuAtualizar($idUsuario, $assunto, $descricao, $categoria) {
        $existente = $this->buscarPorUsuario($idUsuario);
        if ($existente) {
            $stmt = $this->db->prepare(
                'UPDATE suporte SET assunto = ?, descricao = ?, categoria = ?, status = "Aberto" WHERE id_suporte = ?'
            );
            $stmt->execute([$assunto, $descricao, $categoria, $existente['id_suporte']]);
            return $existente['id_suporte'];
        }
        $stmt = $this->db->prepare(
            'INSERT INTO suporte (id_usuarios, assunto, descricao, categoria, status) VALUES (?, ?, ?, ?, "Aberto")'
        );
        $stmt->execute([$idUsuario, $assunto, $descricao, $categoria]);
        return $this->db->lastInsertId();
    }

    public function listarTodos() {
        $stmt = $this->db->query(
            'SELECT s.*, u.nickname FROM suporte s JOIN usuarios u ON u.id_usuarios = s.id_usuarios ORDER BY s.data_abertura DESC'
        );
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.nickname FROM suporte s JOIN usuarios u ON u.id_usuarios = s.id_usuarios WHERE s.id_suporte = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function responder($idSuporte, $idAdmin, $resposta) {
        $stmt = $this->db->prepare(
            'INSERT INTO suporte_respostas (id_suporte, id_usuarios, resposta) VALUES (?, ?, ?)'
        );
        $stmt->execute([$idSuporte, $idAdmin, $resposta]);
        $stmt = $this->db->prepare('UPDATE suporte SET status = "Resolvido", data_fechamento = NOW() WHERE id_suporte = ?');
        $stmt->execute([$idSuporte]);
    }

    public function respostas($idSuporte) {
        $stmt = $this->db->prepare('SELECT * FROM suporte_respostas WHERE id_suporte = ? ORDER BY data_resposta');
        $stmt->execute([$idSuporte]);
        return $stmt->fetchAll();
    }
}
