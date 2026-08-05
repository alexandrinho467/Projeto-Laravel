<?php

namespace Tests\Feature\Crm;

use App\Livewire\Admin\Crm\Dashboard;
use App\Livewire\Admin\Crm\Pipeline;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmDealStageHistory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class StageHistoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_creating_a_deal_records_first_stage_history_row(): void
    {
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'novo_lead']);

        $this->assertSame(1, CrmDealStageHistory::where('crm_deal_id', $deal->id)->count());
        $this->assertSame('novo_lead', CrmDealStageHistory::where('crm_deal_id', $deal->id)->first()->stage);
    }

    public function test_changing_stage_records_a_new_history_row(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'novo_lead']);
        $this->actingAs($admin);

        Livewire::test(Pipeline::class)->call('updateStage', $deal->id, 'qualificado', [$deal->id]);

        $this->assertSame(2, CrmDealStageHistory::where('crm_deal_id', $deal->id)->count());
        $this->assertSame('qualificado', CrmDealStageHistory::where('crm_deal_id', $deal->id)->latest('id')->first()->stage);
    }

    public function test_bottleneck_report_calculates_average_days_in_stage(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();

        Carbon::setTestNow(Carbon::parse('2026-07-01 00:00:00'));
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'novo_lead']);

        Carbon::setTestNow(Carbon::parse('2026-07-03 00:00:00')); // 2 dias em novo_lead
        $deal->update(['stage' => 'qualificado', 'stage_changed_at' => now()]);

        Carbon::setTestNow(Carbon::parse('2026-07-07 00:00:00')); // 4 dias em qualificado
        $deal->update(['stage' => 'ganho', 'stage_changed_at' => now()]);

        $this->actingAs($admin);
        $html = Livewire::test(Dashboard::class)->html();

        $this->assertStringContainsString('2 dias', $html);
        $this->assertStringContainsString('4 dias', $html);
    }

    public function test_current_open_stage_does_not_count_in_average(): void
    {
        $admin = User::factory()->admin()->create();
        $contact = CrmContact::factory()->create();
        CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'stage' => 'novo_lead']);

        $this->actingAs($admin);
        $html = Livewire::test(Dashboard::class)->html();

        $this->assertStringContainsString('Ainda não há negócios', $html);
    }
}
