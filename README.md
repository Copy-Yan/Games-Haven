# GamesHaven

Plataforma web de venda de jogos digitais com foco em dar visibilidade a desenvolvedores independentes (indie).

## Estrutura do Projeto

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

## Sobre o Projeto

O GamesHaven é uma plataforma de jogos digitais desenvolvida com a arquitetura MVC (Model-View-Controller), utilizando PHP e MySQL. O objetivo do projeto é vender jogos e ajudar criadores independentes a publicar seus títulos e ganhar visibilidade, aplicando boas práticas de organização de código, separação de responsabilidades e integração com banco de dados relacional.

O sistema atende três perfis de usuário:

**Usuário**

- Criar conta e realizar login;
- Pesquisar jogos e visualizar suas informações;
- Adicionar títulos ao carrinho e efetuar compras;
- Gerenciar a biblioteca pessoal;
- Avaliar jogos;
- Entrar em contato com o suporte.

**Criador**

- Realizar cadastro complementar como desenvolvedor indie;
- Publicar jogos informando nome, categoria, descrição, imagens e preço;
- Receber doações por meio dos jogos publicados.

**Suporte**

- Acompanhar os jogos publicados;
- Ajustar a classificação dos jogos;
- Organizar o catálogo;
- Atender usuários e criadores.

## Pré-requisitos

Antes de executar a aplicação localmente, certifique-se de possuir os seguintes programas instalados:

- PHP 8.0 ou superior;
- MySQL 8.0 ou superior (ou compatível com MariaDB);
- Servidor Apache com suporte ao módulo `mod_rewrite`;
- XAMPP, WAMP ou Laragon (recomendado para facilitar a configuração do ambiente);
- Navegador web;
- Git (opcional, para clonar o repositório).

