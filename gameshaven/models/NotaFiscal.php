<?php
/**
 * Notas fiscais (tabela nota_fiscal).
 *
 * Uma nota é emitida automaticamente para cada venda (uma por criador que tem
 * jogos no pedido, somando só os itens dele) e para cada doação. A nota guarda
 * um retrato dos dados no momento da emissão: comprador, forma de pagamento,
 * endereço e itens. Assim ela continua correta mesmo se o cadastro mudar depois.
 */
class NotaFiscal {
    private $db;

    public function __construct() { $this->db = getConexao(); }

    // ---------- Leitura ----------

    public function buscarPorId($id) {
        $stmt = $this->db->prepare('SELECT * FROM nota_fiscal WHERE id_fiscal = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorPedidoECriador($idPedido, $idCriador) {
        $stmt = $this->db->prepare('SELECT * FROM nota_fiscal WHERE id_pedidos = ? AND id_criadores = ?');
        $stmt->execute([$idPedido, $idCriador]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorDoacao($idDoacao) {
        $stmt = $this->db->prepare('SELECT * FROM nota_fiscal WHERE id_doacoes = ?');
        $stmt->execute([$idDoacao]);
        return $stmt->fetch() ?: null;
    }

    // ---------- Emissão ----------

    /** Emite uma nota para cada criador que tem jogos neste pedido (se ainda não existir). */
    public function emitirParaPedido($idPedido) {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT jc.id_criadores
             FROM pedido_itens pi
             JOIN jogos_criadores jc ON jc.id_jogos = pi.id_jogos
             WHERE pi.id_pedidos = ?'
        );
        $stmt->execute([$idPedido]);
        $emitidas = [];
        foreach ($stmt->fetchAll() as $linha) {
            $emitidas[] = $this->emitirPedidoParaCriador($idPedido, $linha['id_criadores']);
        }
        return array_filter($emitidas);
    }

    /** Nota da venda para um criador (só os itens dele). Se já existir, devolve a existente. */
    public function emitirPedidoParaCriador($idPedido, $idCriador) {
        $existente = $this->buscarPorPedidoECriador($idPedido, $idCriador);
        if ($existente) return $existente;

        $itens = $this->itensDoPedido($idPedido, $idCriador);
        if (!$itens) return null;
        $total = 0.0;
        foreach ($itens as $item) $total += $item['subtotal'];

        $pedido = $this->dadosCompraPedido($idPedido);
        if (!$pedido) return null;

        return $this->gravar(
            ['id_pedidos' => $idPedido, 'id_criadores' => $idCriador],
            $total,
            $this->retratoComprador($pedido['id_usuarios'], $pedido['metodo'], $pedido['status_pagamento'])
        );
    }

    /** Nota da doação (cada doação tem a sua). */
    public function emitirParaDoacao($idDoacao) {
        $existente = $this->buscarPorDoacao($idDoacao);
        if ($existente) return $existente;

        $stmt = $this->db->prepare('SELECT id_usuarios, id_criadores, valor, metodo, status FROM doacoes WHERE id_doacoes = ?');
        $stmt->execute([$idDoacao]);
        $doacao = $stmt->fetch();
        if (!$doacao) return null;

        return $this->gravar(
            ['id_doacoes' => $idDoacao, 'id_criadores' => $doacao['id_criadores']],
            (float) $doacao['valor'],
            $this->retratoComprador($doacao['id_usuarios'], $doacao['metodo'], $doacao['status'])
        );
    }

    /** Emite as notas que ainda faltam (vendas e doações antigas). Devolve quantas criou. */
    public function emitirPendentes() {
        $criadas = 0;
        $stmt = $this->db->query('SELECT DISTINCT id_pedidos FROM pedido_itens');
        foreach ($stmt->fetchAll() as $linha) {
            $antes = $this->contarNotas();
            $this->emitirParaPedido($linha['id_pedidos']);
            $criadas += $this->contarNotas() - $antes;
        }
        $stmt = $this->db->query('SELECT id_doacoes FROM doacoes');
        foreach ($stmt->fetchAll() as $linha) {
            if (!$this->buscarPorDoacao($linha['id_doacoes'])) {
                if ($this->emitirParaDoacao($linha['id_doacoes'])) $criadas++;
            }
        }
        return $criadas;
    }

    /** Preenche o retrato dos dados em todas as notas antigas que ainda não o têm. */
    public function completarRetratos() {
        foreach ($this->db->query('SELECT * FROM nota_fiscal WHERE comprador_nome IS NULL')->fetchAll() as $nota) {
            $this->completarRetrato($nota);
        }
    }

    /** Preenche o retrato de uma nota, se ela ainda não tiver. */
    public function completarRetrato(array $nota) {
        if (!empty($nota['comprador_nome'])) return;
        if ($nota['id_pedidos']) {
            $pedido = $this->dadosCompraPedido($nota['id_pedidos']);
            if (!$pedido) return;
            $retrato = $this->retratoComprador($pedido['id_usuarios'], $pedido['metodo'], $pedido['status_pagamento']);
        } else {
            $stmt = $this->db->prepare('SELECT id_usuarios, metodo, status FROM doacoes WHERE id_doacoes = ?');
            $stmt->execute([$nota['id_doacoes']]);
            $d = $stmt->fetch();
            if (!$d) return;
            $retrato = $this->retratoComprador($d['id_usuarios'], $d['metodo'], $d['status']);
        }
        $this->atualizarRetrato($nota['id_fiscal'], $retrato);
    }

    /** Nome (nickname) do criador de uma nota. */
    public function nomeCriador($idCriador) {
        $stmt = $this->db->prepare(
            'SELECT u.nickname FROM criadores c JOIN usuarios u ON u.id_usuarios = c.id_usuarios WHERE c.id_criadores = ?'
        );
        $stmt->execute([$idCriador]);
        $l = $stmt->fetch();
        return $l ? $l['nickname'] : 'Criador #' . $idCriador;
    }

    // ---------- Itens ----------

    /** Itens de um pedido; se $idCriador for informado, só os jogos desse criador. */
    public function itensDoPedido($idPedido, $idCriador = null) {
        $sql = 'SELECT pi.id_jogos, j.titulo, pi.preco_unitario AS preco, pi.quantidade
                FROM pedido_itens pi
                JOIN jogos j ON j.id_jogos = pi.id_jogos';
        $params = [$idPedido];
        if ($idCriador !== null) {
            $sql .= ' JOIN jogos_criadores jc ON jc.id_jogos = pi.id_jogos AND jc.id_criadores = ?';
            $params = [$idCriador, $idPedido];
        }
        $sql .= ' WHERE pi.id_pedidos = ? ORDER BY j.titulo';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $linhas = [];
        foreach ($stmt->fetchAll() as $l) {
            $quantidade = max(1, (int) $l['quantidade']);
            $linhas[] = [
                'id_jogos' => $l['id_jogos'],
                'titulo' => $l['titulo'],
                'quantidade' => $quantidade,
                'preco' => (float) $l['preco'],
                'subtotal' => round((float) $l['preco'] * $quantidade, 2),
            ];
        }
        return $linhas;
    }

    /** Itens de uma nota para exibição (venda: jogos do criador; doação: a contribuição). */
    public function itensDaNota($nota) {
        if ($nota['id_pedidos']) {
            return $this->itensDoPedido($nota['id_pedidos'], $nota['id_criadores']);
        }
        $stmt = $this->db->prepare(
            'SELECT d.valor, d.id_jogos, j.titulo FROM doacoes d JOIN jogos j ON j.id_jogos = d.id_jogos WHERE d.id_doacoes = ?'
        );
        $stmt->execute([$nota['id_doacoes']]);
        $d = $stmt->fetch();
        if (!$d) return [];
        return [[
            'id_jogos' => $d['id_jogos'],
            'titulo' => 'Doação ao criador pelo jogo ' . $d['titulo'],
            'quantidade' => 1,
            'preco' => (float) $d['valor'],
            'subtotal' => (float) $d['valor'],
        ]];
    }

    // ---------- Listagem, filtros e análise ----------

    /**
     * Notas com filtros. $filtros aceita: tipo ('venda'|'doacao'), metodo, de, ate (Y-m-d),
     * numero (parte do número). $idCriador restringe às notas de um criador.
     */
    public function listar(array $filtros = [], $idCriador = null) {
        [$where, $params] = $this->condicoes($filtros, $idCriador);
        $stmt = $this->db->prepare(
            'SELECT n.*,
                    (SELECT u.nickname FROM criadores c JOIN usuarios u ON u.id_usuarios = c.id_usuarios
                     WHERE c.id_criadores = n.id_criadores) AS criador_nome,
                    (SELECT COUNT(*) FROM pedido_itens pi
                     JOIN jogos_criadores jc ON jc.id_jogos = pi.id_jogos AND jc.id_criadores = n.id_criadores
                     WHERE pi.id_pedidos = n.id_pedidos) AS qtd_itens
             FROM nota_fiscal n
             WHERE ' . $where . '
             ORDER BY n.data_emissao DESC, n.id_fiscal DESC'
        );
        $stmt->execute($params);
        $linhas = $stmt->fetchAll();
        foreach ($linhas as &$l) {
            if ($l['id_doacoes']) $l['qtd_itens'] = 1;
        }
        return $linhas;
    }

    /** Números para a análise: totais, por tipo, forma de pagamento, mês, criador e jogos mais vendidos. */
    public function resumo(array $filtros = [], $idCriador = null, $incluirCriadores = false) {
        $notas = $this->listar($filtros, $idCriador);
        $r = [
            'quantidade' => count($notas), 'valor_total' => 0.0, 'ticket_medio' => 0.0,
            'vendas' => ['quantidade' => 0, 'valor' => 0.0], 'doacoes' => ['quantidade' => 0, 'valor' => 0.0],
            'por_metodo' => [], 'por_mes' => [], 'por_criador' => [], 'top_jogos' => [],
        ];
        foreach ($notas as $n) {
            $valor = (float) $n['valor_total'];
            $r['valor_total'] += $valor;
            $tipo = $n['id_pedidos'] ? 'vendas' : 'doacoes';
            $r[$tipo]['quantidade']++;
            $r[$tipo]['valor'] += $valor;

            $metodo = $n['forma_pagamento'] ?: 'não informado';
            $r['por_metodo'][$metodo] = ($r['por_metodo'][$metodo] ?? ['quantidade' => 0, 'valor' => 0.0]);
            $r['por_metodo'][$metodo]['quantidade']++;
            $r['por_metodo'][$metodo]['valor'] += $valor;

            $mes = substr($n['data_emissao'], 0, 7);
            $r['por_mes'][$mes] = ($r['por_mes'][$mes] ?? ['quantidade' => 0, 'valor' => 0.0]);
            $r['por_mes'][$mes]['quantidade']++;
            $r['por_mes'][$mes]['valor'] += $valor;

            if ($incluirCriadores) {
                $nome = $n['criador_nome'] ?: 'Criador #' . $n['id_criadores'];
                $r['por_criador'][$nome] = ($r['por_criador'][$nome] ?? ['quantidade' => 0, 'valor' => 0.0]);
                $r['por_criador'][$nome]['quantidade']++;
                $r['por_criador'][$nome]['valor'] += $valor;
            }
        }
        $r['ticket_medio'] = $r['quantidade'] ? $r['valor_total'] / $r['quantidade'] : 0.0;
        ksort($r['por_mes']);
        $r['por_mes'] = array_slice($r['por_mes'], -12, null, true);
        uasort($r['por_metodo'], fn($a, $b) => $b['valor'] <=> $a['valor']);
        if ($incluirCriadores) uasort($r['por_criador'], fn($a, $b) => $b['valor'] <=> $a['valor']);
        $r['top_jogos'] = $this->topJogos($filtros, $idCriador);
        return $r;
    }

    /** Jogos mais vendidos (venda, por valor), considerando só os itens do criador de cada nota. */
    private function topJogos(array $filtros, $idCriador) {
        [$where, $params] = $this->condicoes($filtros, $idCriador, 'n.');
        $stmt = $this->db->prepare(
            'SELECT j.titulo, SUM(pi.quantidade) AS quantidade, SUM(pi.preco_unitario * pi.quantidade) AS valor
             FROM nota_fiscal n
             JOIN pedido_itens pi ON pi.id_pedidos = n.id_pedidos
             JOIN jogos_criadores jc ON jc.id_jogos = pi.id_jogos AND jc.id_criadores = n.id_criadores
             JOIN jogos j ON j.id_jogos = pi.id_jogos
             WHERE n.id_pedidos IS NOT NULL AND ' . $where . '
             GROUP BY j.id_jogos, j.titulo
             ORDER BY valor DESC
             LIMIT 5'
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Lista de criadores (para o filtro do admin). */
    public function criadores() {
        return $this->db->query(
            'SELECT c.id_criadores, u.nickname FROM criadores c JOIN usuarios u ON u.id_usuarios = c.id_usuarios ORDER BY u.nickname'
        )->fetchAll();
    }

    // ---------- Internos ----------

    private function condicoes(array $f, $idCriador, $alias = '') {
        $where = ['1 = 1'];
        $params = [];
        if ($idCriador !== null) { $where[] = "{$alias}id_criadores = ?"; $params[] = $idCriador; }
        if (($f['tipo'] ?? '') === 'venda') $where[] = "{$alias}id_pedidos IS NOT NULL";
        if (($f['tipo'] ?? '') === 'doacao') $where[] = "{$alias}id_doacoes IS NOT NULL";
        if (!empty($f['metodo'])) { $where[] = "{$alias}forma_pagamento = ?"; $params[] = $f['metodo']; }
        if (!empty($f['de'])) { $where[] = "DATE({$alias}data_emissao) >= ?"; $params[] = $f['de']; }
        if (!empty($f['ate'])) { $where[] = "DATE({$alias}data_emissao) <= ?"; $params[] = $f['ate']; }
        if (!empty($f['numero'])) { $where[] = "{$alias}numero_nota LIKE ?"; $params[] = '%' . addcslashes($f['numero'], '%_\\') . '%'; }
        if (!empty($f['criador']) && $idCriador === null) { $where[] = "{$alias}id_criadores = ?"; $params[] = (int) $f['criador']; }
        return [implode(' AND ', $where), $params];
    }

    private function dadosCompraPedido($idPedido) {
        $stmt = $this->db->prepare(
            'SELECT p.id_usuarios, pg.metodo, pg.status AS status_pagamento
             FROM pedidos p LEFT JOIN pagamentos pg ON pg.id_pedidos = p.id_pedidos
             WHERE p.id_pedidos = ?'
        );
        $stmt->execute([$idPedido]);
        return $stmt->fetch() ?: null;
    }

    /** Dados do comprador/endereço no momento da emissão. */
    private function retratoComprador($idUsuario, $forma, $status) {
        $stmt = $this->db->prepare('SELECT nickname, email FROM usuarios WHERE id_usuarios = ?');
        $stmt->execute([$idUsuario]);
        $u = $stmt->fetch() ?: ['nickname' => '', 'email' => ''];
        $stmt = $this->db->prepare('SELECT rua, numero, complemento, cidade, estado, cep FROM enderecos WHERE id_usuarios = ?');
        $stmt->execute([$idUsuario]);
        $e = $stmt->fetch() ?: [];
        return [
            'comprador_nome' => $u['nickname'],
            'comprador_email' => $u['email'],
            'forma_pagamento' => $forma,
            'status_pagamento' => $status,
            'end_rua' => $e['rua'] ?? null,
            'end_numero' => $e['numero'] ?? null,
            'end_complemento' => $e['complemento'] ?? null,
            'end_cidade' => $e['cidade'] ?? null,
            'end_estado' => $e['estado'] ?? null,
            'end_cep' => $e['cep'] ?? null,
        ];
    }

    private function atualizarRetrato($idFiscal, array $r) {
        $stmt = $this->db->prepare(
            'UPDATE nota_fiscal SET comprador_nome = ?, comprador_email = ?, forma_pagamento = ?, status_pagamento = ?,
                    end_rua = ?, end_numero = ?, end_complemento = ?, end_cidade = ?, end_estado = ?, end_cep = ?
             WHERE id_fiscal = ?'
        );
        $stmt->execute([
            $r['comprador_nome'], $r['comprador_email'], $r['forma_pagamento'], $r['status_pagamento'],
            $r['end_rua'], $r['end_numero'], $r['end_complemento'], $r['end_cidade'], $r['end_estado'], $r['end_cep'],
            $idFiscal,
        ]);
    }

    /** Grava a nota: número temporário -> id -> número definitivo, com chave de acesso (simulada). */
    private function gravar(array $origem, $valor, array $retrato) {
        $numeroTemp = 'TMP' . bin2hex(random_bytes(8));
        $stmt = $this->db->prepare(
            'INSERT INTO nota_fiscal (id_pedidos, id_doacoes, id_criadores, numero_nota, valor_produtos, valor_total, status,
                    comprador_nome, comprador_email, forma_pagamento, status_pagamento,
                    end_rua, end_numero, end_complemento, end_cidade, end_estado, end_cep)
             VALUES (?, ?, ?, ?, ?, ?, "autorizada", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $origem['id_pedidos'] ?? null, $origem['id_doacoes'] ?? null, $origem['id_criadores'],
            $numeroTemp, $valor, $valor,
            $retrato['comprador_nome'], $retrato['comprador_email'], $retrato['forma_pagamento'], $retrato['status_pagamento'],
            $retrato['end_rua'], $retrato['end_numero'], $retrato['end_complemento'], $retrato['end_cidade'],
            $retrato['end_estado'], $retrato['end_cep'],
        ]);
        $id = $this->db->lastInsertId();
        // Chave de acesso de 44 dígitos (formato da NF-e). É SIMULADA: não é uma chave válida na SEFAZ.
        $aleatorio = '';
        for ($i = 0; $i < 20; $i++) $aleatorio .= random_int(0, 9);
        $chave = date('YmdHis') . str_pad($id, 10, '0', STR_PAD_LEFT) . $aleatorio;
        $stmt = $this->db->prepare('UPDATE nota_fiscal SET numero_nota = ?, chave_acesso = ? WHERE id_fiscal = ?');
        $stmt->execute(['NF' . str_pad($id, 8, '0', STR_PAD_LEFT), substr($chave, 0, 44), $id]);
        return $this->buscarPorId($id);
    }

    private function contarNotas() {
        return (int) $this->db->query('SELECT COUNT(*) FROM nota_fiscal')->fetchColumn();
    }
}
