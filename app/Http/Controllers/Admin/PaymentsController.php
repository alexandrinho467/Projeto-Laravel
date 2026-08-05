<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Stripe\Balance;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;

class PaymentsController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $error = null;
        $available = 0;
        $pending = 0;
        $payments = collect();

        try {
            $balance = Balance::retrieve();
            $available = collect($balance->available)->sum('amount');
            $pending = collect($balance->pending)->sum('amount');

            $payments = collect(PaymentIntent::all(['limit' => 20])->data);
        } catch (ApiErrorException $e) {
            $error = $e->getMessage();
        }

        return view('admin.payments', compact('available', 'pending', 'payments', 'error'));
    }
}
