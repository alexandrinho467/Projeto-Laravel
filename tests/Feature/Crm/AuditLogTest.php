<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\AuditLogViewer;
use App\Livewire\Admin\Crm\ContactShow;
use App\Livewire\Admin\Crm\ContactsIndex;
use App\Models\CrmAuditLog;
use App\Models\CrmContact;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    public function test_viewing_a_contact_logs_it(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create(['name' => 'Auditado']);
        $this->actingAs($admin);

        Livewire::test(ContactShow::class, ['contact' => $contact]);

        $this->assertTrue(
            CrmAuditLog::where('user_id', $admin->id)->where('description', 'like', '%Auditado%')->exists()
        );
    }

    public function test_archiving_a_contact_logs_it(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $this->actingAs($admin);

        Livewire::test(ContactShow::class, ['contact' => $contact])->call('toggleArchive');

        $this->assertTrue(CrmAuditLog::where('action', 'archive_contact')->exists());
    }

    public function test_bulk_archive_logs_a_single_entry_with_count(): void
    {
        $admin = User::factory()->admin()->create();
        $c1 = CrmContact::factory()->create();
        $c2 = CrmContact::factory()->create();
        $this->actingAs($admin);

        Livewire::test(ContactsIndex::class)
            ->set('selected', [$c1->id, $c2->id])
            ->call('bulkArchive');

        $log = CrmAuditLog::where('action', 'bulk_archive_contacts')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('2 contato', $log->description);
    }

    public function test_export_logs_it(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->get(route('admin.crm.contacts.export'));

        $this->assertTrue(CrmAuditLog::where('action', 'export_contacts')->exists());
    }

    public function test_viewer_is_admin_only(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.crm.audit'))
            ->assertStatus(403);
    }

    public function test_viewer_lists_logs_most_recent_first(): void
    {
        $admin = User::factory()->admin()->create();
        CrmAuditLog::create(['user_id' => $admin->id, 'action' => 'a', 'description' => 'Ação antiga', 'created_at' => now()->subDay()]);
        CrmAuditLog::create(['user_id' => $admin->id, 'action' => 'b', 'description' => 'Ação recente', 'created_at' => now()]);

        $this->actingAs($admin);

        $html = Livewire::test(AuditLogViewer::class)->html();

        $this->assertTrue(strpos($html, 'Ação recente') < strpos($html, 'Ação antiga'));
    }
}
