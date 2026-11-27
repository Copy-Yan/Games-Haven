<?php
require_once __DIR__ . '/../models/Carrinho.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Endereco.php';
require_once __DIR__ . '/../models/NotaFiscal.php';
require_once __DIR__ . '/../includes/endereco.php';

function handlePayment() {
    exigirLogin();
    $carrinhoModelo = new Carrinho();
    $itens = $carrinhoModelo->itens($_SESSION['usuario_id']);
    if (!$itens) redirecionar('carrinho');
    foreach ($itens as $item) {
        if (!jogoPermitidoParaUsuario($item)) {
            mensagemFlash('erro', 'Remova do carrinho os jogos que não estão disponíveis para a sua faixa etária.');
            redirecionar('carrinho');
        }
    }
    $total = $carrinhoModelo->total($_SESSION['usuario_id']);
    $erro = null;
    $metodo = $_GET['metodo'] ?? $_POST['metodo'] ?? 'cartao credito';
    $usuario = usuarioLogado();
    $enderecoModelo = new Endereco();
    $endereco = $enderecoModelo->buscarPorUsuario($_SESSION['usuario_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $metodo = $_POST['metodo'] ?? 'cartao credito';
        $nomeComprador = trim($_POST['nome_completo'] ?? '');
        $emailComprador = trim($_POST['email'] ?? '');
        [$enderecoPost, $erroEndereco] = lerEnderecoPost();
        $endereco = $enderecoPost;

        if ($nomeComprador === '' || $emailComprador === '') {
            $erro = 'Preencha nome e e-mail do comprador.';
        } elseif ($erroEndereco !== null) {
            $erro = $erroEndereco;
        } elseif (($erroPagamento = validarDadosPagamento($metodo)) !== null) {
            $erro = $erroPagamento;
        } else {
            $enderecoModelo->salvar($_SESSION['usuario_id'], $enderecoPost);
            $pedidoModelo = new Pedido();
            $idPedido = $pedidoModelo->fecharPedido($_SESSION['usuario_id'], $itens, $metodo);
            // Uma nota por criador envolvido no pedido, emitida já na compra.
            (new NotaFiscal())->emitirParaPedido($idPedido);
            $carrinhoModelo->esvaziar($_SESSION['usuario_id']);
            redirecionar('confirmacao', ['id' => $idPedido]);
        }
    }

    require __DIR__ . '/../views/payment/index.php';
}

function handleOrderConfirmation() {
    exigirLogin();
    $id = (int) ($_GET['id'] ?? 0);
    $pedidoModelo = new Pedido();
    $pedido = $pedidoModelo->buscarComItens($id);
    if (!$pedido) redirecionar('home');
    require __DIR__ . '/../views/order/confirmation.php';
}
