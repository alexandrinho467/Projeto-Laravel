<?php
namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function dashboard()
    {
        $orders = auth()->user()->orders()->latest()->limit(5)->get();
        return view('account.dashboard', compact('orders'));
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);
        return view('account.orders', compact('orders'));
    }

    public function showOrder(int $id)
    {
        $order    = auth()->user()->orders()->with('items')->findOrFail($id);
        $cardInfo = null;

        if ($order->payment_method === 'cartao' && $order->stripe_id) {
            $cardInfo = (new StripeService())->retrieveCardDetails($order->stripe_id);
        }

        return view('account.order-show', compact('order', 'cardInfo'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email,' . $user->id,
            'id_document'          => 'nullable|string|max:30',
            'phone'                => 'nullable|string',
            'birth_date'           => 'nullable|date',
            'address_cep'          => 'nullable|string|max:9',
            'address_street'       => 'nullable|string|max:255',
            'address_number'       => 'nullable|string|max:20',
            'address_complement'   => 'nullable|string|max:100',
            'address_neighborhood' => 'nullable|string|max:100',
            'address_city'         => 'nullable|string|max:100',
            'address_state'        => 'nullable|string|max:50',
        ]);

        $data = $request->only('name','email','id_document','phone','birth_date','address_cep','address_street','address_number','address_complement','address_neighborhood','address_city','address_state');
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', 'Dados atualizados com sucesso!');
    }
}
