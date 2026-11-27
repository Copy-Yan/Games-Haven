<?php
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../models/Doacao.php';
require_once __DIR__ . '/../models/Endereco.php';
require_once __DIR__ . '/../models/NotaFiscal.php';
require_once __DIR__ . '/../includes/endereco.php';

/** Tela "Fazer Doação" (acessada pelo Carrinho): lista os jogos que já têm um criador vinculado. */
function handleDonationChoose() {
    exigirLogin();
    $jogoModelo = new Jogo();
    $jogos = $jogoModelo->listarComCriador();
    require __DIR__ . '/../views/donation/choose.php';
}

/** Formulário de doação (valor + Pix/cartão) para um jogo/criador específico. */
function handleDonationForm() {
    exigirLogin();
    $id = (int) ($_GET['id'] ?? 0);
    $jogoModelo = new Jogo();
    $jogo = $jogoModelo->buscarPorId($id);
    if (!$jogo) redirecionar('doacao');
    if (!jogoPermitidoParaUsuario($jogo)) {
        mensagemFlash('erro', 'Este jogo não está disponível para a sua faixa etária.');
        redirecionar('home');
    }

    $erro = null;
    $metodo = $_GET['metodo'] ?? $_POST['metodo'] ?? 'pix';
    $usuario = usuarioLogado();
    $valorPadrao = $_POST['valor'] ?? '10,00';
    $enderecoModelo = new Endereco();
    $endereco = $enderecoModelo->buscarPorUsuario($_SESSION['usuario_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $metodo = $_POST['metodo'] ?? 'pix';
        $nomeDoador = trim($_POST['nome_completo'] ?? '');
        $emailDoador = trim($_POST['email'] ?? '');
        $valor = (float) str_replace(',', '.', $_POST['valor'] ?? '0');
        [$enderecoPost, $erroEndereco] = lerEnderecoPost();
        $endereco = $enderecoPost;

        if ($nomeDoador === '' || $emailDoador === '') {
            $erro = 'Preencha nome e e-mail do doador.';
        } elseif ($valor <= 0) {
            $erro = 'Informe um valor de doação maior que zero.';
        } elseif ($erroEndereco !== null) {
            $erro = $erroEndereco;
        } elseif (($erroPagamento = validarDadosPagamento($metodo)) !== null) {
            $erro = $erroPagamento;
        } else {
            $enderecoModelo->salvar($_SESSION['usuario_id'], $enderecoPost);
            try {
                $idDoacao = (new Doacao())->registrar($_SESSION['usuario_id'], $id, $valor, $metodo);
                (new NotaFiscal())->emitirParaDoacao($idDoacao);
                mensagemFlash('sucesso', 'Doação de ' . formatarPreco($valor) . ' registrada. Obrigado por apoiar este criador!');
                redirecionar('jogo', ['id' => $id]);
            } catch (Exception $e) {
                $erro = $e->getMessage();
            }
        }
    }

    require __DIR__ . '/../views/donation/form.php';
}
