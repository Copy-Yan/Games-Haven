<?php
$paginaAtual = $paginaAtual ?? '';
?>

<footer class="site-footer" aria-label="Rodapé">
  <div class="footer-simple">
    <div class="footer-brand">
      
      <span class="footer-brand-name">GamesHaven</span>
    </div>
    <p class="footer-tagline">Sua loja de jogos digitais. Descubra, jogue e compartilhe.</p>
    <div class="footer-links">
        <a href="index.php?page=sobre"<?= urlAtiva('sobre', $paginaAtual); ?>>Sobre</a>
        <a href="index.php?page=suporte"<?= urlAtiva('suporte', $paginaAtual) ?>>Suporte</a>
        <a href="#">Privacidade</a>
        <a href="#">Termos de uso</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 GamesHaven. Todos os direitos reservados.</span>
  </div>
</footer>
</body>
</html>
