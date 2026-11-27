<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Criador.php';

/**
 * Tela "Meu Perfil" (mesmos recursos do GameStormy): foto de perfil, dados
 * pessoais e troca de senha, tudo num único formulário.
 */
function handleProfile() {
    exigirLogin();
    $erro = null;
    $sucesso = mensagemFlash('sucesso');
    $usuarioModelo = new Usuario();
    $criadorModelo = new Criador();

    $usuario = $usuarioModelo->buscarPorId($_SESSION['usuario_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dados = [
            'nome_usuario' => trim($_POST['nome_usuario'] ?? ''),
            'nickname' => trim($_POST['nickname'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'data_nascimento' => $_POST['data_nascimento'] ?? '',
        ];
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $dataValida = DateTime::createFromFormat('Y-m-d', $dados['data_nascimento']);

        if (in_array('', $dados, true)) {
            $erro = 'Preencha nome, nickname, e-mail e data de nascimento.';
        } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe um e-mail válido.';
        } elseif (!$dataValida) {
            $erro = 'Informe uma data de nascimento válida.';
        } elseif ($usuarioModelo->campoEmUsoPorOutro($usuario['id_usuarios'], 'nome_usuario', $dados['nome_usuario'])) {
            $erro = 'Este nome de usuário já está em uso.';
        } elseif ($usuarioModelo->campoEmUsoPorOutro($usuario['id_usuarios'], 'nickname', $dados['nickname'])) {
            $erro = 'Este nickname já está em uso.';
        } elseif ($usuarioModelo->campoEmUsoPorOutro($usuario['id_usuarios'], 'email', $dados['email'])) {
            $erro = 'Este e-mail já está em uso por outra conta.';
        } elseif ($novaSenha !== '' && !password_verify($senhaAtual, $usuario['senha'])) {
            $erro = 'Para trocar a senha, informe a senha atual corretamente.';
        } elseif ($novaSenha !== '' && strlen($novaSenha) < 4) {
            $erro = 'A nova senha deve ter ao menos 4 caracteres.';
        }

        $novaFoto = null;
        if (!$erro) {
            try {
                $novaFoto = salvarFotoPerfilUpload('foto');
            } catch (Exception $e) {
                $erro = $e->getMessage();
            }
        }

        if (!$erro) {
            $usuarioModelo->atualizarPerfil($usuario['id_usuarios'], $dados);
            if ($novaSenha !== '') {
                $usuarioModelo->atualizarSenha($usuario['id_usuarios'], $novaSenha);
            }
            if ($novaFoto !== null) {
                $fotoAntiga = $usuario['foto_perfil'];
                $usuarioModelo->atualizarFotoPerfil($usuario['id_usuarios'], $novaFoto);
                removerArquivoUpload($fotoAntiga);
            }
            $usuario = $usuarioModelo->buscarPorId($_SESSION['usuario_id']);
            $sucesso = 'Perfil atualizado com sucesso!';
        }
    }

    // Mantém a sessão sincronizada com o banco (foto, admin, dados).
    $_SESSION['usuario'] = $usuario;
    $ehCriador = (bool) $criadorModelo->buscarPorUsuario($_SESSION['usuario_id']);

    require __DIR__ . '/../views/profile/index.php';
}
