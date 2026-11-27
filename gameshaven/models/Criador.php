<?php
class Criador {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    public function buscarPorUsuario($idUsuario) {
        $stmt = $this->db->prepare('SELECT * FROM criadores WHERE id_usuarios = ? LIMIT 1');
        $stmt->execute([$idUsuario]);
        return $stmt->fetch();
    }

    public function total() {
        return (int) $this->db->query('SELECT COUNT(*) AS total FROM criadores')->fetch()['total'];
    }

    /** Lista todos os criadores cadastrados no site, com os dados do usuário, para o admin. */
    public function listarTodos() {
        $stmt = $this->db->query(
            'SELECT c.*, u.nickname, u.email, u.nome_usuario
             FROM criadores c
             JOIN usuarios u ON u.id_usuarios = c.id_usuarios
             ORDER BY c.data_cadastro DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Cadastra um usuário como criador. O schema não tinha uma coluna própria para
     * "chave pix", então ela é guardada junto do campo conta_bancaria.
     * id_doacoes fica NULL até que a primeira doação seja recebida (ver observação
     * no database.sql sobre a referência circular entre Criadores <-> doacoes).
     */
    public function cadastrar($idUsuario, $cpf, $telefone, $agenciaBancaria, $pix) {
        $contaCompleta = 'Agência: ' . $agenciaBancaria . ' | Pix: ' . $pix;
        $stmt = $this->db->prepare(
            'INSERT INTO criadores (id_usuarios, id_doacoes, CPF, conta_bancaria, telefone)
             VALUES (?, NULL, ?, ?, ?)'
        );
        $stmt->execute([$idUsuario, $cpf, $contaCompleta, $telefone]);
        return $this->db->lastInsertId();
    }
}
