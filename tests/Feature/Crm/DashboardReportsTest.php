<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Dashboard;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardReportsTest extends TestCase
{
    public function test_average_conversion_time_is_computed_from_won_deals(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();

        CrmDeal::factory()->create([
            'crm_contact_id'   => $contact->id,
            'stage'            => 'ganho',
            'created_at'       => now()->subDays(10),
            'stage_changed_at' => now()->subDays(5),
        ]);

        $this->actingAs($admin);

        $html = Livewire::test(Dashboard::class)->html();

        $this->assertStringContainsString('5 dias', $html);
    }

    public function test_loss_reason_breakdown_shows_reasons_and_percentages(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();

        CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'perdido', 'lost_reason' => 'Cartão Recusado']);
        CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'perdido', 'lost_reason' => 'Cartão Recusado']);
        CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'perdido', 'lost_reason' => 'Preço']);

        $this->actingAs($admin);

        $html = Livewire::test(Dashboard::class)->html();

        $this->assertStringContainsString('Cartão Recusado', $html);
        $this->assertStringContainsString('Preço', $html);
        $this->assertStringContainsString('67%', $html);
    }

    public function test_no_lost_deals_shows_empty_state(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $html = Livewire::test(Dashboard::class)->html();

        $this->assertStringContainsString('Nenhum negócio perdido', $html);
    }
}
