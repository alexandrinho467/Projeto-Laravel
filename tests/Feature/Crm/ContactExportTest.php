<?php

namespace Tests\Feature\Crm;

use App\Models\CrmContact;
use App\Models\User;
use Tests\TestCase;

class ContactExportTest extends TestCase
{
    public function test_admin_can_export_csv(): void
    {
        $admin = User::factory()->admin()->create();
        CrmContact::factory()->create(['name' => 'Export Me']);

        $response = $this->actingAs($admin)->get(route('admin.crm.contacts.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Export Me', $content);
        $this->assertStringContainsString('Nome', $content);
    }

    public function test_vendedor_cannot_export_csv(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.crm.contacts.export'))
            ->assertStatus(403);
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('admin.crm.contacts.export'))
            ->assertRedirect(route('login'));
    }
}
