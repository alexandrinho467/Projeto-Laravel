<?php
namespace App\Models;
use App\Mail\OrderConfirmationMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Models\StockMovement;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','guest_name','guest_email','guest_id_document','guest_phone','coupon_code','discount','subtotal','shipping_method','shipping_cost','total','payment_method','payment_status','stripe_id','status','stock_decremented','confirmation_sent'];
    protected $casts    = ['discount' => 'decimal:2', 'subtotal' => 'decimal:2', 'shipping_cost' => 'decimal:2', 'total' => 'decimal:2', 'stock_decremented' => 'boolean', 'confirmation_sent' => 'boolean'];

    public function user()  { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }

    public function sendConfirmation(): void
    {
        if ($this->confirmation_sent) return;

        $this->loadMissing('items');

        Mail::to($this->guest_email)->send(new OrderConfirmationMail($this));

        $this->update(['confirmation_sent' => true]);
    }

    public function decrementStock(): void
    {
        if ($this->stock_decremented) return;

        $this->loadMissing('items');

        foreach ($this->items as $item) {
            if (!$item->product_id) continue;

            $ps = ProductSize::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->first();

            if (!$ps) continue;

            $decremented = min($item->qty, $ps->stock);
            $newStock    = max(0, $ps->stock - $item->qty);
            $ps->update(['stock' => $newStock, 'available' => $newStock > 0]);

            if ($decremented > 0) {
                StockMovement::create([
                    'product_id'      => $item->product_id,
                    'product_size_id' => $ps->id,
                    'product_name'    => $item->product_name,
                    'product_brand'   => $item->product_brand,
                    'size'            => $item->size,
                    'type'            => 'saida',
                    'qty'             => $decremented,
                    'order_id'        => $this->id,
                ]);
            }
        }

        $this->update(['stock_decremented' => true]);
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'pending'    => 'Aguardando',
            'processing' => 'Em processamento',
            'shipped'    => 'Enviado',
            'delivered'  => 'Entregue',
            'cancelled'  => 'Cancelado',
            default      => $this->status,
        };
    }

    public function getPaymentMethodLabelAttribute(): string {
        return match($this->payment_method) {
            'cartao'  => 'Cartão de crédito',
            'pix'     => 'PIX',
            'boleto'  => 'Boleto',
            default   => $this->payment_method ?? '—',
        };
    }

    public function getPaymentStatusLabelAttribute(): string {
        return match($this->payment_status) {
            'pending'  => 'Aguardando pagamento',
            'paid'     => 'Pago',
            'failed'   => 'Falhou',
            'refunded' => 'Estornado',
            default    => $this->payment_status,
        };
    }

    public function getTotalFormattedAttribute(): string {
        return 'AED ' . number_format($this->total, 2, '.', ',');
    }

    public function syncToCrm(): void
    {
        try {
            if (CrmDeal::where('order_id', $this->id)->exists()) return;

            $contact = $this->findOrCreateCrmContact();

            CrmDeal::create([
                'crm_contact_id'   => $contact->id,
                'order_id'         => $this->id,
                'title'            => "Pedido #{$this->id}",
                'value'            => $this->total,
                'stage'            => 'negociacao',
                'stage_changed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Falha ao sincronizar pedido com o CRM: ' . $e->getMessage(), ['order_id' => $this->id]);
        }
    }

    public function markCrmDealWon(): void
    {
        try {
            $deal = CrmDeal::where('order_id', $this->id)->first();
            if (!$deal || in_array($deal->stage, ['ganho', 'perdido'], true)) return;

            $deal->update(['stage' => 'ganho', 'stage_changed_at' => now()]);

            CrmActivity::create([
                'crm_contact_id' => $deal->crm_contact_id,
                'crm_deal_id'    => $deal->id,
                'type'           => 'nota',
                'description'    => "Pagamento confirmado — pedido #{$this->id}.",
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Falha ao marcar negócio como ganho no CRM: ' . $e->getMessage(), ['order_id' => $this->id]);
        }
    }

    public function markCrmDealLost(string $reason = 'Pagamento não concluído'): void
    {
        try {
            $deal = CrmDeal::where('order_id', $this->id)->first();
            if (!$deal || in_array($deal->stage, ['ganho', 'perdido'], true)) return;

            $deal->update(['stage' => 'perdido', 'lost_reason' => $reason, 'stage_changed_at' => now()]);

            CrmActivity::create([
                'crm_contact_id' => $deal->crm_contact_id,
                'crm_deal_id'    => $deal->id,
                'type'           => 'nota',
                'description'    => "Negócio perdido — {$reason} (pedido #{$this->id}).",
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Falha ao marcar negócio como perdido no CRM: ' . $e->getMessage(), ['order_id' => $this->id]);
        }
    }

    public static function syncHistoricalOrders(): int
    {
        $synced = 0;

        $syncedOrderIds = CrmDeal::whereNotNull('order_id')->pluck('order_id');

        static::whereNotIn('id', $syncedOrderIds)
            ->orderBy('id')
            ->chunk(50, function ($orders) use (&$synced) {
                foreach ($orders as $order) {
                    $order->syncToCrm();

                    if ($order->payment_status === 'paid') {
                        $order->markCrmDealWon();
                    } elseif ($order->payment_status === 'failed' || $order->status === 'cancelled') {
                        $order->markCrmDealLost('Sincronização retroativa');
                    }

                    $synced++;
                }
            });

        return $synced;
    }

    protected function findOrCreateCrmContact(): CrmContact
    {
        if ($this->user_id) {
            $contact = CrmContact::where('user_id', $this->user_id)->first();

            if (!$contact) {
                $contact = CrmContact::where('email', $this->user->email)->whereNull('user_id')->first();
                if ($contact) $contact->update(['user_id' => $this->user_id]);
            }

            if (!$contact) {
                $contact = CrmContact::create([
                    'user_id' => $this->user_id,
                    'name'    => $this->user->name,
                    'email'   => $this->user->email,
                    'phone'   => \App\Support\PhoneFormatter::toE164($this->user->phone),
                    'source'  => 'site',
                    'status'  => 'ativo',
                ]);
            } elseif ($contact->status === 'lead') {
                $contact->update(['status' => 'ativo']);
            }

            return $contact;
        }

        $contact = CrmContact::where('email', $this->guest_email)->first();

        if (!$contact) {
            $contact = CrmContact::create([
                'name'   => $this->guest_name,
                'email'  => $this->guest_email,
                'phone'  => \App\Support\PhoneFormatter::toE164($this->guest_phone),
                'source' => 'site',
                'status' => 'ativo',
            ]);
        } elseif ($contact->status === 'lead') {
            $contact->update(['status' => 'ativo']);
        }

        return $contact;
    }
}
