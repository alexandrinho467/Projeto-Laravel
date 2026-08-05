# Dias Sneakers

Uma vitrine de e-commerce construída em Laravel 12 com foco em vendas de tênis premium, checkout integrado, painel administrativo e CRM leve.

**Autor:** Alexandre Dias Melo da Silva

## Visão geral

Este projeto é uma loja virtual completa com:
- Home moderna com banner principal e destaques de coleção
- Página de produtos com filtros de categoria e marcas
- Busca de produtos
- Carrinho de compras com cupom, cálculo de frete e atualização de quantidade
- Processo de checkout passo a passo (identificação, endereço, pagamento)
- Autenticação de clientes (login, cadastro, recuperação de senha)
- Área do cliente com pedidos, perfil e histórico
- Painel administrativo para gerenciamento de produtos, estoque, pedidos, cupons e configurações do site
- Recursos CRM para contatos, vendas e integração de e-mail

## Principais funcionalidades

### Loja
- Exibe produtos ativos com imagens, marcas, preços e badges
- Seletor de tamanhos para cada produto
- Carrinho persistente via sessão
- Aplicação de cupons de desconto
- Cálculo de frete com serviço externo via `PorterService`
- Checkout com opção de pagamento por cartão Stripe ou cash on delivery
- Confirmação de pedidos com geração de pedido no banco e envio de confirmação por e-mail

### Autenticação e conta do cliente
- Cadastro com validação de senha forte
- Login seguro e logout
- Recuperação de senha por e-mail
- Área de conta com dashboard, pedidos, detalhes do pedido e perfil
- Atualização de dados pessoais e endereço

### Admin / CRM
- Painel administrativo completo com rotas protegidas para usuários com papel `admin`
- Configurações do site armazenadas em `site_settings`
- Cadastro e edição de produtos, estoque, cupons e equipe
- CRM básico para acompanhar contatos, negociações e histórico
- Sincronização de e-mails via IMAP para vendedores
- Rota de webhooks para WhatsApp e Instagram

### Dados iniciais e seeders
O banco é inicializado com:
- Usuário admin (`admin@diasneakers.com.br` / `admin123`)
- Cupom de desconto `BEMVINDO10`
- Configurações básicas de site e banner
- Coleção de produtos pré-selecionados com diferentes marcas, preços, descrições e tamanhos

## Tecnologias usadas

- PHP 8.2
- Laravel 12
- Livewire 4
- Vite + Tailwind CSS
- Stripe (pagamentos)
- MySQL / SQLite (Banco de dados via configuração Laravel)
- Composer e npm

## Estrutura do projeto

- `app/Http/Controllers/Shop/` - controle da loja, produtos, carrinho e busca
- `app/Http/Controllers/Checkout/` - fluxo de checkout e integração Stripe
- `app/Http/Controllers/Auth/` - login, cadastro e reset de senha
- `app/Http/Controllers/Account/` - área do cliente
- `app/Http/Controllers/Admin/` - administração de pedidos, produtos, cupons e configurações
- `app/Models/` - modelos de produtos, pedidos, usuários, cupons e configurações do site
- `resources/views/` - templates Blade para frontend e backend
- `public/assets/images/` - imagens de produtos e banners
- `database/seeders/DatabaseSeeder.php` - dados iniciais do projeto

## Instalação local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Para desenvolvimento:

```bash
npm run dev
php artisan serve
```

## Configuração adicional

- Ajuste as credenciais Stripe em `.env` (`STRIPE_KEY`, `STRIPE_SECRET`)
- Configure banco de dados no `.env`
- O site usa configuração de frete via `PorterService` e pode depender de credenciais externas

## Rotas importantes

- `/` - home
- `/masculino` - categoria masculino
- `/feminino` - categoria feminino
- `/marcas` - listagem de marcas
- `/produto/{id}` - detalhe do produto
- `/carrinho` - carrinho de compras
- `/checkout/identificacao` - início do checkout
- `/login`, `/cadastro`, `/conta` - autenticação e área do cliente
- `/admin` - entrada para painel administrativo (acesso limitado)

## Notas finais

Este repositório é ideal como base para uma loja de moda/sneakers, com recursos de e-commerce, administração e CRM. Ele pode ser expandido facilmente com integração real de pagamentos, estoque em tempo real, filtros avançados e catálogo de marcas.

