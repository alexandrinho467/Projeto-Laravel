<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\ContactsIndex;
use App\Models\CrmContact;
use App\Models\Order;
use App\Models\User;
use App\Support\PhoneFormatter;
use Livewire\Livewire;
use Tests\TestCase;

class PhoneFormattingTest extends TestCase
{
    public function test_uae_local_number_formats_to_e164(): void
    {
        $this->assertSame('+971501234567', PhoneFormatter::toE164('0501234567'));
    }

    public function test_number_already_with_plus_is_parsed_directly(): void
    {
        $this->assertSame('+14155552671', PhoneFormatter::toE164('+1 415 555 2671'));
    }

    public function test_invalid_number_falls_back_to_raw_value(): void
    {
        $this->assertSame('abc', PhoneFormatter::toE164('abc'));
    }

    public function test_null_and_empty_pass_through(): void
    {
        $this->assertNull(PhoneFormatter::toE164(null));
        $this->assertSame('', PhoneFormatter::toE164(''));
    }

    public function test_creating_contact_formats_phone_to_e164(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('name', 'Phone Test')
            ->set('phone', '0501234567')
            ->call('createContact');

        $contact = CrmContact::where('name', 'Phone Test')->first();
        $this->assertSame('+971501234567', $contact->phone);
    }

    public function test_order_sync_formats_guest_phone_to_e164(): void
    {
        $order = Order::factory()->create(['guest_phone' => '0501234567', 'guest_email' => 'e164test@example.com']);

        $order->syncToCrm();

        $contact = CrmContact::where('email', 'e164test@example.com')->first();
        $this->assertSame('+971501234567', $contact->phone);
    }
}
