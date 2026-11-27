<?php $paginaAtual = 'jogo'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - <?= esc($jogo['titulo']) ?></title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/jogo.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="sucesso"><?= esc($sucesso) ?></div><?php endif; ?>

    <div class="detalhe-jogo">
        <div class="detalhe-capa"><?php if (imagemExiste($jogo['capa'])): ?><img src="<?= esc($jogo['capa']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;"><?php else: ?>Imagem do Jogo<?php endif; ?></div>
        <div class="detalhe-info">
            <h2><?= esc($jogo['titulo']) ?></h2>
            <p style="color:var(--text-muted); font-size:13px;">Detalhes e informações do jogo</p>
            <p style="font-size:13px;"><?= nl2br(esc($jogo['descricao'])) ?></p>
            <p style="font-size:12px; color:var(--text-muted);">
                Desenvolvedora: <?= esc($jogo['desenvolvedora'] ?? '-') ?> &middot;
                Publicadora: <?= esc($jogo['publicadora'] ?? '-') ?> &middot;
                Lançamento: <?= formatarData($jogo['data_lancamento']) ?> &middot;
                Classificação: <?= esc($jogo['classificacao_etaria']) ?>
            </p>

            <div class="detalhe-acoes">
                <span class="preco-tag"><?= formatarPreco($jogo['preco']) ?></span>

                <div style="display:flex; gap:10px;">
                    <a href="index.php?page=doacao_jogo&id=<?= $jogo['id_jogos'] ?>" class="btn azul">Doar</a>
                    <form method="post" action="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>">
                        <button type="submit" name="acao" value="carrinho" class="btn outline">Adicionar ao Carrinho</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 style="margin-top:26px;">Avaliações</h3>
    <div class="avaliacoes-caixa">
        <?php if ($avaliacoes): foreach ($avaliacoes as $av): ?>
            <div class="avaliacao-item">
                <strong><?= esc($av['nickname']) ?></strong> &mdash; Nota <?= (int) $av['nota'] ?>/5<br>
                <?= esc($av['comentario']) ?>
            </div>
        <?php endforeach; else: ?>
            <p style="font-size:13px;">Ainda não há avaliações para este jogo.</p>
        <?php endif; ?>
    </div>

    <?php if (estaLogado()): ?>
    <form method="post" action="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>" style="margin-top:16px; max-width:400px;">
        <input type="hidden" name="acao" value="avaliar">
        <label style="font-size:13px;">Sua nota (0 a 5)</label>
        <select name="nota" style="width:100%; padding:8px; border-radius:6px; margin-bottom:10px;">
            <?php for ($n = 5; $n >= 0; $n--): ?><option value="<?= $n ?>"><?= $n ?></option><?php endfor; ?>
        </select>
        <textarea name="comentario" placeholder="Escreva sua avaliação" rows="3" style="width:100%; border-radius:6px; padding:8px;"></textarea>
        <button type="submit" class="btn small" style="margin-top:8px;">Enviar avaliação</button>
    </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
