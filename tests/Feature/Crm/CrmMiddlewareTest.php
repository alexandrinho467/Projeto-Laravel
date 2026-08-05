<?php

namespace Tests\Feature\Crm;

use App\Models\User;
use Tests\TestCase;

class CrmMiddlewareTest extends TestCase
{
    public function test_vendedor_can_access_crm_dashboard(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.crm.dashboard'))
            ->assertStatus(200);
    }

    public function test_vendedor_is_blocked_from_admin_products(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.produtos.index'))
            ->assertStatus(403);
    }

    public function test_vendedor_is_blocked_from_team_page(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.team.index'))
            ->assertStatus(403);
    }

    public function test_vendedor_is_blocked_from_settings(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.settings.index'))
            ->assertStatus(403);
    }

    public function test_vendedor_is_blocked_from_coupons(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.cupons.index'))
            ->assertStatus(403);
    }

    public function test_customer_is_blocked_from_crm(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.crm.dashboard'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_both_crm_and_admin_sections(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.crm.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.team.index'))->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.crm.dashboard'))
            ->assertRedirect(route('login'));
    }
}
