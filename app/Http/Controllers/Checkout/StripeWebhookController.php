<?php
namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');
        $sig    = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($request->getContent(), $sig, $secret);
        } catch (SignatureVerificationException) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $intent = $event->data->object;

        if ($event->type === 'payment_intent.succeeded') {
            $order = Order::where('stripe_id', $intent->id)
                ->where('payment_status', '!=', 'paid')
                ->first();

            if ($order) {
                $order->update(['payment_status' => 'paid']);
                $order->decrementStock();
                $order->sendConfirmation();
                $order->markCrmDealWon();
            }
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $order = Order::where('stripe_id', $intent->id)
                ->where('payment_status', 'pending')
                ->first();

            if ($order) {
                $order->update(['payment_status' => 'failed']);
                $order->markCrmDealLost('Pagamento falhou');
            }
        }

        return response()->json(['ok' => true]);
    }
}
