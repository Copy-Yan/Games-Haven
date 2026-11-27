<?php
class Carrinho {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    private function idCarrinho($idUsuario) {
        $stmt = $this->db->prepare('SELECT id_carrinho FROM carrinho WHERE id_usuarios = ?');
        $stmt->execute([$idUsuario]);
        $linha = $stmt->fetch();
        if ($linha) return $linha['id_carrinho'];
        $stmt = $this->db->prepare('INSERT INTO carrinho (id_usuarios) VALUES (?)');
        $stmt->execute([$idUsuario]);
        return $this->db->lastInsertId();
    }

    public function itens($idUsuario) {
        $idCarrinho = $this->idCarrinho($idUsuario);
        $stmt = $this->db->prepare(
            'SELECT ci.id_carrinhoItens, ci.quantidade, j.id_jogos, j.titulo, j.preco, j.capa, j.icone, j.classificacao_etaria
             FROM carrinho_itens ci
             JOIN jogos j ON j.id_jogos = ci.id_jogos
             WHERE ci.id_carrinho = ?'
        );
        $stmt->execute([$idCarrinho]);
        return $stmt->fetchAll();
    }

    public function adicionar($idUsuario, $idJogo) {
        $idCarrinho = $this->idCarrinho($idUsuario);
        $stmt = $this->db->prepare('SELECT id_carrinhoItens, quantidade FROM carrinho_itens WHERE id_carrinho = ? AND id_jogos = ?');
        $stmt->execute([$idCarrinho, $idJogo]);
        $linha = $stmt->fetch();
        if ($linha) {
            $stmt = $this->db->prepare('UPDATE carrinho_itens SET quantidade = quantidade + 1 WHERE id_carrinhoItens = ?');
            $stmt->execute([$linha['id_carrinhoItens']]);
        } else {
            $stmt = $this->db->prepare('INSERT INTO carrinho_itens (id_carrinho, id_jogos, quantidade) VALUES (?, ?, 1)');
            $stmt->execute([$idCarrinho, $idJogo]);
        }
    }

    public function alterarQuantidade($idItem, $delta) {
        $stmt = $this->db->prepare('SELECT quantidade FROM carrinho_itens WHERE id_carrinhoItens = ?');
        $stmt->execute([$idItem]);
        $linha = $stmt->fetch();
        if (!$linha) return;
        $nova = max(1, $linha['quantidade'] + $delta);
        $stmt = $this->db->prepare('UPDATE carrinho_itens SET quantidade = ? WHERE id_carrinhoItens = ?');
        $stmt->execute([$nova, $idItem]);
    }

    public function remover($idItem) {
        $stmt = $this->db->prepare('DELETE FROM carrinho_itens WHERE id_carrinhoItens = ?');
        $stmt->execute([$idItem]);
    }

    public function esvaziar($idUsuario) {
        $idCarrinho = $this->idCarrinho($idUsuario);
        $stmt = $this->db->prepare('DELETE FROM carrinho_itens WHERE id_carrinho = ?');
        $stmt->execute([$idCarrinho]);
    }

    public function total($idUsuario) {
        $total = 0;
        foreach ($this->itens($idUsuario) as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
        return $total;
    }
}
