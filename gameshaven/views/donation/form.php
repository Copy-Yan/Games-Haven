<?php $paginaAtual = 'carrinho'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Doação</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/checkout.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">DOAÇÃO &#128179;</h1>
    <p class="subtitulo"><a href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>" style="text-decoration:underline;">&larr; Voltar para <?= esc($jogo['titulo']) ?></a></p>

    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>

    <form method="post" action="index.php?page=doacao_jogo&id=<?= $jogo['id_jogos'] ?>" class="checkout-form">
        <input type="hidden" name="metodo" value="<?= esc($metodo) ?>">

        <div class="checkout-linha">
            <div class="painel checkout-painel">
                <h3 style="margin-top:0;">&#128100; Dados do Doador</h3>
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
                <a href="index.php?page=doacao_jogo&id=<?= $jogo['id_jogos'] ?>&metodo=cartao+credito" class="metodo-opcao <?= $metodo === 'cartao credito' ? 'selecionado' : '' ?>">Cartão de Crédito</a>
                <a href="index.php?page=doacao_jogo&id=<?= $jogo['id_jogos'] ?>&metodo=cartao+debito" class="metodo-opcao <?= $metodo === 'cartao debito' ? 'selecionado' : '' ?>">Cartão de Débito</a>
                <a href="index.php?page=doacao_jogo&id=<?= $jogo['id_jogos'] ?>&metodo=pix" class="metodo-opcao <?= $metodo === 'pix' ? 'selecionado' : '' ?>">PIX</a>
            </div>

            <?php if ($metodo === 'pix'): ?>
                <h4>Informações do Pix</h4>
                <label>Chave Pix</label>
                <input type="text" value="pix@gameshaven.com" disabled>
                <label>Nome do Cliente</label>
                <input type="text" name="nome_pix" value="<?= esc($usuario['nickname'] ?? '') ?>">
                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label>Valor</label>
                        <input type="text" value="<?= esc($valorPadrao) ?>" disabled>
                    </div>
                    <div style="flex:1;">
                        <label>Tipo de chave</label>
                        <select><option>E-mail</option></select>
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
            <h3>&#128176; Resumo da Doação</h3>
            <p style="margin:0 16px 4px; text-align:left;">Jogo: <strong><?= esc($jogo['titulo']) ?></strong></p>
            <label style="margin:10px 16px 0; display:block; text-align:left;">Valor da doação (R$)</label>
            <input type="text" name="valor" value="<?= esc($valorPadrao) ?>" class="checkout-campo-valor" required>
            <p style="margin:6px 0 16px; font-size:12px; color:var(--text-muted);">Você pode alterar o valor antes de finalizar.</p>
        </div>

        <div class="checkout-acoes">
            <a href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>" class="btn outline">&larr; CANCELAR</a>
            <button type="submit" class="btn">&#128274; FINALIZAR DOAÇÃO</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
