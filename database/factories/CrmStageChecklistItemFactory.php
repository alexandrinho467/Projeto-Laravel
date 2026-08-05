<?php

namespace Database\Factories;

use App\Models\CrmStageChecklistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmStageChecklistItem>
 */
class CrmStageChecklistItemFactory extends Factory
{
    protected $model = CrmStageChecklistItem::class;

    public function definition(): array
    {
        return [
            'stage'    => 'proposta',
            'key'      => fake()->unique()->word(),
            'label'    => fake()->sentence(3),
            'position' => 0,
        ];
    }
}
