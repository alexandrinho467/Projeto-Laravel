<?php

namespace Database\Factories;

use App\Models\CrmContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmContact>
 */
class CrmContactFactory extends Factory
{
    protected $model = CrmContact::class;

    public function definition(): array
    {
        return [
            'name'   => fake()->name(),
            'email'  => fake()->unique()->safeEmail(),
            'phone'  => fake()->numerify('55###########'),
            'source' => 'manual',
            'status' => 'lead',
        ];
    }
}
