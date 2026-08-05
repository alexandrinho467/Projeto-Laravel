<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\PorterService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    public function index()
    {
        $cart     = $this->getCart();
        $coupon   = session('coupon');
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $discount = 0;
        if ($coupon) {
            $couponModel = Coupon::where('code', $coupon['code'])->first();
            if ($couponModel) $discount = $couponModel->calculateDiscount($subtotal);
        }
        $total = $subtotal - $discount;
        return view('shop.cart', compact('cart', 'subtotal', 'discount', 'total', 'coupon'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|integer', 'size' => 'required|string']);

        $product = Product::with('images')->findOrFail($request->product_id);
        $cart    = $this->getCart();
        $key     = $product->id . '_' . $request->size;

        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            $img = $product->images->first();
            $cart[$key] = [
                'product_id'    => $product->id,
                'product_name'  => $product->name,
                'product_brand' => $product->brand,
                'size'          => $request->size,
                'price'         => (float) $product->price,
                'qty'           => 1,
                'img'           => $img ? $img->img1 : '',
            ];
        }

        $this->saveCart($cart);
        return response()->json(['success' => true, 'count' => collect($cart)->sum('qty')]);
    }

    public function remove(Request $request)
    {
        $key  = $request->key;
        $cart = $this->getCart();
        unset($cart[$key]);
        $this->saveCart($cart);
        return response()->json(['success' => true, 'count' => collect($cart)->sum('qty')]);
    }

    public function updateQty(Request $request)
    {
        $key  = $request->key;
        $qty  = (int) $request->qty;
        $cart = $this->getCart();
        if (isset($cart[$key]) && $qty > 0) {
            $cart[$key]['qty'] = $qty;
            $this->saveCart($cart);
        }
        return response()->json(['success' => true]);
    }

    public function applyCoupon(Request $request)
    {
        $code   = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->first();
        $cart   = $this->getCart();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);

        if (!$coupon || !$coupon->isValid($subtotal)) {
            session()->forget('coupon');
            return response()->json(['success' => false, 'message' => 'Cupom inválido ou expirado.']);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        session(['coupon' => ['code' => $coupon->code, 'discount' => $discount]]);

        return response()->json([
            'success'  => true,
            'message'  => 'Cupom aplicado! Desconto: AED ' . number_format($discount, 2, '.', ','),
            'discount' => $discount,
            'total'    => $subtotal - $discount,
        ]);
    }

    public function calcularFrete(Request $request)
    {
        $request->validate(['endereco' => 'required|string|min:3']);

        $cart   = $this->getCart();
        $pesoKg = PorterService::pesoTotal($cart);

        $porter = new PorterService();
        $coords = $porter->geocodificar($request->endereco);

        if (!$coords) {
            return response()->json(['success' => false, 'message' => 'Endereço não encontrado em Dubai. Tente incluir a área (ex: Downtown Dubai, Marina, JBR...).']);
        }

        $resultado = $porter->calcular($coords['lat'], $coords['lng'], $pesoKg);

        return response()->json([
            'success'   => true,
            'opcoes'    => $resultado['opcoes'],
            'distancia' => $resultado['distancia_km'],
            'tempo'     => $resultado['tempo_min'] ?? null,
        ]);
    }

    public function count()
    {
        $cart       = $this->getCart();
        $count      = collect($cart)->sum('qty');
        $cartTotal  = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $items      = collect($cart)->map(fn($item, $key) => array_merge($item, ['key' => $key]))->values();

        return response()->json([
            'count'      => $count,
            'cart_total' => $cartTotal,
            'items'      => $items,
        ]);
    }
}
