<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Pipeline;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class PipelineScopingTest extends TestCase
{
    public function test_vendedor_only_sees_deals_assigned_to_them(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();

        $contactA = CrmContact::factory()->create(['assigned_to' => $vendedorA->id]);
        $contactB = CrmContact::factory()->create(['assigned_to' => $vendedorB->id]);

        CrmDeal::factory()->create(['crm_contact_id' => $contactA->id, 'assigned_to' => $vendedorA->id, 'title' => 'Deal A']);
        CrmDeal::factory()->create(['crm_contact_id' => $contactB->id, 'assigned_to' => $vendedorB->id, 'title' => 'Deal B']);

        $this->actingAs($vendedorA);

        $html = Livewire::test(Pipeline::class)->html();

        $this->assertStringContainsString('Deal A', $html);
        $this->assertStringNotContainsString('Deal B', $html);
    }

    public function test_admin_sees_all_deals(): void
    {
        $admin = User::factory()->admin()->create();
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();

        $contactA = CrmContact::factory()->create(['assigned_to' => $vendedorA->id]);
        $contactB = CrmContact::factory()->create(['assigned_to' => $vendedorB->id]);

        CrmDeal::factory()->create(['crm_contact_id' => $contactA->id, 'assigned_to' => $vendedorA->id, 'title' => 'Deal A']);
        CrmDeal::factory()->create(['crm_contact_id' => $contactB->id, 'assigned_to' => $vendedorB->id, 'title' => 'Deal B']);

        $this->actingAs($admin);

        $html = Livewire::test(Pipeline::class)->html();

        $this->assertStringContainsString('Deal A', $html);
        $this->assertStringContainsString('Deal B', $html);
    }

    public function test_update_stage_moves_deal_and_logs_activity(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'novo_lead']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)
            ->call('updateStage', $deal->id, 'qualificado', [$deal->id]);

        $deal->refresh();
        $this->assertSame('qualificado', $deal->stage);
        $this->assertTrue($deal->activities()->where('description', 'like', '%Estágio alterado%')->exists());
    }

    public function test_vendedor_cannot_move_a_deal_not_assigned_to_them(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();
        $contact = CrmContact::factory()->create(['assigned_to' => $vendedorB->id]);
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'assigned_to' => $vendedorB->id, 'stage' => 'novo_lead']);

        $this->actingAs($vendedorA);

        Livewire::test(Pipeline::class)
            ->call('updateStage', $deal->id, 'qualificado', [$deal->id])
            ->assertStatus(403);
    }
}
