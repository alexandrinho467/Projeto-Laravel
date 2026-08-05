@extends('layouts.app')
@section('title', 'Política de Privacidade | Dias Sneakers')
@section('content')

<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a>
  <span class="bc-sep">›</span>
  <span class="bc-current">Política de Privacidade</span>
</div>

<div class="privacy-page">
  <h1 class="privacy-title">Política de Privacidade</h1>
  <p class="privacy-updated">Última atualização: {{ date('d/m/Y') }}</p>

  <div class="privacy-body">

    <h2>1. Quem somos</h2>
    <p>A <strong>Dias Sneakers</strong> é uma loja virtual especializada em tênis de alto desempenho e luxo. Este documento explica como coletamos, usamos e protegemos os seus dados pessoais, em conformidade com a <strong>Lei Geral de Proteção de Dados (LGPD — Lei nº 13.709/2018)</strong>.</p>

    <h2>2. Dados que coletamos</h2>
    <ul>
      <li><strong>Dados de cadastro:</strong> nome, e-mail e senha para criação de conta.</li>
      <li><strong>Dados de entrega:</strong> endereço completo informado no checkout.</li>
      <li><strong>Dados de pagamento:</strong> processados diretamente pela <strong>Stripe</strong> — não armazenamos dados de cartão.</li>
      <li><strong>Dados de navegação:</strong> cookies técnicos para funcionamento do carrinho e sessão.</li>
    </ul>

    <h2>3. Como usamos seus dados</h2>
    <ul>
      <li>Processar e entregar seus pedidos.</li>
      <li>Enviar confirmações de compra e atualizações de entrega.</li>
      <li>Melhorar a experiência de navegação no site.</li>
      <li>Cumprir obrigações legais e fiscais.</li>
    </ul>

    <h2>4. Cookies</h2>
    <p>Utilizamos cookies estritamente necessários para o funcionamento do carrinho de compras e da sessão de login. Ao aceitar nossa política, você também permite cookies de análise para melhorarmos nossos serviços.</p>

    <h2>5. Compartilhamento de dados</h2>
    <p>Seus dados são compartilhados apenas com:</p>
    <ul>
      <li><strong>Stripe</strong> — processamento de pagamentos.</li>
      <li><strong>Correios</strong> — cálculo e entrega dos pedidos.</li>
    </ul>
    <p>Não vendemos nem compartilhamos seus dados com terceiros para fins de marketing.</p>

    <h2>6. Seus direitos (LGPD Art. 18)</h2>
    <p>Você tem direito a:</p>
    <ul>
      <li>Confirmar a existência de tratamento dos seus dados.</li>
      <li>Acessar, corrigir ou excluir seus dados.</li>
      <li>Revogar o consentimento a qualquer momento.</li>
      <li>Solicitar a portabilidade dos dados.</li>
    </ul>
    <p>Para exercer seus direitos, entre em contato: <a href="mailto:suporte@alexandredias.com.br">suporte@alexandredias.com.br</a></p>

    <h2>7. Retenção de dados</h2>
    <p>Mantemos seus dados pelo tempo necessário para cumprir as finalidades descritas acima e as obrigações legais aplicáveis (mínimo de 5 anos para dados fiscais).</p>

    <h2>8. Segurança</h2>
    <p>Adotamos medidas técnicas e organizacionais adequadas para proteger seus dados contra acesso não autorizado, perda ou destruição.</p>

    <h2>9. Contato</h2>
    <p>Dúvidas sobre esta política? Fale conosco:<br>
    <a href="mailto:suporte@alexandredias.com.br">suporte@alexandredias.com.br</a></p>

  </div>
</div>

@endsection
