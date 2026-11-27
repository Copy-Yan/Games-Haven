<?php
class Avaliacao {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    public function listarPorJogo($idJogo) {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.nickname FROM avaliacoes a JOIN usuarios u ON u.id_usuarios = a.id_usuarios
             WHERE a.id_jogos = ? ORDER BY a.data_avaliacao DESC'
        );
        $stmt->execute([$idJogo]);
        return $stmt->fetchAll();
    }

    public function adicionar($idUsuario, $idJogo, $nota, $comentario) {
        $stmt = $this->db->prepare(
            'INSERT INTO avaliacoes (id_usuarios, id_jogos, nota, comentario, data_avaliacao)
             VALUES (?, ?, ?, ?, CURDATE())
             ON DUPLICATE KEY UPDATE nota = VALUES(nota), comentario = VALUES(comentario), data_avaliacao = CURDATE()'
        );
        $stmt->execute([$idUsuario, $idJogo, $nota, $comentario]);
    }
}
