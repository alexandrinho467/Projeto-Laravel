<?php

namespace Tests\Feature\Crm;

use App\Models\User;
use Tests\TestCase;

class TeamTest extends TestCase
{
    public function test_admin_can_promote_existing_customer_to_vendedor(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin)
            ->post(route('admin.team.promote'), ['email' => $customer->email, 'role' => 'vendedor'])
            ->assertRedirect(route('admin.team.index'));

        $this->assertSame('vendedor', $customer->fresh()->role);
    }

    public function test_promoting_unknown_email_fails_validation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.team.promote'), ['email' => 'nobody@example.com', 'role' => 'vendedor'])
            ->assertSessionHasErrors('email');
    }

    public function test_promoting_someone_already_on_the_team_is_blocked(): void
    {
        $admin = User::factory()->admin()->create();
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($admin)
            ->post(route('admin.team.promote'), ['email' => $vendedor->email, 'role' => 'admin'])
            ->assertSessionHas('error');

        $this->assertSame('vendedor', $vendedor->fresh()->role);
    }

    public function test_cannot_delete_last_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.team.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.team.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_removing_a_vendedor_downgrades_to_customer(): void
    {
        $admin = User::factory()->admin()->create();
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($admin)->delete(route('admin.team.destroy', $vendedor));

        $this->assertSame('customer', $vendedor->fresh()->role);
    }
}
