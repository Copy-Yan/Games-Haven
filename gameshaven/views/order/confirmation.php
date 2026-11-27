<?php $paginaAtual = 'confirmacao'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Pedido Confirmado</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/checkout.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <div style="display:flex; justify-content:flex-end;">
        <div class="progresso">
            <span class="ponto ativo"></span><span>Carrinho</span>
            <span class="linha"></span>
            <span class="ponto ativo"></span><span>Pagamento</span>
            <span class="linha"></span>
            <span class="ponto ativo"></span><span>Confirmação</span>
        </div>
    </div>

    <div class="confirmacao-box">
        <div class="check-circulo">&#10003;</div>
        <h2 style="color:var(--btn-orange); margin-bottom:2px;">PEDIDO REALIZADO<br>COM SUCESSO!</h2>
        <p style="font-size:13px; color:var(--text-muted);">Obrigado por comprar na GamesHaven.<br>Seu pedido foi registrado com sucesso.</p>

        <div class="numero-pedido">
            Número do Pedido<br>
            #<?= str_pad($pedido['id_pedidos'], 6, '0', STR_PAD_LEFT) ?>
        </div>

        <table class="tabela" style="text-align:left;">
            <thead><tr><th>Produto</th><th>Quantidade</th><th>Total</th></tr></thead>
            <tbody>
                <?php foreach ($pedido['itens'] as $item): ?>
                <tr>
                    <td><?= esc($item['titulo']) ?></td>
                    <td>1</td>
                    <td><?= formatarPreco($item['preco_unitario']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="text-align:right; font-weight:bold; color:var(--btn-orange); margin-top:10px;">TOTAL PAGO <?= formatarPreco($pedido['valor_total']) ?></p>

        <a href="index.php?page=home" class="btn" style="margin-top:20px; display:inline-block;">&#128722; IR PARA O INÍCIO</a>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
