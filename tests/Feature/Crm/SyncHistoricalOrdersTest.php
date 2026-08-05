<?php

namespace Tests\Feature\Crm;

use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\Order;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SyncHistoricalOrdersTest extends TestCase
{
    public function test_command_syncs_orders_without_a_deal(): void
    {
        $paidOrder = Order::factory()->create(['payment_status' => 'paid', 'guest_email' => 'paid@example.com']);
        $cancelledOrder = Order::factory()->create(['status' => 'cancelled', 'guest_email' => 'cancelled@example.com']);
        $pendingOrder = Order::factory()->create(['payment_status' => 'pending', 'guest_email' => 'pending@example.com']);

        Artisan::call('crm:sync-historical-orders');

        $paidDeal = CrmDeal::where('order_id', $paidOrder->id)->first();
        $this->assertSame('ganho', $paidDeal->stage);

        $cancelledDeal = CrmDeal::where('order_id', $cancelledOrder->id)->first();
        $this->assertSame('perdido', $cancelledDeal->stage);
        $this->assertSame('Sincronização retroativa', $cancelledDeal->lost_reason);

        $pendingDeal = CrmDeal::where('order_id', $pendingOrder->id)->first();
        $this->assertSame('negociacao', $pendingDeal->stage);

        $this->assertTrue(CrmContact::where('email', 'paid@example.com')->exists());
    }

    public function test_command_does_not_duplicate_on_second_run(): void
    {
        Order::factory()->create(['payment_status' => 'paid']);

        Artisan::call('crm:sync-historical-orders');
        $countAfterFirst = CrmDeal::count();

        Artisan::call('crm:sync-historical-orders');
        $countAfterSecond = CrmDeal::count();

        $this->assertSame($countAfterFirst, $countAfterSecond);
        $this->assertSame(1, $countAfterFirst);
    }

    public function test_orders_already_synced_are_skipped(): void
    {
        $order = Order::factory()->create();
        $order->syncToCrm();

        $output = Artisan::call('crm:sync-historical-orders');

        $this->assertSame(1, CrmDeal::where('order_id', $order->id)->count());
    }
}
