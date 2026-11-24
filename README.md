<div align="center">

# 🍄 GamesHaven 🎮

**Plataforma web de venda de jogos digitais com foco em dar visibilidade a desenvolvedores independentes.**

[**Acessar o sistema online**](https://SEU-LINK-AQUI) 

</div>

---

## Sumário

- [Sobre o projeto](#sobre-o-projeto)
- [Acesso ao sistema](#acesso-ao-sistema)
- [Funcionalidades](#funcionalidades)
- [Perfis de usuário](#perfis-de-usuário)
- [Tecnologias](#tecnologias)
- [Arquitetura](#arquitetura)
- [Estrutura de pastas](#estrutura-de-pastas)
- [Execução local](#execução-local)
- [Publicação em hospedagem](#publicação-em-hospedagem)
- [Equipe](#equipe)

---

## 🍄 Sobre o projeto

O **GamesHaven** é uma plataforma de jogos digitais desenvolvida com a arquitetura **MVC (Model-View-Controller)**, utilizando **PHP** e **MySQL**.

O projeto tem dois objetivos: **vender jogos** e **ajudar criadores independentes a publicar seus trabalhos e ganhar visibilidade**. Para isso, aplica boas práticas de organização de código, separação de responsabilidades e integração com banco de dados relacional.

> Projeto acadêmico desenvolvido por **Anna Clara Benicio Rangel e João Victor Emiliano Delfino** no curso de **Informática - RGI61** — **3d Colégios**.

---

## 🎮 Acesso ao sistema

| | |
|---|---|
| **Endereço** | https://SEU-LINK-AQUI |
| **Ambiente** | Hospedagem web (PHP + MySQL) |
| **Repositório** | https://github.com/SEU-USUARIO/gameshaven |

---

## Funcionalidades

| Área | Descrição |
|------|-----------|
| Autenticação | Cadastro e login de usuários |
| Catálogo | Busca de jogos e página de detalhes com informações completas |
| Compras | Carrinho, checkout e registro de pedidos |
| Biblioteca | Biblioteca pessoal com os jogos adquiridos |
| Avaliações | Usuários podem avaliar os jogos |
| Criadores | Cadastro complementar como desenvolvedor indie e publicação de jogos |
| Doações | Criadores recebem doações pelos jogos publicados |
| Suporte | Canal de contato e painel de acompanhamento e moderação |

---

## Perfis de usuário

O sistema atende três perfis, cada um com suas permissões.

### 👤 Usuário
- Criar conta e fazer login;
- Pesquisar jogos e visualizar suas informações;
- Adicionar títulos ao carrinho e fazer compras;
- Gerenciar uma biblioteca pessoal;
- Avaliar jogos;
- Entrar em contato com o suporte.

### 🍄 Criador
- Realizar cadastro complementar como desenvolvedor indie;
- Publicar jogos informando nome, categoria, descrição, imagens e preço;
- Receber doações por meio dos jogos publicados.

### 🎮 Suporte
- Acompanhar os jogos publicados;
- Ajustar a classificação dos jogos;
- Organizar o catálogo;
- Atender usuários e criadores.

---

## Tecnologias

| Camada | Tecnologia |
|--------|------------|
| Back-end | PHP 8.0+ com PDO |
| Banco de dados | MySQL 8.0+ ou MariaDB |
| Servidor | Apache com `mod_rewrite` (URLs amigáveis) |
| Front-end | HTML e CSS |
| Arquitetura | MVC |

---

## Arquitetura

O sistema segue o padrão **MVC**, separando responsabilidades em três camadas:

| Camada | Responsabilidade |
|--------|------------------|
| **Model** | Regras de negócio e acesso ao banco de dados |
| **View** | Telas e interface exibidas ao usuário |
| **Controller** | Recebe a requisição, aciona os models e escolhe a view |

```
Requisição → Roteador → Controller → Model ⇄ Banco de dados
                            │
                            └────→ View → Resposta HTML
```

---

## Estrutura de pastas

<!-- Estrutura provisória: substituir pela estrutura real do projeto. -->

```
gameshaven/
├── .htaccess              # Reescrita de URLs
├── index.php              # Roteador principal
├── config/
│   └── database.php       # Conexão PDO com MySQL
├── controller/
│   ├── HomeController.php
│   ├── BuscaController.php
│   ├── JogoController.php
│   ├── UsuarioController.php
│   ├── CriadorController.php
│   ├── CarrinhoController.php
│   ├── BibliotecaController.php
│   ├── SuporteController.php
│   └── SobreController.php
├── model/
│   ├── Usuario.php
│   ├── Criador.php
│   ├── Jogo.php
│   ├── Categoria.php
│   ├── Biblioteca.php
│   ├── Avaliacao.php
│   ├── Doacao.php
│   ├── Pedido.php
│   └── Suporte.php
├── view/
│   ├── home.php
│   ├── busca.php
│   ├── jogo.php
│   ├── login.php
│   ├── register.php
│   ├── perfil.php
│   ├── publicar.php       # Publicação de jogos pelo criador
│   ├── biblioteca.php
│   ├── carrinho.php
│   ├── checkout.php
│   ├── suporte.php
│   ├── sobre.php
│   └── partials/
│       ├── header.php
│       ├── navbar.php
│       └── footer.php
├── assets/
│   ├── css/styles.css
│   ├── img/jogos/         # Imagens dos jogos publicados
│   └── uploads/avatares/  # Fotos de perfil
└── db/
    └── gameshaven.sql     # Script completo do banco
```

---

## Execução local

### Pré-requisitos

- PHP 8.0 ou superior;
- MySQL 8.0 ou superior (ou compatível com MariaDB);
- Apache com suporte ao módulo `mod_rewrite`;
- XAMPP, WAMP ou Laragon (recomendado para facilitar a configuração);
- Navegador web;
- Git (opcional, para clonar o repositório).

---

**Boas práticas de segurança**

- Não versione credenciais reais do banco no repositório. Mantenha no GitHub um arquivo de exemplo e o real só no servidor.
- Troque as senhas padrão das contas de demonstração antes de divulgar o link.
- Ative HTTPS, que a maioria das hospedagens oferece gratuitamente.

---

## Equipe

| Nome | Função | GitHub |
|------|--------|--------|
| Anna Clara Benicio Rangel | 
| João Victor Emiliano Delfino |

---

<div align="center">

🍄 **GamesHaven** — KEEP PLAYING. 🎮

</div>
</div>aDB);
- Servidor Apache com suporte ao módulo `mod_rewrite`;
- XAMPP, WAMP ou Laragon (recomendado para facilitar a configuração do ambiente);
- Navegador web;
- Git (opcional, para clonar o repositório).

