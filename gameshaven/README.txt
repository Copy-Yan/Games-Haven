GAMESHAVEN - Loja de jogos online (PHP puro + MySQL, arquitetura MVC)
=======================================================================

SE APARECER "O banco de dados precisa ser atualizado"
-----------------------------------------------------
Isso significa que o banco ainda não tem as colunas desta versão. Não é
erro do site: rode o atualizar_banco.sql (instruções abaixo) e recarregue
a página. O site mostra essa mensagem em vez de erro fatal, e a checagem
só roda até o banco estar atualizado.

ATUALIZAÇÃO - já tem o banco "gameshaven" criado de antes?
-------------------------------------------------------------
Rode o arquivo atualizar_banco.sql (ele está nesta pasta) no phpMyAdmin:
selecione o banco "gameshaven", aba SQL, cole o conteúdo do arquivo e
clique em Executar. Não precisa apagar nada. O script é seguro de rodar
mais de uma vez: cada passo confere antes de alterar algo.

Ele adiciona as colunas que faltavam (jogos.capa, jogos.nota_critica,
usuarios.is_admin, nota_fiscal.id_criadores), corrige as restrições que
impediam o funcionamento (um pedido por usuário, uma doação por
doador/jogo, nota fiscal que exigia pedido e doação ao mesmo tempo) e
amplia telefone e conta bancária do criador.

Se preferir começar do zero, apague o banco e importe o database.sql
inteiro: o resultado é o mesmo.

PAINEL DO CRIADOR (novo)
----------------------------
Quem já é Criador (Perfil > Tornar-se um Criador) agora tem um link
"Painel do Criador" na tela de Perfil, com:
- lista dos jogos que ele mesmo cadastrou;
- "Adicionar Jogo", um formulário igual ao do admin, que salva o jogo
  direto na tabela "jogos" (o mesmo catálogo que a Home usa) e vincula
  o jogo ao criador pela tabela jogos_criadores (que já existia no seu
  schema e não estava sendo usada em lugar nenhum). Assim que o jogo é
  enviado, ele já aparece na página inicial para todo mundo.
Quem tenta acessar index.php?page=criador sem ser criador é mandado
para a tela de cadastro de criador.

PAGAMENTOS E DOAÇÕES (novo)
----------------------------
- O checkout (Carrinho > Fechar Pedido > Pagamento) agora valida de verdade
  os dados conforme a forma escolhida: Pix exige nome do cliente; Cartão de
  Crédito/Débito exige número, nome impresso, validade (MM/AAAA, não pode
  estar vencida) e CVV. Antes esses campos do cartão nem tinham "name" no
  HTML - eram digitados e ignorados. Nenhum dado de cartão é salvo no banco
  (não existe coluna pra isso, e é a prática correta); a validação só serve
  pra simular a aprovação, como no fluxo original.
- O botão "FAZER DOAÇÃO" do Carrinho estava indo para a Home (link solto,
  não fazia nada). Agora leva a uma tela "Fazer Doação" que lista os jogos
  com criador cadastrado; escolhendo um, abre um formulário igual ao de
  Pagamento (Pix ou Cartão), com o valor da doação editável pelo doador.
- O botão "Doar" dentro da página do jogo levava direto pra esse mesmo
  formulário de doação, em vez do antigo clique único que sempre doava
  R$10,00 fixos via Pix sem escolha de forma de pagamento.

DETALHAMENTO DOS JOGOS (novo, Painel do Criador)
----------------------------------------------------
O botão que antes era "Meus Dados de Criador" agora é "Detalhamento dos
Jogos". Ele mostra, pra cada jogo do criador:
- Compras: quem comprou, valor, forma de pagamento (Pix/Cartão) e data.
- Doações: quem doou, valor, forma de pagamento e data.
Em cada linha tem "Ver Nota Fiscal", que mostra (emitindo na hora, se ainda
não existir) a nota fiscal daquela venda ou doação - usando a tabela
nota_fiscal do seu schema original, que até então não estava sendo usada
em lugar nenhum. Um criador só consegue ver a nota fiscal de vendas/doações
dos próprios jogos (conferido no servidor, não só escondido na tela).

BUG CORRIGIDO: nota_fiscal exigia id_pedidos NOT NULL **e** id_doacoes NOT
NULL ao mesmo tempo - impossível emitir nota só de uma compra ou só de uma
doação, que é o caso normal. Corrigido via migração no final do
atualizar_banco.sql (mesmo tipo de ajuste já feito antes com Criadores/doacoes).

