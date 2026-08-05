<?php
namespace App\Livewire\Admin\Crm;

use App\Models\CrmActivity;
use Livewire\Attributes\Url;
use Livewire\Component;

class Tasks extends Component
{
    #[Url]
    public string $filter = 'atrasadas';

    public function completeActivity($activityId)
    {
        $activity = CrmActivity::with('contact')->findOrFail($activityId);
        abort_if(auth()->user()->isVendedor() && $activity->contact?->assigned_to !== auth()->id(), 403);

        $activity->update(['completed_at' => now()]);
    }

    public function render()
    {
        $user = auth()->user();

        $query = CrmActivity::with('contact')
            ->where('type', 'tarefa')
            ->whereNull('completed_at')
            ->whereNotNull('due_date');

        if ($user->isVendedor()) {
            $query->whereHas('contact', fn ($q) => $q->where('assigned_to', $user->id));
        }

        $query = match ($this->filter) {
            'hoje'      => $query->whereDate('due_date', now()->toDateString()),
            'amanha'    => $query->whereDate('due_date', now()->addDay()->toDateString()),
            'atrasadas' => $query->where('due_date', '<', now()->startOfDay()),
            default     => $query,
        };

        return view('livewire.admin.crm.tasks', [
            'tasks' => $query->orderBy('due_date')->get(),
        ])->extends('layouts.admin', ['title' => 'Minhas Tarefas | CRM']);
    }
}
