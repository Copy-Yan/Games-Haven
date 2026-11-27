<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Criador.php';
require_once __DIR__ . '/../includes/pam.php';

function handleLogin() {
    $erro = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $modelo = new Usuario();
        $usuario = $modelo->buscarPorEmailOuUsuario($login);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            if ($usuario['status'] !== 'ativo') {
                $erro = 'Sua conta está ' . $usuario['status'] . '. Procure o suporte.';
            } else {
                $_SESSION['usuario_id'] = $usuario['id_usuarios'];
                $_SESSION['usuario'] = $usuario;
                redirecionar('home');
            }
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    }
    require __DIR__ . '/../views/auth/login.php';
}

function handleRegister() {
    $erro = null;
    $dados = ['nome_usuario' => '', 'nickname' => '', 'email' => '', 'data_nascimento' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados['nome_usuario'] = trim($_POST['nome_usuario'] ?? '');
        $dados['nickname'] = trim($_POST['nickname'] ?? '');
        $dados['email'] = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';
        $dia = $_POST['dia'] ?? '';
        $mes = $_POST['mes'] ?? '';
        $ano = $_POST['ano'] ?? '';
        $dados['data_nascimento'] = ($ano && $mes && $dia) ? "$ano-$mes-$dia" : '';

        $modelo = new Usuario();

        if ($dados['nome_usuario'] === '' || $dados['nickname'] === '' || $dados['email'] === '' || $senha === '' || $dados['data_nascimento'] === '') {
            $erro = 'Preencha todos os campos.';
        } elseif ($senha !== $confirmar) {
            $erro = 'As senhas não conferem.';
        } elseif ($modelo->emailExiste($dados['email'])) {
            $erro = 'Este e-mail já está cadastrado.';
        } elseif ($modelo->usuarioExiste($dados['nome_usuario'])) {
            $erro = 'Este nome de usuário já está em uso.';
        } else {
            // Política de senhas da organização (ManageEngine PAM360), se estiver ativa.
            $pam = pamValidarSenha($senha);
            if ($pam['status'] === 'invalida') {
                $erro = 'Senha fora da política de segurança: ' . $pam['mensagem'];
            } elseif ($pam['status'] === 'indisponivel' && !pamPermiteSemValidacao()) {
                $erro = 'Não foi possível validar a senha agora. Tente novamente em instantes.';
            } else {
                $dados['senha'] = $senha;
                $idUsuario = $modelo->criar($dados);
                $_SESSION['usuario_id'] = $idUsuario;
                $_SESSION['usuario'] = $modelo->buscarPorId($idUsuario);
                redirecionar('home');
            }
        }
    }
    require __DIR__ . '/../views/auth/register.php';
}

function handleRegisterCreator() {
    exigirLogin();
    $erro = mensagemFlash('erro');
    $modeloCriador = new Criador();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $senhaConfirmar = $_POST['senha_confirmar'] ?? '';
        $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
        $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
        $agencia = trim($_POST['agencia'] ?? '');
        $pix = trim($_POST['pix'] ?? '');

        $usuarioModelo = new Usuario();
        $usuarioAtual = $usuarioModelo->buscarPorId($_SESSION['usuario_id']);

        if (!password_verify($senhaConfirmar, $usuarioAtual['senha'])) {
            $erro = 'Senha incorreta. Confirme sua senha atual para continuar.';
        } elseif (strlen($cpf) !== 11) {
            $erro = 'Informe um CPF válido (11 dígitos).';
        } elseif (strlen($telefone) < 10) {
            $erro = 'Informe um telefone válido.';
        } elseif ($agencia === '' || $pix === '') {
            $erro = 'Preencha a agência bancária e a chave pix.';
        } else {
            try {
                $modeloCriador->cadastrar($_SESSION['usuario_id'], $cpf, $telefone, $agencia, $pix);
                mensagemFlash('sucesso', 'Cadastro de criador realizado com sucesso!');
                redirecionar('perfil');
            } catch (Exception $e) {
                $erro = 'Não foi possível concluir o cadastro: ' . $e->getMessage();
            }
        }
    }
    require __DIR__ . '/../views/auth/register_creator.php';
}

function handleLogout() {
    $_SESSION = [];
    session_destroy();
    redirecionar('login');
}