ADMIN - ADICIONAR E EDITAR JOGOS (nesta versão)
-----------------------------------------------
Os formulários de Adicionar Jogo e Editar Jogo seguem os campos do
GameStormy:
- Capa: envio de imagem (JPG, PNG, GIF ou WEBP, até 5 MB). Os arquivos
  ficam em public/uploads/capas/. Na edição, deixar o campo vazio mantém
  a capa atual; enviar uma nova substitui e apaga a antiga.
- Desenvolvedora e Publicadora: listas (dropdown) com os cadastrados.
  Se faltar alguma, use "+ Cadastrar nova desenvolvedora/publicadora"
  logo abaixo da lista.
- Tag: a mesma lista do GameStormy (Jogo, Destaque, Indie, RPG...).
- Nota crítica: de 0 a 10.
- Categorias e plataformas: caixas de seleção (várias).
Na lista de jogos (Painel Administrativo > Gerenciar todos os Jogos)
há os botões Editar e Remover. A capa aparece na lista, na Home e na
página do jogo. Jogos com vendas ou doações não podem ser removidos,
para manter o histórico.

A atualização do banco (atualizar_banco.sql) acrescenta a coluna tag
e cadastra plataformas e categorias iniciais, sem duplicar nada.

PERFIL (nesta versão, igual ao GameStormy)
------------------------------------------
Em "Meu Perfil" (ícone de usuário no topo) você pode:
- trocar a foto de perfil (JPG, PNG, GIF ou WEBP, até 2 MB). A foto
  aparece na tela de perfil e no ícone do topo do site. A foto antiga
  é apagada do servidor ao trocar.
- alterar nome de usuário, nickname, e-mail e data de nascimento. Nome,
  nickname e e-mail não podem repetir os de outra conta.
- trocar a senha. Para isso, informe a senha atual (o GameStormy não
  pede, mas aqui pedimos por segurança).
As fotos ficam em public/uploads/avatares/. A coluna usuarios.foto_perfil
é criada pelo atualizar_banco.sql.

VISUAL (nesta versão, baseado no GameStormy)
--------------------------------------------
O arquivo public/css/style.css foi redesenhado com a mesma paleta e
os mesmos componentes do GameStormy: navbar roxa com botões em pílula,
fundo cinza escuro, cards de jogo, painéis roxos com campos azul-claros,
botões arredondados, tabelas e alertas. As classes usadas pelas telas
continuam as mesmas, então nenhuma tela precisou ser reescrita.
O CSS anterior ficou guardado em public/css/style_anterior.css (não é
carregado pelo site). Para voltar ao visual antigo, troque o conteúdo
de style.css por ele.
Também corrigido: o rodapé mostrava "Gamestormy" como nome da marca; agora
mostra "GamesHaven".

ESTRUTURA DO CSS (nesta versão)
--------------------------------
O CSS fica separado por área em public/css/:
- base.css: cores, topo, rodapé, botões, alertas, painéis e tabelas.
  É carregado em todas as telas.
- auth.css: login, cadastro e tornar-se criador (classes auth-page e auth-card).
- home.css: página inicial (vitrine e cards de jogo).
- jogo.css: página do jogo.
- checkout.css: carrinho, pagamento, doação e confirmação.
- biblioteca.css: biblioteca do usuário.
- suporte.css: suporte (classes support-page e support-card).
- sobre.css: página sobre.
- perfil.css: meu perfil.
- admin.css: painel do admin e do criador.
- style_anterior.css: CSS original que veio no seu zip (não é carregado).
Cada tela carrega base.css e o arquivo da sua área. Para mudar a aparência
de uma tela só, edite apenas o arquivo dela.

ESTILOS (CSS separado por página)
---------------------------------
Cada página carrega dois arquivos em public/css/:
- base.css: cores, topo (menu e busca), rodapé, botões, painéis,
  alertas e tabelas. Usado por todas as telas.
- o CSS da própria página:
    login, cadastro e cadastro de criador  -> auth.css
    Home (vitrine e cards)                 -> home.css
    Página do jogo                         -> jogo.css
    Carrinho, doação, pagamento e pedido   -> checkout.css
    Biblioteca                             -> biblioteca.css
    Meu perfil                             -> perfil.css
    Suporte                                -> suporte.css
    Sobre                                  -> sobre.css
    Painel admin e painel do criador       -> admin.css
