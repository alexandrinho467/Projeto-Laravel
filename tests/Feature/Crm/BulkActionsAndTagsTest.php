<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\ContactShow;
use App\Livewire\Admin\Crm\ContactsIndex;
use App\Models\CrmContact;
use App\Models\CrmTag;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class BulkActionsAndTagsTest extends TestCase
{
    public function test_bulk_assign_changes_responsible_for_all_selected(): void
    {
        $admin = User::factory()->admin()->create();
        $vendedor = User::factory()->vendedor()->create();
        $c1 = CrmContact::factory()->create();
        $c2 = CrmContact::factory()->create();

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('selected', [$c1->id, $c2->id])
            ->set('bulkAssignedTo', (string) $vendedor->id)
            ->call('bulkAssign');

        $this->assertSame($vendedor->id, $c1->fresh()->assigned_to);
        $this->assertSame($vendedor->id, $c2->fresh()->assigned_to);
    }

    public function test_bulk_archive_archives_all_selected(): void
    {
        $admin = User::factory()->admin()->create();
        $c1 = CrmContact::factory()->create();
        $c2 = CrmContact::factory()->create();

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('selected', [$c1->id, $c2->id])
            ->call('bulkArchive');

        $this->assertNotNull($c1->fresh()->archived_at);
        $this->assertNotNull($c2->fresh()->archived_at);
    }

    public function test_bulk_add_tag_creates_tag_and_attaches_to_all_selected(): void
    {
        $admin = User::factory()->admin()->create();
        $c1 = CrmContact::factory()->create();
        $c2 = CrmContact::factory()->create();

        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('selected', [$c1->id, $c2->id])
            ->set('bulkTagName', 'VIP')
            ->call('bulkAddTag');

        $this->assertSame(1, CrmTag::where('name', 'VIP')->count());
        $this->assertTrue($c1->fresh()->tags->pluck('name')->contains('VIP'));
        $this->assertTrue($c2->fresh()->tags->pluck('name')->contains('VIP'));
    }

    public function test_add_and_remove_tag_from_contact_show(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();

        $this->actingAs($admin);

        $component = Livewire::test(ContactShow::class, ['contact' => $contact])
            ->set('newTagName', 'Premium')
            ->call('addTag');

        $tag = CrmTag::where('name', 'Premium')->first();
        $this->assertNotNull($tag);
        $this->assertTrue($contact->fresh()->tags->contains($tag));

        $component->call('removeTag', $tag->id);
        $this->assertFalse($contact->fresh()->tags->contains($tag));
    }
}
