<?php

namespace Tests\Feature\Crm;

use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use Tests\TestCase;

class ReengagementAndBackupTest extends TestCase
{
    public function test_creates_followup_task_for_stalled_proposal(): void
    {
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create([
            'crm_contact_id'   => $contact->id,
            'stage'            => 'proposta',
            'stage_changed_at' => now()->subDays(4),
        ]);

        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertOk()->assertJson(['ok' => true, 'stalled' => 1, 'created' => 1]);
        $this->assertTrue(
            CrmActivity::where('crm_deal_id', $deal->id)->where('type', 'tarefa')->exists()
        );
    }

    public function test_does_not_duplicate_task_on_second_run(): void
    {
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create([
            'crm_contact_id'   => $contact->id,
            'stage'            => 'proposta',
            'stage_changed_at' => now()->subDays(4),
        ]);

        $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));
        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertJson(['created' => 0]);
        $this->assertSame(1, CrmActivity::where('crm_deal_id', $deal->id)->where('type', 'tarefa')->count());
    }

    public function test_ignores_proposals_not_yet_stalled(): void
    {
        $contact = CrmContact::factory()->create();
        CrmDeal::factory()->create([
            'crm_contact_id'   => $contact->id,
            'stage'            => 'proposta',
            'stage_changed_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('cron.crm-reengajamento', ['token' => config('app.cron_secret')]));

        $response->assertJson(['ok' => true, 'stalled' => 0, 'created' => 0]);
    }

    public function test_wrong_token_unauthorized(): void
    {
        $this->get(route('cron.crm-reengajamento', ['token' => 'wrong']))->assertStatus(401);
    }

    public function test_backup_creates_sql_file(): void
    {
        $response = $this->get(route('cron.backup', ['token' => config('app.cron_secret')]));

        $response->assertOk();
        $data = $response->json();
        $this->assertTrue($data['ok']);

        $path = storage_path('app/backups/' . $data['file']);
        $this->assertFileExists($path);
        $this->assertGreaterThan(0, filesize($path));

        @unlink($path);
    }

    public function test_backup_wrong_token_unauthorized(): void
    {
        $this->get(route('cron.backup', ['token' => 'wrong']))->assertStatus(401);
    }
}
