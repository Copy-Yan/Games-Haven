<?php
// Espera opcionalmente $paginaAtual definido pela view
$paginaAtual = $paginaAtual ?? '';
?>
<div class="topbar">
    <a href="index.php?page=home" class="logo">
        <img src="public/assets/HavenLogo.png" alt="Logo">
        GAMESHAVEN
    </a>
    <nav>
        <a href="index.php?page=home"<?= urlAtiva('home', $paginaAtual) ?>>Explorar</a>
        <a href="index.php?page=biblioteca"<?= urlAtiva('biblioteca', $paginaAtual) ?>>Biblioteca</a>
        <a href="index.php?page=sobre"<?= urlAtiva('sobre', $paginaAtual) ?>>Sobre</a>
        <a href="index.php?page=suporte"<?= urlAtiva('suporte', $paginaAtual) ?>>Suporte</a>
    </nav>
    <form class="busca" action="index.php" method="get">
        <input type="hidden" name="page" value="busca">
        <input type="text" name="busca" placeholder="Pesquisar jogo" value="<?= esc($_GET['busca'] ?? '') ?>">
        <button type="submit">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        </button>
    </form>
    <div class="topbar-icones">
        <a class="carrinho-icone" href="index.php?page=carrinho" title="Meu carrinho">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </a>
        <a class="perfil-icone" href="index.php?page=<?= estaLogado() ? 'perfil' : 'login' ?>" title="<?= estaLogado() ? 'Meu perfil' : 'Entrar' ?>">
           <?php $fotoTopo = estaLogado() ? ($_SESSION['usuario']['foto_perfil'] ?? null) : null; ?>
           <?php if ($fotoTopo): ?>
               <img src="<?= esc($fotoTopo) ?>" alt="Meu perfil" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
           <?php else: ?>
           <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="padding:6px;color:#fff;width:100%;height:100%;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
           <?php endif; ?>
        </a>
    </div>
</div>
