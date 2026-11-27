<?php $paginaAtual = 'perfil'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Meu Perfil</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/perfil.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Meu Perfil</h1>
    <div class="perfil-barra"></div>

    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="sucesso"><?= esc($sucesso) ?></div><?php endif; ?>

    <div style="display:flex; align-items:center; gap:18px; margin-bottom:24px;">
        <div class="perfil-avatar" style="overflow:hidden; margin-bottom:0;">
            <?php if (!empty($usuario['foto_perfil'])): ?>
                <img src="<?= esc($usuario['foto_perfil']) ?>?v=<?= time() ?>" alt="Foto de perfil" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:60%;height:60%;color:#fff;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?php endif; ?>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;"><?= esc($usuario['nome_usuario']) ?></div>
            <div style="font-size:13px;color:var(--text-muted);">@<?= esc($usuario['nickname']) ?> &bull; <?= esc($usuario['email']) ?></div>
        </div>
    </div>

    <form method="post" action="index.php?page=perfil" enctype="multipart/form-data" class="perfil-campos" style="max-width:520px;">
        <div style="background:var(--panel-bg); border:1px solid var(--border-soft); border-radius:10px; padding:16px; margin-bottom:22px;">
            <label style="display:block;font-size:14px;font-weight:700;margin-bottom:8px;">&#128247; Alterar foto de perfil</label>
            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif,.webp,image/*" style="width:100%;">
            <small style="color:var(--text-muted);">JPG, PNG, GIF ou WEBP, até 2 MB. Deixe em branco para manter a foto atual.</small>
        </div>

        <label style="display:block;font-size:13px;margin-bottom:6px;">Nome completo</label>
        <input type="text" name="nome_usuario" value="<?= esc($usuario['nome_usuario']) ?>" class="campo-peach" style="width:100%;padding:10px;border:none;border-radius:6px;margin-bottom:14px;" required>

        <label style="display:block;font-size:13px;margin-bottom:6px;">Nickname</label>
        <input type="text" name="nickname" value="<?= esc($usuario['nickname']) ?>" class="campo-peach" style="width:100%;padding:10px;border:none;border-radius:6px;margin-bottom:14px;" required>

        <label style="display:block;font-size:13px;margin-bottom:6px;">E-mail</label>
        <input type="email" name="email" value="<?= esc($usuario['email']) ?>" class="campo-peach" style="width:100%;padding:10px;border:none;border-radius:6px;margin-bottom:14px;" required>

        <label style="display:block;font-size:13px;margin-bottom:6px;">Data de nascimento</label>
        <input type="date" name="data_nascimento" value="<?= esc($usuario['data_nascimento']) ?>" class="campo-peach" style="width:100%;padding:10px;border:none;border-radius:6px;margin-bottom:14px;" required>

        <label style="display:block;font-size:13px;margin-bottom:6px;">Modificar senha (deixe em branco para não alterar)</label>
        <input type="password" name="senha_atual" placeholder="Senha atual" autocomplete="current-password" class="campo-peach" style="width:100%;padding:10px;border:none;border-radius:6px;margin-bottom:10px;">
        <input type="password" name="nova_senha" placeholder="Nova senha" autocomplete="new-password" class="campo-peach" style="width:100%;padding:10px;border:none;border-radius:6px;margin-bottom:14px;">

        <button type="submit" class="btn">Salvar alterações</button>
    </form>

    <div style="margin-top:30px; display:flex; gap:14px; flex-wrap:wrap;">
        <?php if (!$ehCriador): ?>
            <a href="index.php?page=register_creator" class="btn azul">Tornar-se um Criador</a>
        <?php else: ?>
            <a href="index.php?page=criador" class="btn azul">Painel do Criador</a>
        <?php endif; ?>
        <?php if (ehAdmin()): ?>
            <a href="index.php?page=admin" class="btn outline">Painel Administrativo</a>
        <?php endif; ?>
        <a href="index.php?page=logout" class="btn outline">Sair da Conta</a>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
