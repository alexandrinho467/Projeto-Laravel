<?php

namespace Database\Factories;

use App\Models\CrmContact;
use App\Models\CrmDeal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmDeal>
 */
class CrmDealFactory extends Factory
{
    protected $model = CrmDeal::class;

    public function definition(): array
    {
        return [
            'crm_contact_id'   => CrmContact::factory(),
            'title'            => fake()->sentence(3),
            'value'            => fake()->randomFloat(2, 50, 1000),
            'stage'            => 'novo_lead',
            'stage_changed_at' => now(),
        ];
    }
}
