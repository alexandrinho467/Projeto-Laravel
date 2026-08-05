<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Settings;
use App\Models\CrmStageChecklistItem;
use App\Models\SiteSetting;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class CrmSettingsTest extends TestCase
{
    public function test_admin_can_save_email_hour(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(Settings::class)
            ->set('emailHour', '14')
            ->call('saveEmailHour');

        $this->assertSame('14', SiteSetting::get('crm_email_hour'));
    }

    public function test_email_hour_must_be_within_range(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(Settings::class)
            ->set('emailHour', '25')
            ->call('saveEmailHour')
            ->assertHasErrors('emailHour');
    }

    public function test_admin_can_add_checklist_item_to_a_stage(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(Settings::class)
            ->set('checklistStage', 'qualificado')
            ->set('checklistLabel', 'Confirmar orçamento')
            ->call('addChecklistItem');

        $this->assertTrue(
            CrmStageChecklistItem::where('stage', 'qualificado')->where('label', 'Confirmar orçamento')->exists()
        );
    }

    public function test_admin_can_remove_checklist_item(): void
    {
        $admin = User::factory()->admin()->create();
        $item = CrmStageChecklistItem::factory()->create(['stage' => 'proposta']);
        $this->actingAs($admin);

        Livewire::test(Settings::class)->call('removeChecklistItem', $item->id);

        $this->assertDatabaseMissing('crm_stage_checklist_items', ['id' => $item->id]);
    }

    public function test_vendedor_is_blocked_from_route(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.crm.settings'))
            ->assertStatus(403);
    }
}
