<?php $paginaAtual = 'carrinho'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Meu Carrinho</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/checkout.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <?php $erroFlash = mensagemFlash('erro'); if ($erroFlash): ?><div class="erro"><?= esc($erroFlash) ?></div><?php endif; ?>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1 class="titulo-pagina">Meu Carrinho</h1>
        <div class="progresso">
            <span class="ponto ativo"></span><span>Carrinho</span>
            <span class="linha"></span>
            <span class="ponto"></span><span>Pagamento</span>
            <span class="linha"></span>
            <span class="ponto"></span><span>Confirmação</span>
        </div>
    </div>

    <table class="carrinho">
        <thead>
            <tr><th>Produto</th><th>Preço</th><th>Quantidade</th><th>Total</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
            <tr>
                <td class="produto-cel">
                    <span class="foto"><?php if ($src = iconeOuCapa($item)): ?><img src="<?= esc($src) ?>" alt=""><?php else: ?>Sem ícone<?php endif; ?></span>
                    <span><?= esc($item['titulo']) ?></span>
                </td>
                <td><?= formatarPreco($item['preco']) ?></td>
                <td>
                    <div class="qtd-form">
                        <a href="index.php?page=carrinho&acao=menos&item=<?= $item['id_carrinhoItens'] ?>"><button type="button">&minus;</button></a>
                        <span><?= (int) $item['quantidade'] ?></span>
                        <a href="index.php?page=carrinho&acao=mais&item=<?= $item['id_carrinhoItens'] ?>"><button type="button">+</button></a>
                    </div>
                </td>
                <td><?= formatarPreco($item['preco'] * $item['quantidade']) ?></td>
                <td><a href="index.php?page=carrinho&acao=remover&item=<?= $item['id_carrinhoItens'] ?>" style="color:#c0392b; font-size:12px;">remover</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$itens): ?>
            <tr><td colspan="5" style="text-align:center; color:#888;">Seu carrinho está vazio.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total-linha">
        <div class="caixa">
            <span>TOTAL</span>
            <span><?= formatarPreco($total) ?></span>
        </div>
    </div>

    <div class="acoes-carrinho">
        <a href="index.php?page=home" class="btn-checkout btn-checkout-contorno">ESCOLHER MAIS PRODUTOS</a>
        <div class="acoes-carrinho-direita">
            <?php if ($itens): ?>
            <a href="index.php?page=pagamento" class="btn-checkout btn-checkout-pagar">&#128722; Pagar Normalmente</a>
            <?php endif; ?>
            <a href="index.php?page=doacao" class="btn-checkout btn-checkout-doacao">&#128176; FAZER DOAÇÃO</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
