<div>
  <div class="admin-topbar">
    <h1 class="admin-title">Minhas Tarefas</h1>
  </div>

  <div style="display:flex;gap:8px;margin-bottom:18px">
    @foreach(['atrasadas' => 'Atrasadas', 'hoje' => 'Hoje', 'amanha' => 'Amanhã', 'todas' => 'Todas'] as $key => $label)
      <button type="button"
        wire:click="$set('filter', '{{ $key }}')"
        class="{{ $filter === $key ? 'btn-primary' : 'btn-secondary' }}"
        style="padding:6px 14px;font-size:.82rem">{{ $label }}</button>
    @endforeach
  </div>

  @if($tasks->isEmpty())
    <div style="text-align:center;padding:60px;color:#697386">
      <div style="font-size:2rem;margin-bottom:12px">✅</div>
      <div style="font-weight:600;color:#374151">Nenhuma tarefa por aqui.</div>
    </div>
  @else
    <table class="admin-table">
      <thead>
        <tr><th>Contato</th><th>Tarefa</th><th>Prazo</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($tasks as $task)
        <tr>
          <td>
            @if($task->contact)
              <a href="{{ route('admin.crm.contacts.show', $task->contact) }}">{{ $task->contact->name }}</a>
            @else
              <span style="color:#9CA3AF">Contato removido</span>
            @endif
          </td>
          <td style="color:#697386">{{ $task->description }}</td>
          <td>
            @if($task->due_date->isPast())
              <span class="badge badge-red">{{ $task->due_date->format('d/m/Y') }}</span>
            @elseif($task->due_date->isToday())
              <span class="badge badge-yellow">Hoje</span>
            @else
              <span class="badge badge-blue">{{ $task->due_date->format('d/m/Y') }}</span>
            @endif
          </td>
          <td><button type="button" class="btn-secondary" style="padding:6px 12px;font-size:.78rem" wire:click="completeActivity({{ $task->id }})">Concluir</button></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
