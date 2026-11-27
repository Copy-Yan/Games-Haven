<?php $paginaAtual = 'carrinho'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Fazer Doação</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/checkout.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Fazer Doação</h1>
    <p class="subtitulo">Escolha o jogo (e o criador por trás dele) que você quer apoiar.</p>

    <table class="tabela">
        <thead><tr><th>Capa</th><th>Jogo</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($jogos as $jogo): ?>
            <tr>
                <td><span class="capa-placeholder"></span></td>
                <td><?= esc($jogo['titulo']) ?></td>
                <td><a href="index.php?page=doacao_jogo&id=<?= $jogo['id_jogos'] ?>" class="btn laranja-outline">&#128176; Doar</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$jogos): ?>
            <tr><td colspan="3" style="text-align:center;color:#888;">Nenhum jogo com criador cadastrado ainda para receber doações.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
