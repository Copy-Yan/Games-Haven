<?php $paginaAtual = 'admin'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Doações</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Doações Feitas</h1>
    <p class="subtitulo"><a href="index.php?page=admin" style="text-decoration:underline;">&larr; Voltar ao painel</a></p>

    <table class="tabela">
        <thead>
            <tr><th>id</th><th>Usuário</th><th>Jogo</th><th>Valor</th><th>Método</th><th>Status</th><th>Data</th></tr>
        </thead>
        <tbody>
            <?php foreach ($doacoes as $d): ?>
            <tr>
                <td><?= $d['id_doacoes'] ?></td>
                <td><?= esc($d['nickname']) ?></td>
                <td><?= esc($d['titulo']) ?></td>
                <td><?= formatarPreco($d['valor']) ?></td>
                <td><?= esc($d['metodo']) ?></td>
                <td><?= esc($d['status']) ?></td>
                <td><?= formatarData($d['data_doacao']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$doacoes): ?>
            <tr><td colspan="7" style="text-align:center;color:#888;">Nenhuma doação registrada ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
