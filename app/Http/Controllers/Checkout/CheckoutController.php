<?php
namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function identification()
    {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->route('cart');

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $coupon   = session('coupon');
        $discount = $coupon['discount'] ?? 0;
        $total    = $subtotal - $discount;

        return view('checkout.identification', compact('cart', 'subtotal', 'discount', 'total'));
    }

    public function saveIdentification(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
            'id_document' => 'required|string|max:30',
            'phone'       => 'required|string|min:6',
        ]);

        session(['checkout_identity' => $request->only('name', 'email', 'id_document', 'phone')]);
        return redirect()->route('checkout.address');
    }

    public function address()
    {
        if (empty(session('cart', [])))        return redirect()->route('cart');
        if (!session('checkout_identity'))     return redirect()->route('checkout.identification');

        $user    = auth()->user();
        $saved   = $user ? [
            'cep'          => $user->address_cep,
            'street'       => $user->address_street,
            'number'       => $user->address_number,
            'complement'   => $user->address_complement,
            'neighborhood' => $user->address_neighborhood,
            'city'         => $user->address_city,
            'state'        => $user->address_state,
        ] : null;

        $address = session('checkout_address', $saved ?? []);
        return view('checkout.address', compact('address'));
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'cep'          => 'required|string|max:30',
            'street'       => 'required|string|max:255',
            'number'       => 'required|string|max:100',
            'complement'   => 'nullable|string|max:200',
            'neighborhood' => 'required|string|max:100',
            'city'         => 'required|string|max:100',
            'state'        => 'required|string|max:50',
        ]);

        $address = $request->only('cep','street','number','complement','neighborhood','city','state');
        session(['checkout_address' => $address]);

        if (auth()->check() && $request->boolean('save_address')) {
            auth()->user()->update([
                'address_cep'          => $address['cep'],
                'address_street'       => $address['street'],
                'address_number'       => $address['number'],
                'address_complement'   => $address['complement'],
                'address_neighborhood' => $address['neighborhood'],
                'address_city'         => $address['city'],
                'address_state'        => $address['state'],
            ]);
        }

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->route('cart');
        if (!session('checkout_identity')) return redirect()->route('checkout.identification');
        if (!session('checkout_address'))  return redirect()->route('checkout.address');

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $coupon   = session('coupon');
        $discount = $coupon['discount'] ?? 0;
        $shipping = session('shipping', ['method' => null, 'cost' => 0]);
        $total    = $subtotal - $discount + ($shipping['cost'] ?? 0);
        $identity = session('checkout_identity');

        return view('checkout.payment', compact('cart', 'subtotal', 'discount', 'shipping', 'total', 'identity'));
    }

    public function process(Request $request)
    {
        $request->validate(['payment_method' => 'required|in:cartao,cod']);

        $cart     = session('cart', []);
        $identity = session('checkout_identity');
        if (empty($cart) || !$identity) return redirect()->route('cart');

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $coupon   = session('coupon');
        $discount = $coupon['discount'] ?? 0;
        $shipping = session('shipping', ['method' => 'PAC', 'cost' => 0]);
        $total    = $subtotal - $discount + ($shipping['cost'] ?? 0);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'         => auth()->id(),
                'guest_name'      => $identity['name'],
                'guest_email'     => $identity['email'],
                'guest_id_document' => $identity['id_document'],
                'guest_phone'     => $identity['phone'],
                'coupon_code'     => $coupon['code'] ?? null,
                'discount'        => $discount,
                'subtotal'        => $subtotal,
                'shipping_method' => $shipping['method'],
                'shipping_cost'   => $shipping['cost'],
                'total'           => $total,
                'payment_method'  => $request->payment_method,
                'status'          => 'pending',
            ]);

            $order->syncToCrm();

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'] ?? null,
                    'product_name'  => $item['product_name'],
                    'product_brand' => $item['product_brand'],
                    'size'          => $item['size'],
                    'price'         => $item['price'],
                    'qty'           => $item['qty'],
                    'img'           => $item['img'],
                ]);
            }

            $stripe = new StripeService();
            $base   = [
                'order_id' => $order->id,
                'name'     => $identity['name'],
                'email'    => $identity['email'],
                'id_document' => $identity['id_document'],
                'total'    => $total,
            ];

            if ($request->payment_method === 'cartao') {
                $result = $stripe->createCardPayment(array_merge($base, [
                    'stripe_payment_method_id' => $request->stripe_payment_method_id,
                ]));

                if (!$result['success']) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => $result['message']]);
                }

                $paid = ($result['paid'] ?? false);
                $order->update([
                    'stripe_id'      => $result['stripe_id'],
                    'payment_status' => $paid ? 'paid' : 'pending',
                ]);

                if ($paid) {
                    $order->decrementStock();
                    $order->sendConfirmation();
                    $order->markCrmDealWon();
                }

                DB::commit();
                $this->clearCheckoutSession($coupon);

                if ($result['requires_action'] ?? false) {
                    return response()->json([
                        'success'         => true,
                        'requires_action' => true,
                        'client_secret'   => $result['client_secret'],
                        'order_id'        => $order->id,
                    ]);
                }

                return response()->json([
                    'success'  => true,
                    'order_id' => $order->id,
                    'redirect' => route('checkout.success', $order->id),
                ]);

            } else {
                // Cash on Delivery — no payment processor needed
                $order->update(['payment_status' => 'pending']);
                DB::commit();
                $this->clearCheckoutSession($coupon);

                return response()->json([
                    'success'  => true,
                    'order_id' => $order->id,
                    'redirect' => route('checkout.success', $order->id),
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erro ao processar pedido: ' . $e->getMessage()], 500);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'order_id'          => 'required|integer',
            'payment_intent_id' => 'required|string',
        ]);

        $order  = Order::findOrFail($request->order_id);
        $stripe = new StripeService();
        $intent = $stripe->retrievePaymentIntent($request->payment_intent_id);

        if ($intent && $intent->status === 'succeeded' && ($intent->metadata['order_id'] ?? null) == $order->id) {
            $order->update(['payment_status' => 'paid', 'stripe_id' => $intent->id]);
            $order->decrementStock();
            $order->sendConfirmation();
            $order->markCrmDealWon();
            return response()->json(['success' => true, 'redirect' => route('checkout.success', $order->id)]);
        }

        return response()->json(['success' => false, 'message' => 'Pagamento não confirmado. Tente novamente.']);
    }

    public function success(int $id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('checkout.success', compact('order'));
    }

    public function setShipping(Request $request)
    {
        session(['shipping' => ['method' => $request->method, 'cost' => (float) $request->cost]]);
        return response()->json(['success' => true]);
    }

    private function clearCheckoutSession(?array $coupon): void
    {
        if ($coupon) {
            $couponModel = Coupon::where('code', $coupon['code'])->first();
            if ($couponModel) $couponModel->increment('uses');
        }
        session()->forget(['cart', 'coupon', 'checkout_identity', 'checkout_address', 'shipping']);
    }
}
