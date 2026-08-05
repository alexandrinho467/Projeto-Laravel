<?php

namespace Tests\Feature\Crm;

use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class OrderCrmSyncTest extends TestCase
{
    public function test_sync_creates_contact_and_deal_for_logged_in_user(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->forUser($user)->create();

        $order->syncToCrm();

        $contact = CrmContact::where('user_id', $user->id)->first();
        $this->assertNotNull($contact);
        $this->assertSame('ativo', $contact->status);

        $deal = CrmDeal::where('order_id', $order->id)->first();
        $this->assertNotNull($deal);
        $this->assertSame('negociacao', $deal->stage);
        $this->assertEquals($order->total, $deal->value);
    }

    public function test_sync_is_idempotent(): void
    {
        $order = Order::factory()->create();

        $order->syncToCrm();
        $order->syncToCrm();

        $this->assertSame(1, CrmDeal::where('order_id', $order->id)->count());
    }

    public function test_sync_creates_contact_for_guest_checkout(): void
    {
        $order = Order::factory()->create(['user_id' => null, 'guest_email' => 'guest@example.com']);

        $order->syncToCrm();

        $contact = CrmContact::where('email', 'guest@example.com')->first();
        $this->assertNotNull($contact);
        $this->assertNull($contact->user_id);
    }

    public function test_two_orders_same_email_reuse_contact_but_create_separate_deals(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order1 = Order::factory()->forUser($user)->create();
        $order2 = Order::factory()->forUser($user)->create();

        $order1->syncToCrm();
        $order2->syncToCrm();

        $this->assertSame(1, CrmContact::where('user_id', $user->id)->count());
        $this->assertSame(2, CrmDeal::count());
    }

    public function test_mark_won_transitions_deal_and_logs_activity(): void
    {
        $order = Order::factory()->create();
        $order->syncToCrm();

        $order->markCrmDealWon();

        $deal = CrmDeal::where('order_id', $order->id)->first();
        $this->assertSame('ganho', $deal->stage);
        $this->assertTrue($deal->activities()->where('description', 'like', '%confirmado%')->exists());
    }

    public function test_mark_lost_sets_reason_and_does_not_downgrade_a_won_deal(): void
    {
        $order = Order::factory()->create();
        $order->syncToCrm();
        $order->markCrmDealWon();

        $order->markCrmDealLost('teste pos-ganho');

        $deal = CrmDeal::where('order_id', $order->id)->first();
        $this->assertSame('ganho', $deal->stage);
    }

    public function test_mark_lost_on_pending_deal_sets_perdido(): void
    {
        $order = Order::factory()->create();
        $order->syncToCrm();

        $order->markCrmDealLost('Pagamento expirado');

        $deal = CrmDeal::where('order_id', $order->id)->first();
        $this->assertSame('perdido', $deal->stage);
        $this->assertSame('Pagamento expirado', $deal->lost_reason);
    }

    public function test_mark_won_on_order_without_crm_deal_does_not_throw(): void
    {
        $order = Order::factory()->create();

        $order->markCrmDealWon();

        $this->assertSame(0, CrmDeal::where('order_id', $order->id)->count());
    }

    public function test_existing_lead_contact_is_promoted_to_ativo_on_purchase(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        CrmContact::factory()->create(['user_id' => $user->id, 'status' => 'lead']);

        $order = Order::factory()->forUser($user)->create();
        $order->syncToCrm();

        $contact = CrmContact::where('user_id', $user->id)->first();
        $this->assertSame('ativo', $contact->status);
    }
}
