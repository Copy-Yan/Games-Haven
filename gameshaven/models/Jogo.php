<?php
class Jogo {
    /** Mesmas tags do GameStormy (dropdown de "Tag" no cadastro/edição). */
    const TAGS = ['Jogo', 'Oferta da semana', 'Novo lançamento', 'Promoção', 'Destaque', 'Aventura',
                  'Ação', 'Indie', 'Cozy', 'Terror', 'RPG', 'Estratégia', 'Simulação', 'Premium'];
    const CLASSIFICACOES = ['L', '10', '12', '14', '16', '18'];

    private $db;
    public function __construct() { $this->db = getConexao(); }

    /**
     * Jogos cujo título contém o termo (busca do cabeçalho). Escapa os caracteres
     * curinga do LIKE; os que começam com o termo aparecem primeiro. Máx. 100.
     */
    public function buscarPorNome($termo) {
        $escapado = addcslashes($termo, '%_\\');
        $stmt = $this->db->prepare(
            'SELECT id_jogos, titulo FROM jogos
             WHERE titulo LIKE ? AND ' . condicaoIdadeSql() . '
             ORDER BY (titulo LIKE ?) DESC, titulo ASC
             LIMIT 100'
        );
        $stmt->execute(['%' . $escapado . '%', $escapado . '%']);
        return $stmt->fetchAll();
    }

    /** Catálogo filtrado pela faixa etária do usuário (admin vê tudo). */
    public function listarTodos($busca = '') {
        if ($busca !== '') {
            $stmt = $this->db->prepare(
                'SELECT * FROM jogos WHERE titulo LIKE ? AND ' . condicaoIdadeSql() . ' ORDER BY data_lancamento DESC'
            );
            $stmt->execute(['%' . $busca . '%']);
        } else {
            $stmt = $this->db->query('SELECT * FROM jogos WHERE ' . condicaoIdadeSql() . ' ORDER BY data_lancamento DESC');
        }
        return $stmt->fetchAll();
    }

