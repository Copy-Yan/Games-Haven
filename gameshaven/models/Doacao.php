<?php
class Doacao {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    public function registrar($idUsuario, $idJogo, $valor, $metodo) {
        // Descobre um criador vinculado ao jogo (jogos_criadores); se não houver, usa NULL/0 não é permitido
        // pelo schema (id_criadores NOT NULL), então exige que o jogo tenha ao menos um criador cadastrado.
        $stmt = $this->db->prepare(
            'SELECT id_criadores FROM jogos_criadores WHERE id_jogos = ? LIMIT 1'
        );
        $stmt->execute([$idJogo]);
        $linha = $stmt->fetch();
        if (!$linha) {
            throw new Exception('Este jogo ainda não possui um criador cadastrado para receber doações.');
        }
        $idCriador = $linha['id_criadores'];

        $stmt = $this->db->prepare(
            'INSERT INTO doacoes (id_criadores, id_usuarios, id_jogos, valor, metodo, status)
             VALUES (?, ?, ?, ?, ?, "aprovado")'
        );
        $stmt->execute([$idCriador, $idUsuario, $idJogo, $valor, $metodo]);
        return $this->db->lastInsertId();
    }

    /** Lista as doações recebidas por um criador específico, para o Painel do Criador. */
    public function listarPorCriador($idCriador) {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.nickname, u.email, j.titulo AS jogo
             FROM doacoes d
             JOIN usuarios u ON u.id_usuarios = d.id_usuarios
             JOIN jogos j ON j.id_jogos = d.id_jogos
             WHERE d.id_criadores = ?
             ORDER BY d.data_doacao DESC'
        );
        $stmt->execute([$idCriador]);
        return $stmt->fetchAll();
    }

    /** Confere se esta doação foi feita para este criador, antes de liberar a nota fiscal. */
    public function pertenceAoCriador($idDoacao, $idCriador) {
        $stmt = $this->db->prepare('SELECT 1 FROM doacoes WHERE id_doacoes = ? AND id_criadores = ?');
        $stmt->execute([$idDoacao, $idCriador]);
        return (bool) $stmt->fetch();
    }

    public function listarTodas() {
        $stmt = $this->db->query(
            'SELECT d.*, u.nickname, j.titulo FROM doacoes d
             JOIN usuarios u ON u.id_usuarios = d.id_usuarios
             JOIN jogos j ON j.id_jogos = d.id_jogos
             ORDER BY d.data_doacao DESC'
        );
        return $stmt->fetchAll();
    }

    public function total() {
        return (int) $this->db->query('SELECT COUNT(*) AS total FROM doacoes')->fetch()['total'];
    }
}
