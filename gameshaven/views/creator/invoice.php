<?php $paginaAtual = 'criador'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Nota Fiscal</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/criador.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Nota Fiscal</h1>
    <p class="subtitulo"><a href="index.php?page=criador_jogos_detalhes" style="text-decoration:underline;">&larr; Voltar ao Detalhamento dos Jogos</a></p>

    <div class="painel" style="max-width:520px;">
        <h3 style="margin-top:0;">&#128196; Nota Fiscal - <?= esc($origem) ?></h3>
        <p><strong>Número:</strong> <?= esc($nota['numero_nota']) ?></p>
        <p><strong>Série:</strong> <?= esc($nota['serie']) ?></p>
        <p><strong>Data de Emissão:</strong> <?= formatarData($nota['data_emissao']) ?></p>
        <p><strong>Status:</strong> <?= esc(ucfirst($nota['status'])) ?></p>
        <hr style="border-color: rgba(255,255,255,0.15); margin: 14px 0;">
        <p><strong>Valor dos Produtos:</strong> <?= formatarPreco($nota['valor_produtos']) ?></p>
        <p style="font-size:16px;"><strong>Valor Total:</strong> <?= formatarPreco($nota['valor_total']) ?></p>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
