<?php

namespace Tests\Feature\Crm;

use App\Mail\CrmAbandonedLeadsMail;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AbandonedLeadsTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_notifies_admin_about_lead_abandoned_24h_plus(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        SiteSetting::set('crm_email_hour', '8');
        Carbon::setTestNow(Carbon::parse('2026-07-05 09:00:00'));

        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create([
            'crm_contact_id' => $contact->id,
            'stage'          => 'novo_lead',
            'created_at'     => Carbon::parse('2026-07-04 08:00:00'),
        ]);

        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertJson(['ok' => true, 'abandoned_email_sent' => true]);
        Mail::assertSent(CrmAbandonedLeadsMail::class, fn ($mail) => $mail->hasTo($admin->email) && $mail->deals->contains('id', $deal->id));
    }

    public function test_ignores_leads_younger_than_24h(): void
    {
        Mail::fake();
        User::factory()->admin()->create();
        SiteSetting::set('crm_email_hour', '8');
        Carbon::setTestNow(Carbon::parse('2026-07-05 09:00:00'));

        $contact = CrmContact::factory()->create();
        CrmDeal::factory()->create([
            'crm_contact_id' => $contact->id,
            'stage'          => 'novo_lead',
            'created_at'     => Carbon::parse('2026-07-05 07:00:00'),
        ]);

        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertJson(['abandoned_email_sent' => false]);
        Mail::assertNothingSent();
    }

    public function test_ignores_leads_already_past_novo_lead_stage(): void
    {
        Mail::fake();
        User::factory()->admin()->create();
        SiteSetting::set('crm_email_hour', '8');
        Carbon::setTestNow(Carbon::parse('2026-07-05 09:00:00'));

        $contact = CrmContact::factory()->create();
        CrmDeal::factory()->create([
            'crm_contact_id' => $contact->id,
            'stage'          => 'contato_feito',
            'created_at'     => Carbon::parse('2026-07-04 08:00:00'),
        ]);

        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertJson(['abandoned_email_sent' => false]);
        Mail::assertNothingSent();
    }

    public function test_does_not_send_twice_same_day(): void
    {
        Mail::fake();
        User::factory()->admin()->create();
        SiteSetting::set('crm_email_hour', '8');
        Carbon::setTestNow(Carbon::parse('2026-07-05 09:00:00'));

        $contact = CrmContact::factory()->create();
        CrmDeal::factory()->create([
            'crm_contact_id' => $contact->id,
            'stage'          => 'novo_lead',
            'created_at'     => Carbon::parse('2026-07-04 08:00:00'),
        ]);

        $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        Carbon::setTestNow(Carbon::parse('2026-07-05 15:00:00'));
        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertJson(['abandoned_email_sent' => false]);
        Mail::assertSentCount(1);
    }
}
