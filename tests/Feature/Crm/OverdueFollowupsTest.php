<?php

namespace Tests\Feature\Crm;

use App\Mail\CrmOverdueFollowupsMail;
use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OverdueFollowupsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::set('crm_email_hour', '0');
    }

    public function test_notifies_assigned_vendedor_and_all_admins(): void
    {
        Mail::fake();

        $vendedor = User::factory()->vendedor()->create();
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create(['assigned_to' => $vendedor->id]);

        $overdue = CrmActivity::create([
            'crm_contact_id' => $contact->id,
            'type'           => 'tarefa',
            'description'    => 'Ligar de volta',
            'due_date'       => now()->subDays(2),
        ]);

        $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]))
            ->assertOk()
            ->assertJson(['ok' => true, 'overdue' => 1]);

        Mail::assertSent(CrmOverdueFollowupsMail::class, fn ($mail) => $mail->hasTo($vendedor->email));
        Mail::assertSent(CrmOverdueFollowupsMail::class, fn ($mail) => $mail->hasTo($admin->email));
    }

    public function test_ignores_activities_not_yet_due_or_already_completed(): void
    {
        Mail::fake();

        $contact = CrmContact::factory()->create();

        CrmActivity::create([
            'crm_contact_id' => $contact->id, 'type' => 'tarefa',
            'description' => 'futuro', 'due_date' => now()->addDays(2),
        ]);
        CrmActivity::create([
            'crm_contact_id' => $contact->id, 'type' => 'tarefa',
            'description' => 'feito', 'due_date' => now()->subDays(2), 'completed_at' => now(),
        ]);

        $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]))
            ->assertJson(['ok' => true, 'overdue' => 0]);
    }

    public function test_wrong_token_is_unauthorized(): void
    {
        $this->get(route('cron.crm-followups', ['token' => 'wrong']))
            ->assertStatus(401);
    }
}
