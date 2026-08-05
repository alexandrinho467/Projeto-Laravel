<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Tasks;
use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class TasksTest extends TestCase
{
    public function test_filters_overdue_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();

        $overdue = CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'Atrasada', 'due_date' => now()->subDays(2)]);
        CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'Hoje', 'due_date' => now()]);
        CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'Futuro', 'due_date' => now()->addDays(5)]);

        $this->actingAs($admin);

        $html = Livewire::test(Tasks::class)->set('filter', 'atrasadas')->html();

        $this->assertStringContainsString('Atrasada', $html);
        $this->assertStringNotContainsString('Futuro', $html);
    }

    public function test_filters_today_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();

        CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'Tarefa de hoje', 'due_date' => now()]);
        CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'Tarefa amanha', 'due_date' => now()->addDay()]);

        $this->actingAs($admin);

        $html = Livewire::test(Tasks::class)->set('filter', 'hoje')->html();

        $this->assertStringContainsString('Tarefa de hoje', $html);
        $this->assertStringNotContainsString('Tarefa amanha', $html);
    }

    public function test_completed_tasks_are_excluded(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'Concluida', 'due_date' => now()->subDay(), 'completed_at' => now()]);

        $this->actingAs($admin);

        $html = Livewire::test(Tasks::class)->set('filter', 'todas')->html();

        $this->assertStringNotContainsString('Concluida', $html);
    }

    public function test_vendedor_only_sees_own_assigned_tasks(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();
        $contactA = CrmContact::factory()->create(['assigned_to' => $vendedorA->id]);
        $contactB = CrmContact::factory()->create(['assigned_to' => $vendedorB->id]);

        CrmActivity::create(['crm_contact_id' => $contactA->id, 'type' => 'tarefa', 'description' => 'Da vendedora A', 'due_date' => now()->subDay()]);
        CrmActivity::create(['crm_contact_id' => $contactB->id, 'type' => 'tarefa', 'description' => 'Da vendedora B', 'due_date' => now()->subDay()]);

        $this->actingAs($vendedorA);

        $html = Livewire::test(Tasks::class)->set('filter', 'atrasadas')->html();

        $this->assertStringContainsString('Da vendedora A', $html);
        $this->assertStringNotContainsString('Da vendedora B', $html);
    }

    public function test_complete_activity_marks_it_done(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $activity = CrmActivity::create(['crm_contact_id' => $contact->id, 'type' => 'tarefa', 'description' => 'x', 'due_date' => now()->subDay()]);

        $this->actingAs($admin);

        Livewire::test(Tasks::class)->call('completeActivity', $activity->id);

        $this->assertNotNull($activity->fresh()->completed_at);
    }
}
