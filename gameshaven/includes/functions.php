<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function esc($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function redirecionar($pagina, $params = []) {
    $url = 'index.php?page=' . urlencode($pagina);
    foreach ($params as $chave => $valor) {
        $url .= '&' . urlencode($chave) . '=' . urlencode($valor);
    }
    header('Location: ' . $url);
    exit;
}

function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

function usuarioLogado() {
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin() {
    if (!estaLogado()) {
        redirecionar('login');
    }
}

function ehAdmin() {
    return estaLogado() && !empty($_SESSION['usuario']['is_admin']);
}

function exigirAdmin() {
    exigirLogin();
    if (!ehAdmin()) {
        redirecionar('home');
    }
}

function formatarPreco($valor) {
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function formatarData($data) {
    if (!$data) return '';
    $ts = strtotime($data);
    return date('d/m/Y', $ts);
}

function mensagemFlash($chave = null, $valor = null) {
    if ($chave !== null && $valor !== null) {
        $_SESSION['flash'][$chave] = $valor;
        return;
    }
    if ($chave !== null) {
        $msg = $_SESSION['flash'][$chave] ?? null;
        unset($_SESSION['flash'][$chave]);
        return $msg;
    }
    return null;
}

function urlAtiva($pagina, $atual) {
    return $pagina === $atual ? ' class="nav-ativo"' : '';
}

/**
 * Valida os campos específicos da forma de pagamento escolhida (cartão de
 * crédito/débito ou pix), usada tanto no fechamento de pedido (compra) quanto
 * nas doações, já que as duas telas compartilham o mesmo formulário de
 * pagamento. Retorna uma mensagem de erro, ou null se estiver tudo certo.
 * Nenhum dado de cartão é salvo no banco - só validamos para simular a
 * aprovação, como no fluxo do Figma.
 */
function validarDadosPagamento($metodo) {
    if ($metodo === 'pix') {
        if (trim($_POST['nome_pix'] ?? '') === '') {
            return 'Informe o nome do cliente para gerar o Pix.';
        }
        return null;
    }

    // cartao credito / cartao debito
    $numero = preg_replace('/\D/', '', $_POST['numero_cartao'] ?? '');
    $nomeCartao = trim($_POST['nome_cartao'] ?? '');
    $validade = trim($_POST['validade'] ?? '');
    $cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? '');

    if (strlen($numero) < 13 || strlen($numero) > 19) {
        return 'Número do cartão inválido.';
    }
    if ($nomeCartao === '') {
        return 'Informe o nome impresso no cartão.';
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{4}$/', $validade)) {
        return 'Validade inválida. Use o formato MM/AAAA.';
    }
    [$mes, $ano] = explode('/', $validade);
    if (mktime(0, 0, 0, (int) $mes + 1, 1, (int) $ano) <= time()) {
        return 'Cartão vencido.';
    }
    if (strlen($cvv) < 3 || strlen($cvv) > 4) {
        return 'CVV inválido.';
    }
    return null;
}

/**
 * Salva uma imagem enviada pelo formulário (campo $campo) numa pasta de uploads.
 * Valida o tipo real do arquivo (não só a extensão) e o tamanho máximo.
 * Retorna o caminho relativo gravado no banco, ou null se nada foi enviado.
 * Lança Exception se o envio for inválido.
 */
function salvarImagemUpload($campo, $pastaRelativa, $prefixo, $maxMB = 5) {
    if (empty($_FILES[$campo]['tmp_name']) || ($_FILES[$campo]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Não foi possível enviar a imagem. Tente outro arquivo.');
    }
    if ($_FILES[$campo]['size'] > $maxMB * 1024 * 1024) {
        throw new Exception("A imagem deve ter no máximo {$maxMB} MB.");
    }
    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$campo]['tmp_name']);
    if (!isset($permitidos[$tipo])) {
        throw new Exception('Formato de imagem inválido. Use JPG, PNG, GIF ou WEBP.');
    }
    $pasta = __DIR__ . '/../' . $pastaRelativa;
    if (!is_dir($pasta)) mkdir($pasta, 0755, true);
    $nome = $prefixo . time() . '_' . bin2hex(random_bytes(4)) . '.' . $permitidos[$tipo];
    if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $pasta . $nome)) {
        throw new Exception('Não foi possível salvar a imagem no servidor.');
    }
    return $pastaRelativa . $nome;
}

/** Capa de jogo: public/uploads/capas/ (até 5 MB). */
function salvarCapaUpload($campo = 'capa') {
    return salvarImagemUpload($campo, 'public/uploads/capas/', 'capa_', 5);
}

/** Foto de perfil: public/uploads/avatares/ (até 2 MB). */
function salvarFotoPerfilUpload($campo = 'foto') {
    return salvarImagemUpload($campo, 'public/uploads/avatares/', 'avatar_', 2);
}

/** Apaga um arquivo enviado (capa ou foto), só se estiver dentro de public/uploads/. */
function removerArquivoUpload($caminho) {
    if (!$caminho || strpos($caminho, 'public/uploads/') !== 0 || strpos($caminho, '..') !== false) return;
    $arquivo = __DIR__ . '/../' . $caminho;
    if (is_file($arquivo)) @unlink($arquivo);
}

function removerArquivoCapa($caminho) {
    removerArquivoUpload($caminho);
}

/** Conta caracteres UTF-8 sem depender da extensão mbstring. */
function contarCaracteres($texto) {
    return strlen(preg_replace('/[\x80-\xBF]/', '', $texto));
}

/**
 * Confere se o banco tem o que o site precisa (criado pelo atualizar_banco.sql).
 * Retorna a lista do que falta; lista vazia = banco atualizado.
 * Quando está tudo certo, guarda isso na sessão para não consultar a cada página.
 */
function pendenciasBanco() {
    if (!empty($_SESSION['banco_ok'])) return [];

    $exigidos = [
        ['jogos', 'capa'],
        ['jogos', 'nota_critica'],
        ['jogos', 'tag'],
        ['jogos', 'icone'],
        ['jogos', 'cadastrado_em'],
        ['usuarios', 'is_admin'],
        ['usuarios', 'foto_perfil'],
        ['nota_fiscal', 'id_criadores'],
        ['nota_fiscal', 'comprador_nome'],
        ['pedido_itens', 'quantidade'],
        ['suporte_respostas', 'resposta'],
    ];
    $pdo = getConexao();
    $faltando = [];
    foreach ($exigidos as [$tabela, $coluna]) {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $stmt->execute([$tabela, $coluna]);
        if ((int) $stmt->fetchColumn() === 0) {
            $faltando[] = "$tabela.$coluna";
        }
    }
    if (!$faltando) {
        $_SESSION['banco_ok'] = true;
    }
    return $faltando;
}

/** Caminho da imagem só se o arquivo existir de verdade (evita ícone de imagem quebrada). */
function imagemExiste($caminho) {
    return $caminho && strpos($caminho, 'public/uploads/') === 0 && strpos($caminho, '..') === false
        && is_file(__DIR__ . '/../' . $caminho);
}

/** Ícone pequeno do jogo: public/uploads/icones/ (até 1 MB). */
function salvarIconeUpload($campo = 'icone') {
    return salvarImagemUpload($campo, 'public/uploads/icones/', 'icone_', 1);
}

/** Miniatura do jogo nas listas: o ícone, se existir; senão a capa; senão null (quadrado cinza). */
function iconeOuCapa($jogo) {
    if (imagemExiste($jogo['icone'] ?? null)) return $jogo['icone'];
    if (imagemExiste($jogo['capa'] ?? null)) return $jogo['capa'];
    return null;
}
