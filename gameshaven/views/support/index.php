<?php $paginaAtual = 'suporte'; ?>
<!DOCTYPE html>
    <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
                <title>GamesHaven - Suporte</title>
                    <link rel="stylesheet" href="public/css/base.css">
                    <link rel="stylesheet" href="public/css/suporte.css">
        </head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina support-page">
    <h1 class="titulo-pagina-suporte">Suporte</h1>

    <div class="suporte-topo-btns" >
        <a href="#form-suporte" data-cat="Pagamento">Caso seja reembolso</a>
        <a href="#form-suporte" data-cat="Produto">Caso seja problemas gerais</a>
    </div>

    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="sucesso"><?= esc($sucesso) ?></div><?php endif; ?>

    <?php if ($ticketAtual):?>
        <div class="painel largo support-card">
            <h3 style="text-align:center;">Seu chamado</h3>
            <p style="font-size:13px;"><strong>Assunto:</strong> <?= esc($ticketAtual['assunto']) ?></p>
            <p style="font-size:13px;"><strong>Categoria:</strong> <?= esc($ticketAtual['categoria']) ?> &middot; <strong>Status:</strong> <?= esc($ticketAtual['status']) ?></p>
            <p style="font-size:13px; color:var(--text-muted);"><?= nl2br(esc($ticketAtual['descricao'])) ?></p>
            <?php foreach ($respostas as $r): ?>
                <div style="background:#2b2f4a; padding:10px 14px; border-radius:6px; margin:8px 0; font-size:13px;">
                    <strong>Suporte GamesHaven:</strong><br><?= nl2br(esc($r['resposta'])) ?>
                </div>
            <?php endforeach; ?>
            <?php if ($ticketAtual['status'] !== 'Resolvido' && $ticketAtual['status'] !== 'Fechado'): ?>
                <p style="font-size:12px; color:var(--text-muted); margin-top:10px;">Aguardando resposta da nossa equipe.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="painel largo support-card" id="form-suporte">
            <h3 style="text-align:center; font-size: 24px;">Enviar Mensagem</h3>
            <?php if (!estaLogado()): ?>
                <p style="text-align:center; font-size:13px;">Você precisa <a href="index.php?page=login" style="text-decoration:underline;">entrar</a> para abrir um chamado.</p>
            <?php else: ?>
            <form method="post" action="index.php?page=suporte">
                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label>Digite seu nome</label>
                        <input type="text" id = input-suporte value="<?= esc(usuarioLogado()['nickname'] ?? '') ?>" disabled>
                    </div>
                    <div style="flex:1;">
                        <label>Digite seu e-mail</label>
                        <input type="text" id = input-suporte value="<?= esc(usuarioLogado()['email'] ?? '') ?>" disabled>
                    </div>
                </div>
                <label>Selecione o assunto</label>
                <select name="categoria"  id= mensagem-suporte>
                    <option value="Pagamento">Pagamento / Reembolso</option>
                    <option value="Conta">Conta</option>
                    <option value="Produto">Produto / Problemas gerais</option>
                </select>
                <label> Assunto (breve título)</label>
                <input type="text" id="input-suporte" name="assunto" placeholder="Ex: Reembolso do jogo X" required>
                <label>Mensagem</label>
                <textarea name="mensagem" id="mensagem-suporte" rows="4" required></textarea>
                <div class="centro" style="margin-top:18px;">
                <button type="submit" id="button-suporte" class="btn azul">Enviar</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
