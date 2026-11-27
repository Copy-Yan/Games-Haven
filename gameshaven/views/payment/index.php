<?php $paginaAtual = 'pagamento'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Pagamento</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/checkout.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <h1 class="titulo-pagina">PAGAMENTO &#128179;</h1>
        <div class="progresso">
            <span class="ponto"></span><span>Carrinho</span>
            <span class="linha"></span>
            <span class="ponto ativo"></span><span>Pagamento</span>
            <span class="linha"></span>
            <span class="ponto"></span><span>Confirmação</span>
        </div>
    </div>

    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>

    <form method="post" action="index.php?page=pagamento" class="checkout-form">
        <input type="hidden" name="metodo" value="<?= esc($metodo) ?>">

        <div class="checkout-linha">
            <div class="painel checkout-painel" id="input-checkout">
                <h3 style="margin-top:0;">&#128100; Dados do Comprador</h3>
                <label>Nome Completo</label>
                <input type="text" name="nome_completo" value="<?= esc($usuario['nickname'] ?? '') ?>" required>
                <label>E-mail</label>
                <input type="email" name="email" value="<?= esc($usuario['email'] ?? '') ?>" required>
                <label>Telefone</label>
                <input type="tel" name="telefone">
            </div>

            <?php require __DIR__ . '/../partials/_form_endereco.php'; ?>
        </div>

        <div class="painel checkout-painel checkout-largura-total">
            <h3 style="margin-top:0;">&#128179; Forma de Pagamento</h3>
            <div class="metodos-pagamento">
                <a href="index.php?page=pagamento&metodo=cartao+credito" class="metodo-opcao <?= $metodo === 'cartao credito' ? 'selecionado' : '' ?>">Cartão de Crédito</a>
                <a href="index.php?page=pagamento&metodo=cartao+debito" class="metodo-opcao <?= $metodo === 'cartao debito' ? 'selecionado' : '' ?>">Cartão de Débito</a>
                <a href="index.php?page=pagamento&metodo=pix" class="metodo-opcao <?= $metodo === 'pix' ? 'selecionado' : '' ?>">PIX</a>
            </div>

            <?php if ($metodo === 'pix'): ?>
                <h4>Informações do Pix</h4>
                <label>Chave Pix</label>
                <input type="text"  value="pix@gameshaven.com" disabled>
                <label>Nome do Cliente</label>
                <input type="text" name="nome_pix" value="<?= esc($usuario['nickname'] ?? '') ?>" >
                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label>Valor</label>
                        <input type="text" value="<?= formatarPreco($total) ?>" disabled>
                    </div>
                    <div style="flex:1;">
                        <label>Tipo de chave</label>
                        <select id="input-checkout2"><option>E-mail</option></select>
                    </div>
                </div>
                <div class="qrcode-caixa">
                    &#9635;&#9635;&#9635;&#9635;&#9635;<br>
                    ESCANEIE O QR CODE<br>
                    Abra o app do seu banco, escaneie e pague.<br>
                    O pagamento é aprovado em poucos segundos!
                </div>
            <?php else: ?>
                <h4>Informações do Cartão</h4>
                <label>Número do cartão</label>
                <input type="text" name="numero_cartao" placeholder="---- ---- ---- ----" maxlength="19" required>
                <div style="display:flex; gap:16px;">
                    <div style="flex:2;">
                        <label>Nome do Cartão</label>
                        <input type="text" name="nome_cartao" required>
                    </div>
                    <div style="flex:1;">
                        <label>Validade</label>
                        <input type="text" name="validade" placeholder="MM/AAAA" maxlength="7" required>
                    </div>
                    <div style="flex:1;">
                        <label>CVV</label>
                        <input type="text" name="cvv" maxlength="4" required>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="resumo-pedido checkout-resumo">
            <h3>&#128722; Resumo do Pedido</h3>
            <table>
                <thead><tr><th>Produto</th><th>QTD</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= esc($item['titulo']) ?></td>
                        <td><?= (int) $item['quantidade'] ?></td>
                        <td><?= formatarPreco($item['preco'] * $item['quantidade']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="checkout-total">TOTAL <?= formatarPreco($total) ?></p>
        </div>

        <div class="checkout-acoes">
            <a href="index.php?page=carrinho" class="btn outline">&larr; VOLTAR AO CARRINHO</a>
            <button type="submit" class="btn">&#128274; FINALIZAR PAGAMENTO</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
