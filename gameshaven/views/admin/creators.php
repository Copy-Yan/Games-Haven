<?php $paginaAtual = 'admin'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Criadores Cadastrados</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Criadores Cadastrados</h1>
    <p class="subtitulo"><a href="index.php?page=admin" style="text-decoration:underline;">&larr; Voltar ao painel</a></p>

    <table class="tabela">
        <thead>
            <tr><th>id</th><th>Criador</th><th>E-mail</th><th>CPF</th><th>Telefone</th><th>Conta/Pix</th><th>Cadastro</th></tr>
        </thead>
        <tbody>
            <?php foreach ($criadores as $c): ?>
            <tr>
                <td><?= $c['id_criadores'] ?></td>
                <td>&#128100; <?= esc($c['nickname']) ?></td>
                <td><?= esc($c['email']) ?></td>
                <td><?= esc($c['CPF']) ?></td>
                <td><?= esc($c['telefone']) ?></td>
                <td><?= esc($c['conta_bancaria']) ?></td>
                <td><?= formatarData($c['data_cadastro']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$criadores): ?>
            <tr><td colspan="7" style="text-align:center;color:#888;">Nenhum criador cadastrado ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
