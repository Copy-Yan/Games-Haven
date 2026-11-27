<?php
/** Endereço da conta (tabela enderecos). A tabela tem UNIQUE(id_usuarios): um endereço por usuário. */
class Endereco {
    private $db;
    public function __construct() { $this->db = getConexao(); }

    public function buscarPorUsuario($idUsuario) {
        $stmt = $this->db->prepare(
            'SELECT rua, numero, complemento, cidade, estado, cep FROM enderecos WHERE id_usuarios = ?'
        );
        $stmt->execute([$idUsuario]);
        return $stmt->fetch() ?: [];
    }

    /** Cria o endereço do usuário ou atualiza o que já existe. */
    public function salvar($idUsuario, array $dados) {
        $stmt = $this->db->prepare(
            'INSERT INTO enderecos (id_usuarios, rua, numero, cidade, cep, estado, complemento)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE rua = VALUES(rua), numero = VALUES(numero), cidade = VALUES(cidade),
                 cep = VALUES(cep), estado = VALUES(estado), complemento = VALUES(complemento)'
        );
        $stmt->execute([
            $idUsuario, $dados['rua'], (int) $dados['numero'], $dados['cidade'],
            $dados['cep'], $dados['estado'], $dados['complemento'] !== '' ? $dados['complemento'] : null,
        ]);
    }
}
