<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Pipeline;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
    public function test_toggling_checklist_item_persists_state(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)->call('toggleChecklistItem', $deal->id, 'tamanho');

        $this->assertTrue($deal->fresh()->checklist_state['tamanho']);

        [$done, $total] = $deal->fresh()->checklistProgress();
        $this->assertSame(1, $done);
        $this->assertSame(2, $total);
    }

    public function test_toggling_twice_reverts_to_false(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);

        $this->actingAs($admin);

        $component = Livewire::test(Pipeline::class);
        $component->call('toggleChecklistItem', $deal->id, 'frete');
        $component->call('toggleChecklistItem', $deal->id, 'frete');

        $this->assertFalse($deal->fresh()->checklist_state['frete']);
    }

    public function test_stage_without_checklist_template_has_no_items(): void
    {
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'novo_lead']);

        $this->assertSame([0, 0], $deal->checklistProgress());
    }

    public function test_unknown_checklist_key_is_ignored(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'proposta']);

        $this->actingAs($admin);

        Livewire::test(Pipeline::class)->call('toggleChecklistItem', $deal->id, 'nao-existe');

        $this->assertNull($deal->fresh()->checklist_state);
    }
}
