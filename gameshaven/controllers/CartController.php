<?php
require_once __DIR__ . '/../models/Carrinho.php';

function handleCart() {
    exigirLogin();
    $carrinhoModelo = new Carrinho();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao'])) {
        $idItem = (int) ($_GET['item'] ?? 0);
        if ($_GET['acao'] === 'mais') $carrinhoModelo->alterarQuantidade($idItem, 1);
        if ($_GET['acao'] === 'menos') $carrinhoModelo->alterarQuantidade($idItem, -1);
        if ($_GET['acao'] === 'remover') $carrinhoModelo->remover($idItem);
        redirecionar('carrinho');
    }

    $itens = $carrinhoModelo->itens($_SESSION['usuario_id']);
    $total = $carrinhoModelo->total($_SESSION['usuario_id']);
    require __DIR__ . '/../views/cart/index.php';
}
