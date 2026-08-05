<?php

namespace Database\Factories;

use App\Models\CrmMessageTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmMessageTemplate>
 */
class CrmMessageTemplateFactory extends Factory
{
    protected $model = CrmMessageTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'body' => fake()->sentence(10),
        ];
    }
}
