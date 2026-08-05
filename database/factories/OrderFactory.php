<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $total = fake()->randomFloat(2, 50, 1000);

        return [
            'user_id'           => null,
            'guest_name'        => fake()->name(),
            'guest_email'       => fake()->unique()->safeEmail(),
            'guest_id_document' => fake()->numerify('###########'),
            'guest_phone'       => fake()->numerify('55###########'),
            'discount'          => 0,
            'subtotal'          => $total,
            'shipping_cost'     => 0,
            'total'             => $total,
            'payment_method'    => 'cartao',
            'payment_status'    => 'pending',
            'status'            => 'pending',
        ];
    }

    public function forUser(\App\Models\User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id'     => $user->id,
            'guest_name'  => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => $user->phone,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => ['payment_status' => 'paid']);
    }
}
