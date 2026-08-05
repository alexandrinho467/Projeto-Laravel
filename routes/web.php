<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\BrandController;
use App\Http\Controllers\Shop\SearchController;
use App\Http\Controllers\Shop\ReviewController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Checkout\StripeWebhookController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\EstoqueController;
use App\Http\Controllers\Admin\VendasController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\Crm\ContactExportController;
use App\Http\Controllers\Webhooks\WhatsAppWebhookController;
use App\Http\Controllers\Webhooks\InstagramWebhookController;
use App\Livewire\Admin\Crm\Dashboard as CrmDashboard;
use App\Livewire\Admin\Crm\ContactsIndex;
use App\Livewire\Admin\Crm\ContactShow;
use App\Livewire\Admin\Crm\Pipeline;
use App\Livewire\Admin\Crm\LostReasons;
use App\Livewire\Admin\Crm\Settings as CrmSettings;
use App\Livewire\Admin\Crm\MessageTemplates;
use App\Livewire\Admin\Crm\Tasks as CrmTasks;
use App\Livewire\Admin\Crm\AuditLogViewer;
use App\Livewire\Admin\Crm\LogsIndex;
use App\Livewire\Admin\Crm\ConversationShow;
use App\Livewire\Admin\Crm\EmailSettings;

// ── SHOP ──────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/masculino', [CategoryController::class, 'masculino'])->name('masculino');
Route::get('/feminino', [CategoryController::class, 'feminino'])->name('feminino');
Route::get('/marcas', [BrandController::class, 'index'])->name('marcas');
Route::get('/marcas/{slug}', [BrandController::class, 'show'])->name('marca.show');
Route::get('/produto/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/busca', [SearchController::class, 'index'])->name('search');
Route::post('/produto/{product}/avaliar', [ReviewController::class, 'store'])->name('review.store');
Route::middleware('auth')->group(function () {
    Route::put('/avaliacao/{review}', [ReviewController::class, 'update'])->name('review.update');
    Route::delete('/avaliacao/{review}', [ReviewController::class, 'destroy'])->name('review.destroy');
});
Route::get('/privacidade', fn() => view('shop.privacidade'))->name('privacidade');

