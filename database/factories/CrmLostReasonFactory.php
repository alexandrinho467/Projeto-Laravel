<?php

namespace Database\Factories;

use App\Models\CrmLostReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmLostReason>
 */
class CrmLostReasonFactory extends Factory
{
    protected $model = CrmLostReason::class;

    public function definition(): array
    {
        return [
            'name'   => fake()->unique()->words(2, true),
            'active' => true,
        ];
    }
}