Para mudar a aparência de uma tela, edite só o arquivo dela. O arquivo
style_anterior.css é o CSS original do zip e não é carregado.

HOME: DESTAQUE E NOVIDADES
--------------------------
O bloco do topo da Home mostra o jogo em destaque (o de maior nota crítica;
se nenhum tiver nota, o mais recente), com capa, descrição, preço e botão
"Ver jogo". Ao lado, "Novidades da loja" lista os 3 lançamentos mais
recentes com miniatura. Para o destaque aparecer com imagem, cadastre a
capa do jogo no Painel Administrativo (ou no Painel do Criador).

BIBLIOTECA
----------
Os jogos da biblioteca usam os mesmos cards da Home (capa, título e
preço), e o card inteiro leva à página do jogo. Esse estilo fica no
base.css, então a Home e a Biblioteca sempre ficam iguais.

PAINEL DO CRIADOR: ADICIONAR JOGO
---------------------------------
O formulário de Adicionar Jogo do criador é igual ao do admin: capa com
envio de imagem, desenvolvedora e publicadora em lista (com link para
cadastrar uma nova), tag, nota crítica, categorias e plataformas. O
criador também pode cadastrar desenvolvedoras e publicadoras, pelos
mesmos links do formulário. Os jogos enviados por ele ficam vinculados
à sua conta e aparecem na Home para todos.

ENDEREÇO NO PAGAMENTO E NA DOAÇÃO
--------------------------------
O pagamento e a doação agora pedem o endereço, com os campos da tabela
enderecos: rua, número, complemento (opcional), cidade, estado (UF) e CEP
(8 dígitos). Ao enviar, o endereço é salvo na conta do usuário. Como a
tabela tem um endereço por usuário, enviar de novo atualiza o mesmo
registro, sem duplicar. Os campos vêm preenchidos com o que está no banco.

