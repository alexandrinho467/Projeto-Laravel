<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCardPayment(array $o): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'               => (int) round($o['total'] * 100),
                'currency'             => 'aed',
                'payment_method'       => $o['stripe_payment_method_id'],
                'payment_method_types' => ['card'],
                'confirm'              => true,
                'return_url'           => route('checkout.success', $o['order_id']),
                'metadata'             => ['order_id' => $o['order_id']],
                'description'          => 'Pedido #' . $o['order_id'] . ' - Dias Sneakers',
            ]);

            return match($intent->status) {
                'succeeded'       => ['success' => true, 'stripe_id' => $intent->id, 'paid' => true],
                'requires_action' => [
                    'success'         => true,
                    'stripe_id'       => $intent->id,
                    'paid'            => false,
                    'requires_action' => true,
                    'client_secret'   => $intent->client_secret,
                ],
                default => ['success' => false, 'message' => 'Cartão recusado. Tente outro cartão.'],
            };
        } catch (ApiErrorException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function createPixPayment(array $o): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'               => (int) round($o['total'] * 100),
                'currency'             => 'aed',
                'payment_method_types' => ['pix'],
                'payment_method_data'  => ['type' => 'pix'],
                'confirm'              => true,
                'metadata'             => ['order_id' => $o['order_id']],
            ]);

            $qr = $intent->next_action?->pix_display_qr_code ?? null;

            return [
                'success'   => true,
                'stripe_id' => $intent->id,
                'pix_code'  => $qr?->data,
                'pix_image' => $qr?->image_url_png,
            ];
        } catch (ApiErrorException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function createBoletoPayment(array $o): array
    {
        try {
            $cpf = preg_replace('/\D/', '', $o['cpf'] ?? '');
            $intent = PaymentIntent::create([
                'amount'               => (int) round($o['total'] * 100),
                'currency'             => 'aed',
                'payment_method_types' => ['boleto'],
                'payment_method_data'  => [
                    'type'            => 'boleto',
                    'boleto'          => ['tax_id' => $cpf],
                    'billing_details' => [
                        'name'    => $o['name'],
                        'email'   => $o['email'],
                        'address' => [
                            'line1'       => $o['address'],
                            'city'        => $o['city'],
                            'state'       => $o['state'],
                            'postal_code' => preg_replace('/\D/', '', $o['postal_code']),
                            'country'     => 'BR',
                        ],
                    ],
                ],
                'confirm'              => true,
                'metadata'             => ['order_id' => $o['order_id']],
            ]);

            $details = $intent->next_action?->boleto_display_details ?? null;

            return [
                'success'        => true,
                'stripe_id'      => $intent->id,
                'boleto_url'     => $details?->hosted_voucher_url,
                'boleto_barcode' => $details?->number,
                'boleto_expiry'  => $details?->expires_at ? date('d/m/Y', $details->expires_at) : null,
            ];
        } catch (ApiErrorException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function retrievePaymentIntent(string $id): ?PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($id);
        } catch (ApiErrorException) {
            return null;
        }
    }

    public function retrieveCardDetails(string $intentId): ?array
    {
        try {
            $intent = PaymentIntent::retrieve($intentId, ['expand' => ['payment_method']]);
            $card   = $intent->payment_method?->card ?? null;
            if (!$card) return null;

            return [
                'brand'     => ucfirst($card->brand),
                'last4'     => $card->last4,
                'exp_month' => str_pad($card->exp_month, 2, '0', STR_PAD_LEFT),
                'exp_year'  => substr($card->exp_year, -2),
            ];
        } catch (ApiErrorException) {
            return null;
        }
    }
}
