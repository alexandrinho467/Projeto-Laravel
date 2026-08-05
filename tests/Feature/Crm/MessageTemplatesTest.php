<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\MessageTemplates;
use App\Models\CrmMessageTemplate;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class MessageTemplatesTest extends TestCase
{
    public function test_admin_can_create_template(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(MessageTemplates::class)
            ->call('openCreate')
            ->set('name', 'Boas-vindas')
            ->set('body', 'Olá {{nome}}, tudo bem?')
            ->call('save');

        $this->assertTrue(CrmMessageTemplate::where('name', 'Boas-vindas')->exists());
    }

    public function test_admin_can_edit_and_delete_template(): void
    {
        $admin = User::factory()->admin()->create();
        $template = CrmMessageTemplate::factory()->create();
        $this->actingAs($admin);

        Livewire::test(MessageTemplates::class)
            ->call('openEdit', $template->id)
            ->set('name', 'Atualizado')
            ->call('save');

        $this->assertSame('Atualizado', $template->fresh()->name);

        Livewire::test(MessageTemplates::class)->call('delete', $template->id);
        $this->assertDatabaseMissing('crm_message_templates', ['id' => $template->id]);
    }

    public function test_vendedor_can_view_but_not_mutate(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $template = CrmMessageTemplate::factory()->create();
        $this->actingAs($vendedor);

        $this->get(route('admin.crm.messages'))->assertOk();

        Livewire::test(MessageTemplates::class)->call('openCreate')->assertStatus(403);
        Livewire::test(MessageTemplates::class)->call('delete', $template->id)->assertStatus(403);

        $this->assertDatabaseHas('crm_message_templates', ['id' => $template->id]);
    }
}
