<?php $paginaAtual = 'home'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Explorar</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/home.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <?php $erroFlash = mensagemFlash('erro'); if ($erroFlash): ?><div class="erro"><?= esc($erroFlash) ?></div><?php endif; ?>

    <?php
    // Destaque: jogo com maior nota crítica (ou o mais recente, se nenhum tiver nota).
    $destaqueJogo = $emDestaque[0] ?? ($recentes[0] ?? null);
    // Novidades: os lançamentos mais recentes (ou os cadastrados mais novos, se não houver lançamentos).
    $novidades = array_slice($novosLancamentos ?: $recentes, 0, 3);
    ?>
    <div class="banner-topo">
        <?php if ($destaqueJogo): ?>
        <a class="hero-jogo" href="index.php?page=jogo&id=<?= (int) $destaqueJogo['id_jogos'] ?>">
            <div class="hero-capa-box">
                <?php if (imagemExiste($destaqueJogo['capa'])): ?>
                    <img src="<?= esc($destaqueJogo['capa']) ?>" alt="">
                <?php else: ?>
                    <span>colocar imagem</span>
                <?php endif; ?>
            </div>
            <div class="hero-texto">
                <span class="hero-etiqueta">Em destaque<?= $destaqueJogo['nota_critica'] !== null ? ' &middot; &#9733; ' . esc($destaqueJogo['nota_critica']) : '' ?></span>
                <h2><?= esc($destaqueJogo['titulo']) ?></h2>
                <?php preg_match('/^.{0,160}/us', (string) $destaqueJogo['descricao'], $trecho); ?>
                <p><?= esc($trecho[0] ?? '') ?></p>
                <div class="hero-acoes">
                    <span class="hero-preco"><?= formatarPreco($destaqueJogo['preco']) ?></span>
                    <span class="btn small" id ="btn-home">Ver jogo</span>
                </div>
            </div>
        </a>
        <?php else: ?>
        <div class="hero-vazio">Nenhum jogo cadastrado ainda.</div>
        <?php endif; ?>

        <div class="banner-lateral">
            <strong class="novidades-titulo">Novidades da loja</strong>
            <?php foreach ($novidades as $n): ?>
            <a class="novidade-item" href="index.php?page=jogo&id=<?= (int) $n['id_jogos'] ?>">
                <span class="novidade-capa">
                    <?php if ($src = iconeOuCapa($n)): ?><img src="<?= esc($src) ?>" alt=""><?php endif; ?>
                </span>
                <span class="novidade-info">
                    <strong><?= esc($n['titulo']) ?></strong>
                    <small><?= formatarData($n['data_lancamento']) ?> &middot; <?= formatarPreco($n['preco']) ?></small>
                </span>
            </a>
            <?php endforeach; ?>
            <?php if (!$novidades): ?><p class="novidades-vazio">Nenhum lançamento ainda.</p><?php endif; ?>
        </div>
    </div>

    <div class="secao-titulo">Explorar por categoria <span>&rsaquo;</span></div>
    <div class="categorias-linha">
        <?php if ($categorias): foreach ($categorias as $cat): ?>
            <a href="index.php?page=home&busca=<?= urlencode($cat['nome']) ?>" class="categoria-pill"><?= esc($cat['nome']) ?></a>
        <?php endforeach; else: ?>
            <div class="categoria-pill">colocar imagem</div>
            <div class="categoria-pill">colocar imagem</div>
            <div class="categoria-pill">colocar imagem</div>
            <div class="categoria-pill">colocar imagem</div>
        <?php endif; ?>
    </div>

    <div class="secao-titulo">Novos lançamentos</div>
    <div class="grade-jogos">
        <?php foreach ($novosLancamentos as $jogo): ?>
        <div class="card-jogo">
            <div class="capa" style="height:130px;"><?php if (imagemExiste($jogo['capa'])): ?><img src="<?= esc($jogo['capa']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>colocar imagem<?php endif; ?></div>
            <div class="info">
                <strong><?= esc($jogo['titulo']) ?></strong><br>
                <small>Principal jogo lançado, clique aqui para o jogo</small>
                <span class="preco"><?= formatarPreco($jogo['preco']) ?></span>
                <a href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>" class="btn small" style="margin-top:6px; display:inline-block;">Ver jogo</a>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$novosLancamentos): ?><p style="color:var(--text-muted);">Nenhum jogo lançado ainda.</p><?php endif; ?>
    </div>

    <div style="display:flex; gap: 26px; flex-wrap: wrap; margin-top: 26px;">
        <div style="flex:1; min-width:220px;">
            <div class="secao-titulo">Jogos em destaque <span>&rsaquo;</span></div>
            <?php foreach ($emDestaque as $jogo): ?>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <?php if ($src = iconeOuCapa($jogo)): ?><img class="icone-mini" src="<?= esc($src) ?>" alt=""><?php else: ?><span class="capa-placeholder"></span><?php endif; ?>
                    <a href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>"><?= esc($jogo['titulo']) ?></a>
                    <?php if ($jogo['nota_critica'] !== null): ?><small style="color:var(--text-muted);">&#9733; <?= esc($jogo['nota_critica']) ?></small><?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (!$emDestaque): ?><p style="color:var(--text-muted); font-size:12px;">Nenhum jogo ainda.</p><?php endif; ?>
        </div>
        <div style="flex:1; min-width:220px;">
            <div class="secao-titulo">Mais jogados <span>&rsaquo;</span></div>
            <?php foreach ($maisJogados as $jogo): ?>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <?php if ($src = iconeOuCapa($jogo)): ?><img class="icone-mini" src="<?= esc($src) ?>" alt=""><?php else: ?><span class="capa-placeholder"></span><?php endif; ?>
                    <a href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>"><?= esc($jogo['titulo']) ?></a>
                </div>
            <?php endforeach; ?>
            <?php if (!$maisJogados): ?><p style="color:var(--text-muted); font-size:12px;">Nenhum jogo ainda.</p><?php endif; ?>
        </div>
        <div style="flex:1; min-width:220px;">
            <div class="secao-titulo"><?= esc($tituloPreLancamentos) ?> <span>&rsaquo;</span></div>
            <?php foreach ($preLancamentos as $jogo): ?>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <?php if ($src = iconeOuCapa($jogo)): ?><img class="icone-mini" src="<?= esc($src) ?>" alt=""><?php else: ?><span class="capa-placeholder"></span><?php endif; ?>
                    <a href="index.php?page=jogo&id=<?= $jogo['id_jogos'] ?>"><?= esc($jogo['titulo']) ?></a>
                </div>
            <?php endforeach; ?>
            <?php if (!$preLancamentos): ?><p style="color:var(--text-muted); font-size:12px;">Nenhum jogo cadastrado ainda.</p><?php endif; ?>
        </div>
    </div>

    <div class="secao-titulo">Jogos para você</div>
    <div class="grade-jogos">
        <?php foreach ($paraVoce as $jogo): ?>
        <div class="card-jogo">
            <div class="capa"><?php if (imagemExiste($jogo['capa'])): ?><img src="<?= esc($jogo['capa']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;"><?php else: ?>colocar imagem<?php endif; ?></div>
            <div class="info">
                <?= esc($jogo['titulo']) ?><br>
                <span class="preco"><?= formatarPreco($jogo['preco']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$paraVoce): ?><p style="color:var(--text-muted);">Nenhum resultado encontrado.</p><?php endif; ?>
    </div>

    <p style="text-align:center; color:var(--text-muted); margin-top:30px; font-size:12px;">Sobre a loja, final do site &mdash; <a href="index.php?page=sobre" style="text-decoration:underline;">saiba mais</a></p>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
