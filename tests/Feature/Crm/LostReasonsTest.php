<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\LostReasons;
use App\Models\CrmLostReason;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class LostReasonsTest extends TestCase
{
    public function test_admin_can_create_a_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(LostReasons::class)
            ->set('name', 'Atraso na entrega')
            ->call('create');

        $this->assertTrue(CrmLostReason::where('name', 'Atraso na entrega')->exists());
    }

    public function test_cannot_create_duplicate_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = CrmLostReason::factory()->create();
        $this->actingAs($admin);

        Livewire::test(LostReasons::class)
            ->set('name', $existing->name)
            ->call('create')
            ->assertHasErrors('name');
    }

    public function test_toggle_active_flips_state(): void
    {
        $admin = User::factory()->admin()->create();
        $reason = CrmLostReason::factory()->create(['active' => true]);
        $this->actingAs($admin);

        Livewire::test(LostReasons::class)->call('toggleActive', $reason->id);
        $this->assertFalse($reason->fresh()->active);
    }

    public function test_delete_removes_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $reason = CrmLostReason::factory()->create();
        $this->actingAs($admin);

        Livewire::test(LostReasons::class)->call('delete', $reason->id);
        $this->assertDatabaseMissing('crm_lost_reasons', ['id' => $reason->id]);
    }

    public function test_vendedor_is_blocked_from_route(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.crm.lost-reasons'))
            ->assertStatus(403);
    }
}
