<?php $paginaAtual = 'admin_tickets'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Responder Ticket</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Responder Tickets</h1>

    <div class="painel largo">
        <div style="display:flex; justify-content:space-between; font-weight:bold; padding-bottom:10px; border-bottom:1px solid var(--border-soft);">
            <span>ID_TICKET <?= $ticket['id_suporte'] ?></span>
            <span>Nome( <?= esc($ticket['nickname']) ?> )</span>
        </div>
        <p style="text-align:center; font-weight:bold; margin-top:16px;">Assunto: <?= esc($ticket['assunto']) ?></p>
        <p style="font-size:13px; color:var(--text-muted);"><?= nl2br(esc($ticket['descricao'])) ?></p>

        <?php foreach ($suporteRespostas ?? [] as $resp): ?>
            <div style="background:#2b2f4a; padding:10px 14px; border-radius:6px; margin-bottom:8px; font-size:13px;">
                <?= nl2br(esc($resp['resposta'])) ?>
            </div>
        <?php endforeach; ?>

        <form method="post" action="index.php?page=admin_ticket_reply&id=<?= $ticket['id_suporte'] ?>">
            <label>Responder</label>
            <textarea name="resposta" rows="6" class="campo-peach" required></textarea>
            <div class="centro" style="margin-top:18px;">
                <button type="submit" class="btn outline">Enviar</button>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
