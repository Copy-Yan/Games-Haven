<?php $paginaAtual = 'criador'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Detalhamento dos Jogos</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/criador.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Detalhamento dos Jogos</h1>
    <p class="subtitulo"><a href="index.php?page=criador" style="text-decoration:underline;">&larr; Voltar ao Painel do Criador</a></p>

    <div class="secao-titulo">Compras</div>
    <table class="tabela">
        <thead>
            <tr><th>Jogo</th><th>Comprador</th><th>Valor</th><th>Pagamento</th><th>Data</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($compras as $c): ?>
            <tr>
                <td><?= esc($c['jogo']) ?></td>
                <td>&#128100; <?= esc($c['nickname']) ?> <small style="color:var(--text-muted);">(<?= esc($c['email']) ?>)</small></td>
                <td><?= formatarPreco($c['preco_unitario']) ?></td>
                <td><?= esc($c['metodo'] ?? '-') ?></td>
                <td><?= formatarData($c['data_pedido']) ?></td>
                <td><a class="acao" href="index.php?page=criador_nota_fiscal&tipo=pedido&id=<?= $c['id_pedidos'] ?>">Ver Nota Fiscal</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$compras): ?>
            <tr><td colspan="6" style="text-align:center;color:#888;">Nenhuma compra registrada ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="secao-titulo" style="margin-top:30px;">Doações</div>
    <table class="tabela">
        <thead>
            <tr><th>Jogo</th><th>Doador</th><th>Valor</th><th>Pagamento</th><th>Data</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($doacoes as $d): ?>
            <tr>
                <td><?= esc($d['jogo']) ?></td>
                <td>&#128100; <?= esc($d['nickname']) ?> <small style="color:var(--text-muted);">(<?= esc($d['email']) ?>)</small></td>
                <td><?= formatarPreco($d['valor']) ?></td>
                <td><?= esc($d['metodo']) ?></td>
                <td><?= formatarData($d['data_doacao']) ?></td>
                <td><a class="acao" href="index.php?page=criador_nota_fiscal&tipo=doacao&id=<?= $d['id_doacoes'] ?>">Ver Nota Fiscal</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$doacoes): ?>
            <tr><td colspan="6" style="text-align:center;color:#888;">Nenhuma doação recebida ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