CEP AUTOMÁTICO (ViaCEP)
-----------------------
Na seção de endereço do pagamento e da doação, ao digitar o CEP (8 dígitos)
o site consulta a API pública do ViaCEP (https://viacep.com.br) e preenche
rua, cidade e UF. Depois o usuário confere e informa o número. A consulta é
feita pelo arquivo public/js/endereco.js, que já existia no projeto. Sem
internet ou se o CEP não existir, os campos continuam editáveis à mão.
O bairro é mostrado no aviso, mas não é salvo, porque a tabela enderecos não
tem essa coluna.

ÍCONE PEQUENO DO JOGO
---------------------
Além da capa, cada jogo pode ter um ícone pequeno (miniatura). O campo
"Ícone do jogo" é opcional, nos formulários de Adicionar e Editar do admin
e do criador (JPG, PNG, GIF ou WEBP, até 1 MB). O ícone aparece nas listas
da Home (Jogos em destaque, Mais jogados, Pré-Lançamentos e Novidades da loja)
e nas tabelas do painel do admin e do criador. Se o jogo não tiver ícone, a
lista usa a capa; se não tiver nenhuma das duas, aparece um quadrado cinza.
A imagem só é mostrada se o arquivo existir no servidor, então não aparece
mais ícone de imagem quebrada. A atualização do banco (atualizar_banco.sql)
acrescenta a coluna jogos.icone.

PESQUISA DE JOGOS
-----------------
A barra "Pesquisar jogo" do topo leva a uma página de resultados que mostra
só os nomes dos jogos encontrados (sem a vitrine da Home). Os jogos cujo
nome começa com o termo aparecem primeiro. A busca não diferencia maiúsculas
de minúsculas, e os caracteres % e _ são tratados como texto comum. Se o
campo estiver vazio, a página pede para digitar um nome. Cada nome leva à
página do jogo. Estilo em public/css/busca.css.

PRÉ-LANÇAMENTOS E RECÉM CADASTRADOS (Home)
------------------------------------------
A seção "Pré-Lançamentos" mostra jogos cuja data de lançamento ainda não
chegou. Como jogos novos costumam ser cadastrados já com data passada, essa
seção pode ficar vazia. Por isso, quando não houver pré-lançamento, a
seção mostra "Recém cadastrados": os 3 jogos enviados por último (admin ou
criador), do mais novo para o mais antigo. O título muda conforme o caso,
para não aparentar que é um pré-lançamento. A atualização do banco
(atualizar_banco.sql) acrescenta a coluna jogos.cadastrado_em.

LAYOUT DO PAGAMENTO E DA DOAÇÃO
-------------------------------
Dados (do comprador ou do doador) e Endereço ficam lado a lado. A forma de
pagamento ocupa a largura toda. O resumo do pedido (ou da doação) fica
centralizado, e os botões ficam no centro, ao final. Em telas estreitas, os
blocos voltam a ficar um embaixo do outro. Na doação, o resumo traz o nome
do jogo e o campo do valor. Estilos em public/css/checkout.css.

VALIDAÇÃO DE SENHA NO CADASTRO (ManageEngine PAM360)
--------------------------------------------------------
No cadastro, a senha é validada pela política de senhas da organização
no ManageEngine PAM360 (API Password Validator). A conta só é criada se a
senha for aprovada.

Para ativar:
1. Gere um AUTHTOKEN no PAM360 para um usuário com acesso à API.
2. Abra config/pam.php e preencha:
   - 'host' e 'porta' do servidor PAM360;
   - 'authtoken' (o token gerado no passo 1);
   - 'politica_nome' (nome da política de senha, ex.: Strong);
   - 'ativo' => true.
3. Escolha 'se_indisponivel':
   - 'bloquear' (recomendado): se o PAM360 não responder, o cadastro
     fica bloqueado até a validação voltar;
   - 'permitir': se o PAM360 não responder, o cadastro segue sem validar.

A consulta é feita pelo servidor (PHP), em includes/pam.php. O AUTHTOKEN
nunca chega ao navegador. Não há arquivo .js nem .json novo. Enquanto
'ativo' estiver false, o cadastro funciona sem consultar o PAM360.

Observações:
- A mensagem de recusa vem do próprio PAM360, no idioma configurado nele.
- O PAM360 recebe a senha digitada no cadastro, via HTTPS. Use https em
  produção e não ative 'verificar_certificado' => false, exceto em teste.
- Foi testado com um servidor simulado que segue a resposta documentada
  (message "Valid", status "Success" para senha aprovada). Confira o formato
  de recusa no seu PAM360 antes de usar em produção.

CARRINHO
--------
A miniatura de cada item no carrinho agora é o ícone do jogo (ou a capa, se
não houver ícone). Os botões de ação viraram caixas: "Escolher mais produtos"
(contorno), "Pagar Normalmente" (roxo) e "Fazer Doação" (rosa). Estilos em
public/css/checkout.css.

CLASSIFICAÇÃO ETÁRIA (filtro por idade)
---------------------------------------
Cada jogo tem uma classificação: L (livre), 10, 12, 14, 16 ou 18. O usuário só
vê os jogos com classificação igual ou menor que a idade dele, calculada pela
data de nascimento cadastrada. Por exemplo, quem tem 15 anos vê L, 10, 12 e 14.

- Visitantes (sem login) veem só os jogos L, porque a idade é desconhecida.
- Administradores não têm restrição, para poderem gerir todo o catálogo.
- O filtro vale na Home (todas as seções), na busca, na biblioteca e na
  Pagamento: jogos fora da faixa não aparecem. A página do jogo, adicionar
  ao carrinho e doar também são bloqueados, mesmo digitando o endereço direto.
- Se houver jogo fora da faixa no carrinho, o pagamento é bloqueado até ele
  ser removido.
- A regra está em includes/idade.php.

VERIFICAÇÃO reCAPTCHA ENTERPRISE (entrada no site)
--------------------------------------------------
Com a verificação ativa, o site não abre antes de o visitante passar na
verificação do Google reCAPTCHA Enterprise. A validação é feita pela API
recaptchaenterprise.googleapis.com, no servidor (includes/recaptcha.php).

A verificação LIGA SOZINHA quando você preenche as três chaves em
config/recaptcha.php: project_id, api_key e site_key. Não é preciso mexer
em mais nada. Ao abrir o site, o visitante é verificado automaticamente e
segue para a Home. Depois de aprovado, fica liberado durante a sessão.

Chaves a preencher (no Google Cloud):
- site_key: chave do site reCAPTCHA Enterprise (pública; aparece na tela de verificação);
- api_key: chave de API restrita ao reCAPTCHA Enterprise (só no servidor);
- project_id: ID do projeto.

Para DESLIGAR mesmo com as chaves preenchidas, use 'ativo' => false.
Sem as chaves, o site abre normalmente, sem verificação.

Observações:
- A api_key nunca vai para o navegador.
- Se o Google não responder, o site não libera a entrada. Não há modo
  "permitir" nesta versão, de propósito.
- A ação esperada é 'HOME', e o servidor confere se o token foi feito para ela.
- Se o script do Google não carregar (rede ou bloqueador de anúncios), após
  alguns segundos aparece uma mensagem e o botão "Continuar".
- Em teste no localhost, confirme no Google Cloud que o domínio está permitido
  na chave do site.

NOTAS FISCAIS (análise, detalhes e impressão)
---------------------------------------------
Emissão automática: ao concluir uma compra, a loja emite uma nota para cada
criador que tem jogos no pedido (somando só os itens dele). Ao concluir uma
doação, emite a nota da doação. A nota guarda um retrato dos dados na emissão
(comprador, forma de pagamento, endereço e itens), então continua correta
mesmo se o cadastro mudar depois.

Onde ver:
- Admin: painel administrativo > "Notas fiscais". Mostra todas as notas, com
  indicadores (quantidade, valor total, ticket médio, vendas e doações), filtros
  (tipo, forma de pagamento, período, número, criador), análise por forma de
  pagamento, por mês, por criador e jogos mais vendidos.
- Criador: painel do criador > "Minhas notas fiscais" (somente as dele).
- Detalhes: cada nota abre uma página com o documento completo (emitente,
  destinatário, itens com quantidade, totais e aviso). O botão "Imprimir /
  Salvar em PDF" gera a impressão em A4, sem menu nem rodapé do site.
- "Emitir notas pendentes" (admin): cria as notas que faltam para vendas e
  doações antigas.

Dados do emitente: preencha config/loja.php com os dados reais (razão social,
CNPJ, endereço e o aviso do rodapé).

Importante:
- A chave de acesso é simulada (44 dígitos, formato da NF-e). Ela NÃO é uma
  chave válida na SEFAZ, e o documento não tem validade fiscal. Para emitir
  nota oficial é preciso integrar com um emissor fiscal autorizado.
- A quantidade passou a ser gravada nos itens do pedido. Pedidos feitos antes
  dessa atualização ficam com quantidade 1 (a quantidade anterior não foi salva).
- Rode o atualizar_banco.sql deste zip para criar as colunas novas.

CORREÇÕES DESTA RODADA (testadas num MariaDB com o fluxo completo)
----------------------------------------------------------------------
- Erro "Unknown column nota_critica": a migração não tinha sido aplicada.
  Agora está no atualizar_banco.sql, que pode ser rodado sem erro.
- Tabelas com nomes em maiúsculo/minúsculo misturados: o código usa
  minúsculo, então o schema foi padronizado (funcionava só no Windows).
- Um usuário só conseguia um pedido na vida: a 2ª compra apagava a 1ª.
  Agora cada compra é um pedido novo, e o histórico fica completo.
- Doações repetidas do mesmo doador para o mesmo jogo eram somadas numa
  linha só. Agora cada doação é um registro, com sua nota fiscal.
- Cadastro de criador falhava (valores fora de ordem no INSERT).
- Telefone de celular (11 dígitos) era cortado para 10.
- Nota fiscal da venda somava o pedido inteiro. Agora soma só os jogos
  do criador que está vendo a nota.
- Remover jogo quebrava com erro do banco. Agora limpa os vínculos, e
  jogos com vendas ou doações não são removidos (aparece a mensagem).

BUGS CORRIGIDOS NESTA REVISÃO
----------------------------------
- jogos tinha UNIQUE(id_desenvolvedoras, id_publicadoras): travava o
  cadastro de um 2º jogo do mesmo estúdio+editora. Corrigido via
  atualizar_banco.sql (ver seção "ATUALIZAÇÃO" acima).
- Os campos "Capa" e "Nota Crítica" existiam nos formulários de
  Adicionar/Editar Jogo mas eram digitados e descartados (não havia
  coluna no banco nem código salvando). Agora são persistidos.
- O campo "Tags" do formulário de jogo também era descartado; agora
  vira categoria (tabela categorias/jogos_categorias), reaproveitando
  categorias já existentes pelo nome.

Todo o projeto é PHP puro - nenhuma linha de JavaScript em lugar
nenhum (já revisei o código-fonte procurando por <script>, onclick,
onchange etc. e não há nenhum).
