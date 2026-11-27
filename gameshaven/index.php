<?php
/**
 * GamesHaven - roteador principal (front controller).
 * Toda navegação do site passa por aqui: index.php?page=NOME_DA_PAGINA
 * Sem JavaScript em nenhum lugar do projeto - navegação 100% via links/forms.
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/idade.php';

// Se o banco ainda não tem as colunas/tabelas desta versão, mostra como atualizar
// em vez de deixar o site quebrar com erro fatal.
$pendencias = pendenciasBanco();
if ($pendencias) {
    require __DIR__ . '/includes/banco_desatualizado.php';
    exit;
}

$page = $_GET['page'] ?? 'home';

// Porta de entrada: com o reCAPTCHA Enterprise ativo, nenhuma página abre antes da verificação.
require_once __DIR__ . '/includes/recaptcha.php';
if (recaptchaAtivo() && empty($_SESSION['captcha_ok']) && $page !== 'captcha') {
    $cfgCaptcha = recaptchaConfig();
    $siteKey = $cfgCaptcha['site_key'];
    $acao = $cfgCaptcha['acao'];
    $erro = null;
    require __DIR__ . '/views/captcha/index.php';
    exit;
}

switch ($page) {

    // ---------- Autenticação ----------
    case 'login':
        require __DIR__ . '/controllers/AuthController.php';
        handleLogin();
        break;

    case 'register':
        require __DIR__ . '/controllers/AuthController.php';
        handleRegister();
        break;

    case 'register_creator':
        require __DIR__ . '/controllers/AuthController.php';
        handleRegisterCreator();
        break;

    case 'logout':
        require __DIR__ . '/controllers/AuthController.php';
        handleLogout();
        break;

    // ---------- Perfil ----------
    case 'perfil':
        require __DIR__ . '/controllers/ProfileController.php';
        handleProfile();
        break;

    // ---------- Loja / vitrine ----------
    case 'busca':
        require __DIR__ . '/controllers/BuscaController.php';
        handleBusca();
        break;

    // ---------- Notas fiscais ----------
    case 'admin_notas':
        require __DIR__ . '/controllers/NotaController.php';
        handleNotasAdmin();
        break;

    case 'criador_notas':
        require __DIR__ . '/controllers/NotaController.php';
        handleNotasCriador();
        break;

    case 'nota_detalhe':
        require __DIR__ . '/controllers/NotaController.php';
        handleNotaDetalhe();
        break;

    case 'captcha':
        require __DIR__ . '/controllers/CaptchaController.php';
        handleCaptcha();
        break;

    case 'home':
        require __DIR__ . '/controllers/HomeController.php';
        handleHome();
        break;

    case 'jogo':
        require __DIR__ . '/controllers/GameController.php';
        handleGameDetail();
        break;

    // ---------- Biblioteca ----------
    case 'biblioteca':
        require __DIR__ . '/controllers/LibraryController.php';
        handleLibrary();
        break;

    // ---------- Suporte ----------
    case 'suporte':
        require __DIR__ . '/controllers/SupportController.php';
        handleSupport();
        break;

    // ---------- Sobre ----------
    case 'sobre':
        $paginaAtual = 'sobre';
        require __DIR__ . '/views/about/index.php';
        break;

    // ---------- Carrinho ----------
    case 'carrinho':
        require __DIR__ . '/controllers/CartController.php';
        handleCart();
        break;

    // ---------- Pagamento ----------
    case 'pagamento':
        require __DIR__ . '/controllers/PaymentController.php';
        handlePayment();
        break;

    case 'confirmacao':
        require __DIR__ . '/controllers/PaymentController.php';
        handleOrderConfirmation();
        break;

    // ---------- Painel Administrativo ----------
    case 'admin':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminDashboard();
        break;

    case 'admin_games':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminGames();
        break;

    case 'admin_add_game':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminAddGame();
        break;

    case 'admin_edit_game':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminEditGame();
        break;

    case 'admin_remove_game':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminRemoveGame();
        break;

    case 'admin_add_desenvolvedora':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminAddDesenvolvedora();
        break;

    case 'admin_add_publicadora':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminAddPublicadora();
        break;

    case 'admin_tickets':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminTickets();
        break;

    case 'admin_ticket_reply':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminTicketReply();
        break;

    case 'admin_donations':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminDonations();
        break;

    case 'admin_purchases':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminPurchases();
        break;

    case 'admin_creators':
        require __DIR__ . '/controllers/AdminController.php';
        handleAdminCreators();
        break;

    // ---------- Doações ----------
    case 'doacao':
        require __DIR__ . '/controllers/DonationController.php';
        handleDonationChoose();
        break;

    case 'doacao_jogo':
        require __DIR__ . '/controllers/DonationController.php';
        handleDonationForm();
        break;

    // ---------- Painel do Criador ----------
    case 'criador':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorDashboard();
        break;

    case 'criador_add_game':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorAddGame();
        break;

    case 'criador_remover_jogo':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorRemoveGame();
        break;

    case 'criador_add_desenvolvedora':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorAddDesenvolvedora();
        break;

    case 'criador_add_publicadora':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorAddPublicadora();
        break;

    case 'criador_jogos_detalhes':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorGameDetails();
        break;

    case 'criador_nota_fiscal':
        require __DIR__ . '/controllers/CreatorController.php';
        handleCreatorInvoice();
        break;

    // ---------- Página não encontrada ----------
    default:
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8">
              <title>GamesHaven - Página não encontrada</title>
              <link rel="stylesheet" href="public/css/base.css"></head><body>
              <div class="wrapper">';
        require __DIR__ . '/includes/header.php';
        echo '<div class="conteudo pagina centro">
                <h1 class="titulo-pagina">404</h1>
                <p>Esta página não existe.</p>
                <a href="index.php?page=home" class="btn">Voltar ao início</a>
              </div>';
        require __DIR__ . '/includes/footer.php';
        echo '</div></body></html>';
        break;
}