// Cart
Route::get('/carrinho', [CartController::class, 'index'])->name('cart');
Route::post('/carrinho/adicionar', [CartController::class, 'add'])->name('cart.add');
Route::post('/carrinho/remover', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/carrinho/quantidade', [CartController::class, 'updateQty'])->name('cart.qty');
Route::post('/carrinho/cupom', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::get('/carrinho/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/carrinho/frete', [CartController::class, 'calcularFrete'])->name('cart.frete');

// Stripe Webhook (CSRF excluded in bootstrap/app.php)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Webhooks Omnichannel (CSRF excluded em bootstrap/app.php)
Route::match(['get', 'post'], '/webhooks/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('webhooks.whatsapp');
Route::match(['get', 'post'], '/webhooks/instagram', [InstagramWebhookController::class, 'handle'])->name('webhooks.instagram');

// Cron — verificação de pagamentos pendentes
Route::get('/cron/pagamentos', [CronController::class, 'verificarPagamentos'])->name('cron.pagamentos');

// Cron — notificação de follow-ups atrasados no CRM
Route::get('/cron/crm-followups', [CronController::class, 'verificarFollowups'])->name('cron.crm-followups');

// Cron — negócios parados em "Proposta" há mais de 3 dias
Route::get('/cron/crm-reengajamento', [CronController::class, 'verificarEstagiosParados'])->name('cron.crm-reengajamento');

// Cron — sincronização das caixas de e-mail (IMAP) dos vendedores
Route::get('/cron/email-sync', [CronController::class, 'sincronizarEmails'])->name('cron.email-sync');

// Cron — backup diário do banco de dados
Route::get('/cron/backup', [CronController::class, 'backupBanco'])->name('cron.backup');

// Checkout
Route::get('/checkout/identificacao', [CheckoutController::class, 'identification'])->name('checkout.identification');
Route::post('/checkout/identificacao', [CheckoutController::class, 'saveIdentification'])->name('checkout.identification.save');
Route::get('/checkout/endereco', [CheckoutController::class, 'address'])->name('checkout.address');
Route::post('/checkout/endereco', [CheckoutController::class, 'saveAddress'])->name('checkout.address.save');
Route::get('/checkout/pagamento', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/processar', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/checkout/confirmar', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
Route::get('/checkout/sucesso/{id}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/frete', [CheckoutController::class, 'setShipping'])->name('checkout.shipping');

// ── AUTH ──────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/cadastro', [AuthController::class, 'registerForm'])->name('register');
Route::post('/cadastro', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redefinição de senha
Route::get('/senha/esqueci', [ForgotPasswordController::class, 'showForm'])->name('senha.esqueci');
Route::post('/senha/esqueci', [ForgotPasswordController::class, 'sendLink'])->name('senha.enviar');
Route::get('/senha/redefinir/{token}', [ResetPasswordController::class, 'showForm'])->name('senha.redefinir.form');
Route::post('/senha/redefinir', [ResetPasswordController::class, 'reset'])->name('senha.redefinir');

// ── ACCOUNT ───────────────────────────────────────────
Route::middleware(['auth'])->prefix('conta')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/pedidos', [AccountController::class, 'orders'])->name('orders');
    Route::get('/pedidos/{id}', [AccountController::class, 'showOrder'])->name('order.show');
    Route::get('/perfil', [AccountController::class, 'profile'])->name('profile');
    Route::post('/perfil', [AccountController::class, 'updateProfile'])->name('profile.update');
});

// ── ADMIN ─────────────────────────────────────────────
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produtos', AdminProductController::class)->parameters(['produtos' => 'product']);
    Route::post('/produtos/{product}/toggle', [AdminProductController::class, 'toggleActive'])->name('produtos.toggle');

    Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pedidos/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque');
    Route::get('/estoque/relatorio', [EstoqueController::class, 'relatorio'])->name('estoque.relatorio');
    Route::get('/estoque/{product}/gerenciar', [EstoqueController::class, 'edit'])->name('estoque.edit');
    Route::post('/estoque/{product}/atualizar', [EstoqueController::class, 'update'])->name('estoque.update');
    Route::post('/estoque/{product}/adicionar', [EstoqueController::class, 'addSize'])->name('estoque.add');
    Route::delete('/estoque/tamanho/{size}', [EstoqueController::class, 'removeSize'])->name('estoque.remove');
    Route::get('/vendas', [VendasController::class, 'index'])->name('vendas');
    Route::get('/pagamentos', [PaymentsController::class, 'index'])->name('payments.index');

    Route::get('/configuracoes', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/configuracoes', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('cupons', CouponController::class)->parameters(['cupons' => 'cupon']);
    Route::post('/cupons/{cupon}/toggle', [CouponController::class, 'toggle'])->name('cupons.toggle');

    Route::get('/equipe', [TeamController::class, 'index'])->name('team.index');
    Route::get('/equipe/novo', [TeamController::class, 'create'])->name('team.create');
    Route::post('/equipe', [TeamController::class, 'store'])->name('team.store');
    Route::post('/equipe/promover', [TeamController::class, 'promote'])->name('team.promote');
    Route::get('/equipe/{member}/editar', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/equipe/{member}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/equipe/{member}', [TeamController::class, 'destroy'])->name('team.destroy');

    // Exportação de contatos do CRM — só admin (rota fora do grupo 'crm' de propósito)
    Route::get('/crm/contatos/exportar', [ContactExportController::class, 'export'])->name('crm.contacts.export');

    // Gerenciamento do CRM — só admin
    Route::get('/crm/motivos-perda', LostReasons::class)->name('crm.lost-reasons');
    Route::get('/crm/configuracoes', CrmSettings::class)->name('crm.settings');
    Route::get('/crm/auditoria', AuditLogViewer::class)->name('crm.audit');
});

// ── ADMIN / CRM ───────────────────────────────────────
Route::middleware(['auth','crm'])->prefix('admin/crm')->name('admin.crm.')->group(function () {
    Route::get('/', CrmDashboard::class)->name('dashboard');
    Route::get('/contatos', ContactsIndex::class)->name('contacts.index');
    Route::get('/contatos/{contact}', ContactShow::class)->name('contacts.show');
    Route::get('/pipeline', Pipeline::class)->name('pipeline');
    Route::get('/tarefas', CrmTasks::class)->name('tasks');
    Route::get('/mensagens', MessageTemplates::class)->name('messages');
    Route::get('/email', EmailSettings::class)->name('email-settings');
    Route::get('/logs', LogsIndex::class)->name('logs');
    Route::get('/negociacoes/{deal}/conversa', ConversationShow::class)->name('conversations.show');
});
