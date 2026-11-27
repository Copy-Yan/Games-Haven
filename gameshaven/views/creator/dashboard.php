<?php $paginaAtual = 'criador'; $sucesso = mensagemFlash('sucesso'); $erroFlash = mensagemFlash('erro'); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Painel do Criador</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/criador.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Painel do Criador</h1>
    <?php if ($sucesso): ?><div class="sucesso"><?= esc($sucesso) ?></div><?php endif; ?>
    <?php if ($erroFlash): ?><div class="erro"><?= esc($erroFlash) ?></div><?php endif; ?>

    <div class="criador-cards">
        <a href="index.php?page=criador_add_game" class="criador-card">
            Adicionar Jogo
            <small>Enviar um novo título para a loja</small>
        </a>
        <a href="index.php?page=criador_notas" class="criador-card-2">
            Minhas notas fiscais
            <small>Vendas e doações com análise e impressão</small>
        </a>
        <a href="index.php?page=criador_jogos_detalhes" class="criador-card-2">
            Detalhamento dos Jogos
            <small>Ver quem comprou ou doou</small>
        </a>
    </div>

    <table class="tabela">
        <thead>
            <tr><th>id</th><th>Ícone</th><th>Nome</th><th>Preço</th><th>Lançamento</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php foreach ($jogos as $jogo): ?>
            <tr>
                <td><?= $jogo['id_jogos'] ?></td>
                <td><?php if ($src = iconeOuCapa($jogo)): ?><img class="icone-mini" src="<?= esc($src) ?>" alt=""><?php else: ?><span class="capa-placeholder"></span><?php endif; ?></td>
                <td><?= esc($jogo['titulo']) ?></td>
                <td><?= formatarPreco($jogo['preco']) ?></td>
                <td><?= formatarData($jogo['data_lancamento']) ?></td>
                <td>
                    <a class="acao" href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>">Ver</a> |
                    <a class="acao" href="index.php?page=criador_remover_jogo&id=<?= $jogo['id_jogos'] ?>">Remover</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$jogos): ?>
            <tr><td colspan="7" style="text-align:center;color:#888;">Você ainda não enviou nenhum jogo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
