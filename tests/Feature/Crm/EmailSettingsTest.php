<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\EmailSettings;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class EmailSettingsTest extends TestCase
{
    public function test_vendedor_can_access_own_email_settings(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->get(route('admin.crm.email-settings'))
            ->assertOk();
    }

    public function test_admin_can_access_email_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.crm.email-settings'))
            ->assertOk();
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('admin.crm.email-settings'))->assertRedirect(route('login'));
    }

    public function test_vendedor_can_save_own_imap_settings(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $this->actingAs($vendedor);

        Livewire::test(EmailSettings::class)
            ->set('imapHost', 'imap.gmail.com')
            ->set('imapPort', '993')
            ->set('imapUsername', 'vendedor@gmail.com')
            ->set('imapPassword', 'senha-de-app')
            ->set('imapEncryption', 'ssl')
            ->set('imapFolder', 'INBOX')
            ->call('save')
            ->assertSet('hasStoredPassword', true)
            ->assertSet('imapPassword', '');

        $vendedor->refresh();
        $this->assertSame('imap.gmail.com', $vendedor->imap_host);
        $this->assertSame('vendedor@gmail.com', $vendedor->imap_username);
        $this->assertSame('senha-de-app', $vendedor->imap_password);
        $this->assertTrue($vendedor->hasEmailSyncConfigured());
    }

    public function test_saving_without_password_keeps_existing_one(): void
    {
        $vendedor = User::factory()->vendedor()->create([
            'imap_host' => 'imap.gmail.com',
            'imap_username' => 'vendedor@gmail.com',
            'imap_password' => 'senha-antiga',
        ]);
        $this->actingAs($vendedor);

        Livewire::test(EmailSettings::class)
            ->set('imapHost', 'imap.outlook.com')
            ->call('save');

        $this->assertSame('senha-antiga', $vendedor->fresh()->imap_password);
        $this->assertSame('imap.outlook.com', $vendedor->fresh()->imap_host);
    }

    public function test_vendedor_can_disconnect_mailbox(): void
    {
        $vendedor = User::factory()->vendedor()->create([
            'imap_host' => 'imap.gmail.com',
            'imap_username' => 'vendedor@gmail.com',
            'imap_password' => 'senha-antiga',
        ]);
        $this->actingAs($vendedor);

        Livewire::test(EmailSettings::class)->call('disconnect');

        $vendedor->refresh();
        $this->assertNull($vendedor->imap_host);
        $this->assertNull($vendedor->imap_password);
        $this->assertFalse($vendedor->hasEmailSyncConfigured());
    }

    public function test_test_connection_requires_host_username_and_password(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $this->actingAs($vendedor);

        Livewire::test(EmailSettings::class)
            ->call('testConnection')
            ->assertSet('testResult', false);
    }
}
