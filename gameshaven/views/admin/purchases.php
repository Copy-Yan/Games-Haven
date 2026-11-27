<?php $paginaAtual = 'admin'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Compras Realizadas</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Compras Realizadas</h1>
    <p class="subtitulo"><a href="index.php?page=admin" style="text-decoration:underline;">&larr; Voltar ao painel</a></p>

    <table class="tabela">
        <thead>
            <tr><th>id</th><th>Comprador</th><th>E-mail</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><?= $p['id_pedidos'] ?></td>
                <td>&#128100; <?= esc($p['nickname']) ?></td>
                <td><?= esc($p['email']) ?></td>
                <td><?= formatarPreco($p['valor_total']) ?></td>
                <td><?= esc($p['metodo'] ?? '-') ?> <?= $p['status_pagamento'] ? '(' . esc($p['status_pagamento']) . ')' : '' ?></td>
                <td><?= esc($p['status']) ?></td>
                <td><?= formatarData($p['data_pedido']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$pedidos): ?>
            <tr><td colspan="7" style="text-align:center;color:#888;">Nenhuma compra registrada ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
