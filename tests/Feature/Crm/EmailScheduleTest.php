<?php

namespace Tests\Feature\Crm;

use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\SiteSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailScheduleTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_skips_before_configured_hour(): void
    {
        Mail::fake();
        SiteSetting::set('crm_email_hour', '20');
        Carbon::setTestNow(Carbon::parse('2026-07-04 08:00:00'));

        $response = $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]));

        $response->assertJson(['ok' => true, 'skipped' => true]);
        Mail::assertNothingSent();
    }

    public function test_sends_after_configured_hour(): void
    {
        Mail::fake();
        SiteSetting::set('crm_email_hour', '8');
        Carbon::setTestNow(Carbon::parse('2026-07-04 09:00:00'));

        $response = $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]));

        $response->assertJson(['ok' => true]);
        $this->assertArrayNotHasKey('skipped', $response->json());
        $this->assertSame('2026-07-04', SiteSetting::get('crm_followups_last_run'));
    }

    public function test_does_not_send_twice_in_the_same_day(): void
    {
        Mail::fake();
        Carbon::setTestNow(Carbon::parse('2026-07-04 09:00:00'));

        $vendedor = \App\Models\User::factory()->vendedor()->create();
        \App\Models\User::factory()->admin()->create();
        $contact = CrmContact::factory()->create(['assigned_to' => $vendedor->id]);
        CrmActivity::create([
            'crm_contact_id' => $contact->id, 'type' => 'tarefa',
            'description' => 'atrasada', 'due_date' => now()->subDay(),
        ]);

        SiteSetting::set('crm_email_hour', '8');
        $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]));

        Carbon::setTestNow(Carbon::parse('2026-07-04 15:00:00'));
        $response = $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]));

        $response->assertJson(['ok' => true, 'skipped' => true]);
        Mail::assertSentCount(2); // vendedor + admin, only from the first run
    }

    public function test_sends_again_on_a_new_day(): void
    {
        Mail::fake();
        SiteSetting::set('crm_email_hour', '8');

        Carbon::setTestNow(Carbon::parse('2026-07-04 09:00:00'));
        $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]));

        Carbon::setTestNow(Carbon::parse('2026-07-05 09:00:00'));
        $response = $this->get(route('cron.crm-followups', ['token' => config('app.cron_secret')]));

        $response->assertJson(['ok' => true]);
        $this->assertArrayNotHasKey('skipped', $response->json());
    }
}
