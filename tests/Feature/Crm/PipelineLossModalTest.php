<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Pipeline;
use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmLostReason;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class PipelineLossModalTest extends TestCase
{
    public function test_confirming_with_a_selected_reason_marks_deal_lost(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);
        $reason = CrmLostReason::factory()->create(['name' => 'Preço alto']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)
            ->call('openLossModal', $deal->id, [$deal->id])
            ->set('lossReasonId', (string) $reason->id)
            ->call('confirmLoss');

        $deal->refresh();
        $this->assertSame('perdido', $deal->stage);
        $this->assertSame('Preço alto', $deal->lost_reason);
        $this->assertTrue(CrmActivity::where('crm_deal_id', $deal->id)->where('description', 'like', '%Preço alto%')->exists());
    }

    public function test_confirming_with_custom_other_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)
            ->call('openLossModal', $deal->id, [$deal->id])
            ->set('lossReasonOther', 'Cliente comprou em outra loja')
            ->call('confirmLoss');

        $this->assertSame('Cliente comprou em outra loja', $deal->fresh()->lost_reason);
    }

    public function test_confirming_without_a_reason_shows_error(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)
            ->call('openLossModal', $deal->id, [$deal->id])
            ->call('confirmLoss')
            ->assertHasErrors('lossReasonId');

        $this->assertSame('proposta', $deal->fresh()->stage);
    }

    public function test_cancel_loss_modal_does_not_change_deal(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)
            ->call('openLossModal', $deal->id, [$deal->id])
            ->call('cancelLossModal');

        $this->assertSame('proposta', $deal->fresh()->stage);
    }

    public function test_vendedor_cannot_open_loss_modal_for_deal_not_theirs(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();
        $contact = CrmContact::factory()->create(['assigned_to' => $vendedorB->id]);
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'assigned_to' => $vendedorB->id, 'stage' => 'proposta']);

        $this->actingAs($vendedorA);

        Livewire::test(Pipeline::class)
            ->call('openLossModal', $deal->id, [$deal->id])
            ->assertStatus(403);
    }
}
