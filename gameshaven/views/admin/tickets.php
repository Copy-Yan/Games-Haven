<?php $paginaAtual = 'admin_tickets'; $sucesso = mensagemFlash('sucesso'); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Tickets de Suporte</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Painel Administrativo</h1>
    <?php if ($sucesso): ?><div class="sucesso"><?= esc($sucesso) ?></div><?php endif; ?>

    <div class="admin-cards">
        <a href="index.php?page=admin_games" class="admin-card">
            Gerenciar todos os Jogos cadastrados
            <small>Ver catálogo completo</small>
        </a>
        <a href="index.php?page=admin_tickets" class="admin-card">
            Responder Tickets
            <small>Total <?= count($tickets) ?></small>
        </a>
    </div>

    <table class="tabela laranja">
        <thead>
            <tr><th>id do Ticket</th><th>Usuário</th><th>Assunto</th><th>Situação</th></tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td><a href="index.php?page=admin_ticket_reply&id=<?= $t['id_suporte'] ?>"><?= $t['id_suporte'] ?></a></td>
                <td>&#128100; <?= esc($t['nickname']) ?></td>
                <td><a href="index.php?page=admin_ticket_reply&id=<?= $t['id_suporte'] ?>"><?= esc($t['assunto']) ?></a></td>
                <td><?= esc($t['status']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$tickets): ?>
            <tr><td colspan="4" style="text-align:center;color:#888;">Nenhum ticket aberto no momento.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
