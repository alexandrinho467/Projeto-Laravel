<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmActivity;
use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmDealStageHistory;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $scopeToSelf = $user->isVendedor();

        $contactsQuery = CrmContact::query();
        $dealsQuery    = CrmDeal::query();
        $activitiesQuery = CrmActivity::query();

        if ($scopeToSelf) {
            $contactsQuery->where('assigned_to', $user->id);
            $dealsQuery->where('assigned_to', $user->id);
            $activitiesQuery->where('user_id', $user->id);
        }

        $totalContacts   = (clone $contactsQuery)->count();
        $leadsThisWeek    = (clone $contactsQuery)->where('created_at', '>=', now()->startOfWeek())->count();

        $dealsByStage = (clone $dealsQuery)
            ->selectRaw('stage, count(*) as total, sum(value) as valor')
            ->whereNotIn('stage', ['ganho', 'perdido'])
            ->groupBy('stage')
            ->get()
            ->keyBy('stage');

        $wonThisMonth = (clone $dealsQuery)
            ->where('stage', 'ganho')
            ->where('stage_changed_at', '>=', now()->startOfMonth())
            ->sum('value');

        $overdueActivities = (clone $activitiesQuery)
            ->with('contact')
            ->whereNull('completed_at')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $wonDeals = (clone $dealsQuery)
            ->where('stage', 'ganho')
            ->get(['created_at', 'stage_changed_at']);

        $avgConversionDays = $wonDeals->isEmpty() ? null : round(
            $wonDeals->avg(fn ($deal) => $deal->created_at->diffInHours($deal->stage_changed_at) / 24),
            1
        );

        $lostByReason = (clone $dealsQuery)
            ->where('stage', 'perdido')
            ->selectRaw("COALESCE(NULLIF(lost_reason, ''), 'Não informado') as reason, count(*) as total")
            ->groupBy('reason')
            ->orderByDesc('total')
            ->get();

        $lostTotal = $lostByReason->sum('total');

        $avgDaysPerStage = $this->calculateAvgDaysPerStage((clone $dealsQuery)->pluck('id'));

        return view('livewire.admin.crm.dashboard', [
            'totalContacts'      => $totalContacts,
            'leadsThisWeek'      => $leadsThisWeek,
            'dealsByStage'       => $dealsByStage,
            'wonThisMonth'       => $wonThisMonth,
            'overdueActivities'  => $overdueActivities,
            'stages'             => \App\Models\CrmDeal::STAGES,
            'avgConversionDays'  => $avgConversionDays,
            'lostByReason'       => $lostByReason,
            'lostTotal'          => $lostTotal,
            'avgDaysPerStage'    => $avgDaysPerStage,
        ])->extends('layouts.admin', ['title' => 'CRM | Admin']);
    }

    protected function calculateAvgDaysPerStage($dealIds): array
    {
        $rowsByDeal = CrmDealStageHistory::whereIn('crm_deal_id', $dealIds)
            ->orderBy('crm_deal_id')
            ->orderBy('entered_at')
            ->get()
            ->groupBy('crm_deal_id');

        $hoursByStage = [];

        foreach ($rowsByDeal as $rows) {
            $rows = $rows->values();
            for ($i = 0; $i < $rows->count() - 1; $i++) {
                $stage = $rows[$i]->stage;
                $hoursByStage[$stage][] = $rows[$i]->entered_at->diffInHours($rows[$i + 1]->entered_at);
            }
        }

        $avgDays = [];
        foreach ($hoursByStage as $stage => $hours) {
            $avgDays[$stage] = round(array_sum($hours) / count($hours) / 24, 1);
        }

        arsort($avgDays);

        return $avgDays;
    }
}
