<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\GlobalSearch;
use App\Models\CrmContact;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    public function test_finds_contact_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        CrmContact::factory()->create(['name' => 'Zayed Al Findme']);
        CrmContact::factory()->create(['name' => 'Someone Else']);

        $this->actingAs($admin);

        $html = Livewire::test(GlobalSearch::class)->set('q', 'Findme')->html();

        $this->assertStringContainsString('Zayed Al Findme', $html);
        $this->assertStringNotContainsString('Someone Else', $html);
    }

    public function test_finds_order_by_id(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $this->actingAs($admin);

        $html = Livewire::test(GlobalSearch::class)->set('q', (string) $order->id)->html();

        $this->assertStringContainsString('Pedido #' . $order->id, $html);
    }

    public function test_vendedor_only_finds_own_assigned_contacts(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();
        CrmContact::factory()->create(['name' => 'Belongs To A', 'assigned_to' => $vendedorA->id]);
        CrmContact::factory()->create(['name' => 'Belongs To B', 'assigned_to' => $vendedorB->id]);

        $this->actingAs($vendedorA);

        $html = Livewire::test(GlobalSearch::class)->set('q', 'Belongs')->html();

        $this->assertStringContainsString('Belongs To A', $html);
        $this->assertStringNotContainsString('Belongs To B', $html);
    }

    public function test_short_query_returns_no_results_block(): void
    {
        $admin = User::factory()->admin()->create();
        CrmContact::factory()->create(['name' => 'Anything']);

        $this->actingAs($admin);

        $html = Livewire::test(GlobalSearch::class)->set('q', 'A')->html();

        $this->assertStringNotContainsString('Anything', $html);
    }
}
