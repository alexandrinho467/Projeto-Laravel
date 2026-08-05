<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\ContactShow;
use App\Livewire\Admin\Crm\ContactsIndex;
use App\Models\CrmContact;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ArchiveTest extends TestCase
{
    public function test_archiving_a_contact_hides_it_from_default_listing(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create(['name' => 'To Archive']);

        $this->actingAs($admin);

        Livewire::test(ContactShow::class, ['contact' => $contact])->call('toggleArchive');

        $this->assertNotNull($contact->fresh()->archived_at);

        $html = Livewire::test(ContactsIndex::class)->html();
        $this->assertStringNotContainsString('To Archive', $html);

        $archivedHtml = Livewire::test(ContactsIndex::class)->set('showArchived', true)->html();
        $this->assertStringContainsString('To Archive', $archivedHtml);
    }

    public function test_unarchiving_restores_visibility(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create(['archived_at' => now()]);

        $this->actingAs($admin);

        Livewire::test(ContactShow::class, ['contact' => $contact])->call('toggleArchive');

        $this->assertNull($contact->fresh()->archived_at);
    }
}
