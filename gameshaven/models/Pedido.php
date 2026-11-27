<?php
class Pedido {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    /**
     * Cria um novo pedido com os itens vindos do carrinho e registra o pagamento.
     */
    public function fecharPedido($idUsuario, array $itensCarrinho, $metodoPagamento) {
        $this->db->beginTransaction();
        try {
            $total = 0;
            foreach ($itensCarrinho as $item) {
                $total += $item['preco'] * $item['quantidade'];
            }

            // Cada compra gera um pedido novo, preservando o histórico completo.
            $stmt = $this->db->prepare(
                'INSERT INTO pedidos (id_usuarios, data_pedido, valor_total, status) VALUES (?, CURDATE(), ?, "pago")'
            );
            $stmt->execute([$idUsuario, $total]);
            $idPedido = $this->db->lastInsertId();

            $stmtItem = $this->db->prepare('INSERT INTO pedido_itens (id_pedidos, id_jogos, preco_unitario, quantidade) VALUES (?, ?, ?, ?)');
            $stmtBiblioteca = $this->db->prepare(
                'INSERT INTO biblioteca (id_usuarios, id_jogos, data_compra) VALUES (?, ?, CURDATE())
                 ON DUPLICATE KEY UPDATE data_compra = CURDATE()'
            );
            foreach ($itensCarrinho as $item) {
                $stmtItem->execute([$idPedido, $item['id_jogos'], $item['preco'], max(1, (int) $item['quantidade'])]);
                $stmtBiblioteca->execute([$idUsuario, $item['id_jogos']]);
            }

            $stmt = $this->db->prepare('SELECT id_pagamentos FROM pagamentos WHERE id_pedidos = ?');
            $stmt->execute([$idPedido]);
            $pagamentoExistente = $stmt->fetch();
            if ($pagamentoExistente) {
                $stmt = $this->db->prepare('UPDATE pagamentos SET data_pagamento = CURDATE(), metodo = ?, status = "aprovado" WHERE id_pedidos = ?');
                $stmt->execute([$metodoPagamento, $idPedido]);
            } else {
                $stmt = $this->db->prepare('INSERT INTO pagamentos (id_pedidos, data_pagamento, metodo, status) VALUES (?, CURDATE(), ?, "aprovado")');
                $stmt->execute([$idPedido, $metodoPagamento]);
            }

            $this->db->commit();
            return $idPedido;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Lista as compras de jogos de um criador específico (um item comprado = uma
     * linha), com quem comprou e por qual forma de pagamento - para o Painel do
     * Criador em "Detalhamento dos Jogos".
     */
    public function listarCompradoresPorCriador($idCriador) {
        $stmt = $this->db->prepare(
            'SELECT pi.id_pedidoItens, p.id_pedidos, p.data_pedido, pi.preco_unitario,
                    j.id_jogos, j.titulo AS jogo, u.nickname, u.email,
                    pg.metodo, pg.status AS status_pagamento
             FROM pedido_itens pi
             JOIN pedidos p ON p.id_pedidos = pi.id_pedidos
             JOIN jogos j ON j.id_jogos = pi.id_jogos
             JOIN jogos_criadores jc ON jc.id_jogos = pi.id_jogos
             JOIN usuarios u ON u.id_usuarios = p.id_usuarios
             LEFT JOIN pagamentos pg ON pg.id_pedidos = p.id_pedidos
             WHERE jc.id_criadores = ?
             ORDER BY p.data_pedido DESC'
        );
        $stmt->execute([$idCriador]);
        return $stmt->fetchAll();
    }

    /** Confere se algum item deste pedido é de um jogo deste criador, antes de liberar a nota fiscal. */
    public function pertenceAoCriador($idPedido, $idCriador) {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM pedido_itens pi
             JOIN jogos_criadores jc ON jc.id_jogos = pi.id_jogos
             WHERE pi.id_pedidos = ? AND jc.id_criadores = ? LIMIT 1'
        );
        $stmt->execute([$idPedido, $idCriador]);
        return (bool) $stmt->fetch();
    }

    public function total() {
        return (int) $this->db->query('SELECT COUNT(*) AS total FROM pedidos')->fetch()['total'];
    }

    /** Lista todos os pedidos do site com dados do comprador, para o admin. */
    public function listarTodos() {
        $stmt = $this->db->query(
            'SELECT p.*, u.nickname, u.email, pg.metodo, pg.status AS status_pagamento
             FROM pedidos p
             JOIN usuarios u ON u.id_usuarios = p.id_usuarios
             LEFT JOIN pagamentos pg ON pg.id_pedidos = p.id_pedidos
             ORDER BY p.data_pedido DESC'
        );
        return $stmt->fetchAll();
    }

    public function buscarComItens($idPedido) {
        $stmt = $this->db->prepare('SELECT * FROM pedidos WHERE id_pedidos = ?');
        $stmt->execute([$idPedido]);
        $pedido = $stmt->fetch();
        if (!$pedido) return null;

        $stmt = $this->db->prepare(
            'SELECT pi.*, j.titulo FROM pedido_itens pi JOIN jogos j ON j.id_jogos = pi.id_jogos WHERE pi.id_pedidos = ?'
        );
        $stmt->execute([$idPedido]);
        $pedido['itens'] = $stmt->fetchAll();
        return $pedido;
    }
}
