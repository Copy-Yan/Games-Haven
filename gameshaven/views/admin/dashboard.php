<?php $paginaAtual = 'admin'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Painel Administrativo</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Painel Administrativo</h1>

    <div class="admin-cards">
        <a href="index.php?page=admin_donations" class="admin-card">
            Ver doações feitas
            <small>Total <?= (int) $totalDoacoes ?></small>
        </a>
        <a href="index.php?page=admin_games" class="admin-card">
            Gerenciar todos os Jogos cadastrados
            <small>Total <?= count($jogos) ?></small>
        </a>
        <a href="index.php?page=admin_add_game" class="admin-card">
            Adicionar Jogos
            <small>Adicionar novo título</small>
        </a>
        <a href="index.php?page=admin_notas" class="admin-card">
            Notas fiscais
            <small>Análise, filtros e impressão</small>
        </a>
        <a href="index.php?page=admin_purchases" class="admin-card">
            Ver todas as compras
            <small>Total <?= (int) $totalCompras ?></small>
        </a>
        <a href="index.php?page=admin_creators" class="admin-card">
            Criadores cadastrados
            <small>Total <?= (int) $totalCriadores ?></small>
        </a>
    </div>

    <table class="tabela">
        <thead>
            <tr><th>id</th><th>Ícone</th><th>Nome</th><th>Preço</th><th>Tag</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php foreach ($jogos as $jogo): ?>
            <tr>
                <td><?= $jogo['id_jogos'] ?></td>
                <td><?php if ($src = iconeOuCapa($jogo)): ?><img class="icone-mini" src="<?= esc($src) ?>" alt=""><?php else: ?><span class="capa-placeholder"></span><?php endif; ?></td>
                <td><?= esc($jogo['titulo']) ?></td>
                <td><?= formatarPreco($jogo['preco']) ?></td>
                <td>X</td>
                <td>
                    <a class="acao" href="index.php?page=admin_edit_game&id=<?= $jogo['id_jogos'] ?>">Editar</a>|
                    <a class="acao" href="index.php?page=admin_remove_game&id=<?= $jogo['id_jogos'] ?>">Remover</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$jogos): ?>
            <tr><td colspan="6" style="text-align:center;color:#888;">Nenhum jogo cadastrado ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