    public function listarRecentes($limite = 6) {
        $stmt = $this->db->prepare(
            'SELECT * FROM jogos WHERE ' . condicaoIdadeSql() . ' ORDER BY data_lancamento DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Novos Lançamentos: jogos já lançados (data no passado/hoje), do mais recente pro mais antigo. */
    public function listarNovosLancamentos($limite = 3) {
        $stmt = $this->db->prepare(
            'SELECT * FROM jogos WHERE data_lancamento <= CURDATE() AND ' . condicaoIdadeSql() . '
             ORDER BY data_lancamento DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Em Destaque: maior nota_critica primeiro (quem não tem nota fica por último). */
    public function listarEmDestaque($limite = 3) {
        $stmt = $this->db->prepare(
            'SELECT * FROM jogos WHERE ' . condicaoIdadeSql() . '
             ORDER BY (nota_critica IS NULL) ASC, nota_critica DESC, data_lancamento DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Mais Jogados: quantos usuários têm o jogo na biblioteca (compraram/baixaram). */
    public function listarMaisJogados($limite = 3) {
        $stmt = $this->db->prepare(
            'SELECT j.*, COUNT(b.id_usuarios) AS total_jogadores
             FROM jogos j
             LEFT JOIN biblioteca b ON b.id_jogos = j.id_jogos
             WHERE ' . condicaoIdadeSql('j.') . '
             GROUP BY j.id_jogos
             ORDER BY total_jogadores DESC, j.data_lancamento DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Recém cadastrados: os jogos enviados por último ao catálogo (admin ou criador). */
    public function listarRecentesCadastros($limite = 3) {
        $stmt = $this->db->prepare(
            'SELECT * FROM jogos WHERE ' . condicaoIdadeSql() . ' ORDER BY cadastrado_em DESC, id_jogos DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Pré-Lançamentos: jogos com data de lançamento ainda no futuro, do que sai primeiro pro que sai depois. */
    public function listarPreLancamentos($limite = 3) {
        $stmt = $this->db->prepare(
            'SELECT * FROM jogos WHERE data_lancamento > CURDATE() AND ' . condicaoIdadeSql() . '
             ORDER BY data_lancamento ASC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Jogos para Você: se o usuário está logado e já tem jogos na biblioteca,
     * recomenda outros jogos das mesmas categorias que ele ainda não tem.
     * Sem usuário logado (ou sem histórico suficiente), cai no catálogo geral.
     */
    public function listarParaVoce($idUsuario, $limite = 8) {
        if ($idUsuario) {
            $stmt = $this->db->prepare(
                'SELECT DISTINCT j.*
                 FROM jogos j
                 JOIN jogos_categorias jc ON jc.id_jogos = j.id_jogos
                 WHERE jc.id_categorias IN (
                     SELECT jc2.id_categorias
                     FROM biblioteca bi
                     JOIN jogos_categorias jc2 ON jc2.id_jogos = bi.id_jogos
                     WHERE bi.id_usuarios = ?
                 )
                 AND j.id_jogos NOT IN (SELECT id_jogos FROM biblioteca WHERE id_usuarios = ?)
                 AND ' . condicaoIdadeSql('j.') . '
                 ORDER BY (j.nota_critica IS NULL) ASC, j.nota_critica DESC, j.data_lancamento DESC
                 LIMIT ?'
            );
            $stmt->bindValue(1, $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(2, $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(3, $limite, PDO::PARAM_INT);
            $stmt->execute();
            $recomendados = $stmt->fetchAll();
            if ($recomendados) return $recomendados;
        }
        // Sem usuário, sem biblioteca ainda, ou nenhuma recomendação encontrada: catálogo geral.
        $stmt = $this->db->prepare(
            'SELECT * FROM jogos WHERE ' . condicaoIdadeSql() . ' ORDER BY data_lancamento DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare(
            'SELECT j.*, d.nome AS desenvolvedora, p.nome AS publicadora
             FROM jogos j
             LEFT JOIN desenvolvedoras d ON d.id_desenvolvedoras = j.id_desenvolvedoras
             LEFT JOIN publicadoras p ON p.id_publicadoras = j.id_publicadoras
             WHERE j.id_jogos = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function listarCategorias() {
        return $this->db->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
    }

    public function listarDesenvolvedoras() {
        return $this->db->query('SELECT * FROM desenvolvedoras ORDER BY nome')->fetchAll();
    }

    public function listarPublicadoras() {
        return $this->db->query('SELECT * FROM publicadoras ORDER BY nome')->fetchAll();
    }

    public function buscarOuCriarDesenvolvedora($nome) {
        $stmt = $this->db->prepare('SELECT id_desenvolvedoras FROM desenvolvedoras WHERE nome = ?');
        $stmt->execute([$nome]);
        $linha = $stmt->fetch();
        if ($linha) return $linha['id_desenvolvedoras'];
        $stmt = $this->db->prepare('INSERT INTO desenvolvedoras (nome, data_fundacao, pais) VALUES (?, CURDATE(), ?)');
        $stmt->execute([$nome, 'Não informado']);
        return $this->db->lastInsertId();
    }

    public function buscarOuCriarPublicadora($nome) {
        $stmt = $this->db->prepare('SELECT id_publicadoras FROM publicadoras WHERE nome = ?');
        $stmt->execute([$nome]);
        $linha = $stmt->fetch();
        if ($linha) return $linha['id_publicadoras'];
        $stmt = $this->db->prepare('INSERT INTO publicadoras (nome, pais) VALUES (?, ?)');
        $stmt->execute([$nome, 'Não informado']);
        return $this->db->lastInsertId();
    }

    /** Cria um jogo. Aceita os ids de desenvolvedora/publicadora (admin) ou já resolvidos (criador). */
    public function criar($dados) {
        $stmt = $this->db->prepare(
            'INSERT INTO jogos (id_desenvolvedoras, id_publicadoras, titulo, capa, descricao, preco, data_lancamento,
                classificacao_etaria, tamanho_download, requisitos_minimos, requisitos_recomendados, nota_critica, tag, icone)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $dados['id_desenvolvedoras'], $dados['id_publicadoras'], $dados['titulo'], $dados['capa'] ?: null,
            $dados['descricao'], $dados['preco'], $dados['data_lancamento'], $dados['classificacao_etaria'],
            $dados['tamanho_download'], $dados['requisitos_minimos'], $dados['requisitos_recomendados'],
            $this->notaOuNull($dados['nota_critica'] ?? null),
            $dados['tag'] ?? 'Jogo',
            $dados['icone'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function atualizar($id, $dados) {
        $stmt = $this->db->prepare(
            'UPDATE jogos SET titulo=?, capa=?, descricao=?, preco=?, data_lancamento=?, classificacao_etaria=?,
             tamanho_download=?, requisitos_minimos=?, requisitos_recomendados=?, nota_critica=?, tag=?,
             id_desenvolvedoras=?, id_publicadoras=?, icone=? WHERE id_jogos=?'
        );
        $stmt->execute([
            $dados['titulo'], $dados['capa'] ?: null, $dados['descricao'], $dados['preco'], $dados['data_lancamento'],
            $dados['classificacao_etaria'], $dados['tamanho_download'], $dados['requisitos_minimos'],
            $dados['requisitos_recomendados'], $this->notaOuNull($dados['nota_critica'] ?? null),
            $dados['tag'] ?? 'Jogo',
            $dados['id_desenvolvedoras'] ?? null, $dados['id_publicadoras'] ?? null,
            $dados['icone'] ?? null,
            $id,
        ]);
    }

    private function notaOuNull($nota) {
        return ($nota === '' || $nota === null) ? null : (float) str_replace(',', '.', $nota);
    }

    public function listarPlataformas() {
        return $this->db->query('SELECT * FROM plataformas ORDER BY nome')->fetchAll();
    }

    /** Ids das categorias já vinculadas ao jogo (para marcar os checkboxes na edição). */
    public function idsCategoriasDoJogo($idJogo) {
        $stmt = $this->db->prepare('SELECT id_categorias FROM jogos_categorias WHERE id_jogos = ?');
        $stmt->execute([$idJogo]);
        return array_map('intval', array_column($stmt->fetchAll(), 'id_categorias'));
    }

    /** Ids das plataformas já vinculadas ao jogo. */
    public function idsPlataformasDoJogo($idJogo) {
        $stmt = $this->db->prepare('SELECT id_plataformas FROM jogos_plataformas WHERE id_jogos = ?');
        $stmt->execute([$idJogo]);
        return array_map('intval', array_column($stmt->fetchAll(), 'id_plataformas'));
    }

    /** Substitui as categorias do jogo pela lista recebida. */
    public function definirCategorias($idJogo, array $ids) {
        $this->db->prepare('DELETE FROM jogos_categorias WHERE id_jogos = ?')->execute([$idJogo]);
        $stmt = $this->db->prepare('INSERT IGNORE INTO jogos_categorias (id_jogos, id_categorias) VALUES (?, ?)');
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id > 0) $stmt->execute([$idJogo, $id]);
        }
    }

    /** Substitui as plataformas do jogo pela lista recebida. */
    public function definirPlataformas($idJogo, array $ids) {
        $this->db->prepare('DELETE FROM jogos_plataformas WHERE id_jogos = ?')->execute([$idJogo]);
        $stmt = $this->db->prepare('INSERT IGNORE INTO jogos_plataformas (id_jogos, id_plataformas) VALUES (?, ?)');
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id > 0) $stmt->execute([$idJogo, $id]);
        }
    }

    public function criarDesenvolvedora($nome, $dataFundacao, $pais) {
        $stmt = $this->db->prepare('INSERT INTO desenvolvedoras (nome, data_fundacao, pais) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $dataFundacao, $pais]);
        return $this->db->lastInsertId();
    }

    public function criarPublicadora($nome, $pais) {
        $stmt = $this->db->prepare('INSERT INTO publicadoras (nome, pais) VALUES (?, ?)');
        $stmt->execute([$nome, $pais]);
        return $this->db->lastInsertId();
    }

    /** Caminhos das imagens do jogo (capa e ícone), para apagar os arquivos ao remover. */
    public function buscarImagens($idJogo) {
        $stmt = $this->db->prepare('SELECT capa, icone FROM jogos WHERE id_jogos = ?');
        $stmt->execute([$idJogo]);
        return $stmt->fetch() ?: ['capa' => null, 'icone' => null];
    }

    public function buscarCapa($idJogo) {
        $stmt = $this->db->prepare('SELECT capa FROM jogos WHERE id_jogos = ?');
        $stmt->execute([$idJogo]);
        $linha = $stmt->fetch();
        return $linha ? $linha['capa'] : null;
    }

    /**
     * Remove o jogo e os vínculos dele (categorias, criador, carrinhos, bibliotecas,
     * avaliações). Não remove jogos que já têm vendas ou doações: o histórico
     * financeiro (pedidos, doações e notas fiscais) precisa ser mantido.
     */
    public function remover($id) {
        $stmt = $this->db->prepare(
            'SELECT (SELECT COUNT(*) FROM pedido_itens WHERE id_jogos = ?)
                  + (SELECT COUNT(*) FROM doacoes WHERE id_jogos = ?) AS total'
        );
        $stmt->execute([$id, $id]);
        if ((int) $stmt->fetch()['total'] > 0) {
            throw new Exception('Este jogo já tem vendas ou doações registradas e não pode ser removido, para manter o histórico.');
        }

        $this->db->beginTransaction();
        try {
            foreach (['jogos_categorias', 'jogos_criadores', 'jogos_plataformas', 'carrinho_itens', 'avaliacoes', 'biblioteca'] as $tabela) {
                $this->db->prepare("DELETE FROM $tabela WHERE id_jogos = ?")->execute([$id]);
            }
            $this->db->prepare('DELETE FROM jogos WHERE id_jogos = ?')->execute([$id]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function totalCadastrados() {
        return (int) $this->db->query('SELECT COUNT(*) AS total FROM jogos')->fetch()['total'];
    }

    /** Liga/atualiza as categorias (tags) de um jogo a partir de uma string "RPG, Ação, Indie". */
    public function definirTags($idJogo, $tagsTexto) {
        $nomes = array_filter(array_map('trim', explode(',', (string) $tagsTexto)));
        if (!$nomes) return;
        foreach ($nomes as $nome) {
            $stmt = $this->db->prepare('SELECT id_categorias FROM categorias WHERE nome = ?');
            $stmt->execute([$nome]);
            $linha = $stmt->fetch();
            $idCategoria = $linha ? $linha['id_categorias'] : null;
            if (!$idCategoria) {
                $stmt = $this->db->prepare('INSERT INTO categorias (nome) VALUES (?)');
                $stmt->execute([$nome]);
                $idCategoria = $this->db->lastInsertId();
            }
            $stmt = $this->db->prepare(
                'INSERT IGNORE INTO jogos_categorias (id_jogos, id_categorias) VALUES (?, ?)'
            );
            $stmt->execute([$idJogo, $idCategoria]);
        }
    }

    /** Vincula um jogo ao criador que o cadastrou (tabela jogos_criadores). */
    public function vincularCriador($idJogo, $idCriador) {
        $stmt = $this->db->prepare('INSERT IGNORE INTO jogos_criadores (id_jogos, id_criadores) VALUES (?, ?)');
        $stmt->execute([$idJogo, $idCriador]);
    }

    /** Lista os jogos cadastrados por um criador específico (para o dashboard dele). */
    public function listarPorCriador($idCriador) {
        $stmt = $this->db->prepare(
            'SELECT j.* FROM jogos j
             JOIN jogos_criadores jc ON jc.id_jogos = j.id_jogos
             WHERE jc.id_criadores = ?
             ORDER BY j.data_lancamento DESC'
        );
        $stmt->execute([$idCriador]);
        return $stmt->fetchAll();
    }

    /** Lista os jogos que já têm um criador vinculado (elegíveis a receber doação). */
    public function listarComCriador() {
        $stmt = $this->db->query(
            'SELECT DISTINCT j.* FROM jogos j
             JOIN jogos_criadores jc ON jc.id_jogos = j.id_jogos
             WHERE ' . condicaoIdadeSql('j.') . '
             ORDER BY j.titulo'
        );
        return $stmt->fetchAll();
    }

    /** Confere se um jogo pertence a um criador específico, antes de deixá-lo editar/remover. */
    public function pertenceAoCriador($idJogo, $idCriador) {
        $stmt = $this->db->prepare('SELECT 1 FROM jogos_criadores WHERE id_jogos = ? AND id_criadores = ?');
        $stmt->execute([$idJogo, $idCriador]);
        return (bool) $stmt->fetch();
    }
}
