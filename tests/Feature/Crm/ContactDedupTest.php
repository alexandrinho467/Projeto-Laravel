<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\ContactsIndex;
use App\Models\CrmContact;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ContactDedupTest extends TestCase
{
    public function test_blocks_creating_contact_with_duplicate_email(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = CrmContact::factory()->create(['email' => 'dup@example.com']);

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('name', 'Someone New')
            ->set('email', 'dup@example.com')
            ->call('createContact');

        $this->assertSame(1, CrmContact::where('email', 'dup@example.com')->count());
    }

    public function test_blocks_creating_contact_with_duplicate_phone(): void
    {
        $admin = User::factory()->admin()->create();
        CrmContact::factory()->create(['phone' => '5511999999999', 'email' => null]);

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('name', 'Someone New')
            ->set('phone', '5511999999999')
            ->call('createContact');

        $this->assertSame(1, CrmContact::where('phone', '5511999999999')->count());
    }

    public function test_allows_creating_contact_with_unique_email_and_phone(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('name', 'Brand New Contact')
            ->set('email', 'unique@example.com')
            ->set('phone', '5511900000001')
            ->call('createContact');

        $this->assertSame(1, CrmContact::where('email', 'unique@example.com')->count());
    }

    public function test_allows_two_contacts_with_no_email_or_phone(): void
    {
        $admin = User::factory()->admin()->create();
        CrmContact::factory()->create(['email' => null, 'phone' => null, 'name' => 'First']);

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('name', 'Second')
            ->call('createContact');

        $this->assertSame(2, CrmContact::count());
    }
}
