<div>
  <div class="admin-topbar">
    <h1 class="admin-title">CRM</h1>
    <div style="display:flex;gap:10px">
      <a href="{{ route('admin.crm.pipeline') }}" class="btn-secondary">Pipeline</a>
      <a href="{{ route('admin.crm.contacts.index') }}" class="btn-primary">+ Novo Contato</a>
    </div>
  </div>

  <div class="stat-grid">
    <div class="stat-card">
      <div class="label">Contatos</div>
      <div class="value">{{ $totalContacts }}</div>
      <div class="sub">{{ $leadsThisWeek }} novos esta semana</div>
    </div>
    <div class="stat-card">
      <div class="label">Ganho este mês</div>
      <div class="value">AED {{ number_format($wonThisMonth, 2, '.', ',') }}</div>
    </div>
    <div class="stat-card">
      <div class="label">Tarefas atrasadas</div>
      <div class="value">{{ $overdueActivities->count() }}</div>
    </div>
    <div class="stat-card">
      <div class="label">Tempo médio de conversão</div>
      <div class="value">{{ $avgConversionDays !== null ? $avgConversionDays . ' dias' : '—' }}</div>
      <div class="sub">Do lead ao "Ganho"</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1.3fr 1fr;gap:20px;align-items:start">
    <div>
      <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px;color:#1A1F36">Funil de vendas</h2>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Estágio</th>
            <th>Negócios</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          @foreach($stages as $stage)
            @if(!in_array($stage, ['ganho','perdido']))
            <tr>
              <td>{{ \App\Models\CrmDeal::make(['stage' => $stage])->stage_label }}</td>
              <td>{{ $dealsByStage[$stage]->total ?? 0 }}</td>
              <td>AED {{ number_format($dealsByStage[$stage]->valor ?? 0, 2, '.', ',') }}</td>
            </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>

    <div>
      <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px;color:#1A1F36">Próximas tarefas atrasadas</h2>
      @if($overdueActivities->isEmpty())
        <div style="color:#697386;font-size:.85rem">Nenhuma tarefa atrasada. 🎉</div>
      @else
        <table class="admin-table">
          <thead>
            <tr>
              <th>Contato</th>
              <th>Tarefa</th>
              <th>Prazo</th>
            </tr>
          </thead>
          <tbody>
            @foreach($overdueActivities as $activity)
            <tr>
              <td>
                <a href="{{ route('admin.crm.contacts.show', $activity->contact) }}">{{ $activity->contact->name }}</a>
              </td>
              <td style="color:#697386">{{ \Illuminate\Support\Str::limit($activity->description, 40) }}</td>
              <td><span class="badge badge-red">{{ $activity->due_date->format('d/m/Y') }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>

  <div class="viz-root" style="margin-top:24px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px;color:#1A1F36">Motivo de perda</h2>

    @if($lostTotal === 0)
      <div style="color:#697386;font-size:.85rem">Nenhum negócio perdido registrado ainda.</div>
    @else
      @php
        $palette = ['#2a78d6','#1baf7a','#eda100','#008300','#4a3aa7','#e34948','#e87ba4','#eb6834'];
        $segments = $lostByReason->take(7);
        $otherTotal = $lostByReason->slice(7)->sum('total');
        if ($otherTotal > 0) {
          $segments = $segments->push((object) ['reason' => 'Outro', 'total' => $otherTotal]);
        }
        $circumference = 2 * M_PI * 60;
        $cumulative = 0;
      @endphp
      <div style="display:flex;gap:32px;align-items:center;flex-wrap:wrap">
        <svg width="160" height="160" viewBox="0 0 160 160" style="flex-shrink:0;transform:rotate(-90deg)">
          <circle cx="80" cy="80" r="60" fill="none" stroke="#F1F5F9" stroke-width="20"></circle>
          @foreach($segments as $i => $seg)
            @php
              $pct = $seg->total / $lostTotal;
              $dash = $pct * $circumference;
              $offset = -$cumulative * $circumference;
              $cumulative += $pct;
            @endphp
            <circle cx="80" cy="80" r="60" fill="none" stroke="{{ $palette[$i % count($palette)] }}"
              stroke-width="20" stroke-dasharray="{{ $dash }} {{ $circumference }}" stroke-dashoffset="{{ $offset }}"></circle>
          @endforeach
        </svg>

        <table class="admin-table" style="max-width:360px">
          <thead>
            <tr><th></th><th>Motivo</th><th>Negócios</th><th>%</th></tr>
          </thead>
          <tbody>
            @foreach($segments as $i => $seg)
              <tr>
                <td><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:{{ $palette[$i % count($palette)] }}"></span></td>
                <td>{{ $seg->reason }}</td>
                <td>{{ $seg->total }}</td>
                <td>{{ round($seg->total / $lostTotal * 100) }}%</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <div class="viz-root" style="margin-top:24px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:4px;color:#1A1F36">Gargalo do funil</h2>
    <p style="color:#697386;font-size:.82rem;margin-bottom:14px">Tempo médio que um negócio passa em cada estágio antes de avançar.</p>

    @if(empty($avgDaysPerStage))
      <div style="color:#697386;font-size:.85rem">Ainda não há negócios que passaram de estágio suficientes pra calcular.</div>
    @else
      @php $maxDays = max($avgDaysPerStage); @endphp
      <div style="display:flex;flex-direction:column;gap:10px;max-width:520px">
        @foreach($avgDaysPerStage as $stage => $days)
          <div>
            <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:4px">
              <span style="color:#1A1F36;font-weight:600">{{ \App\Models\CrmDeal::make(['stage' => $stage])->stage_label }}</span>
              <span style="color:#697386">{{ $days }} {{ $days == 1 ? 'dia' : 'dias' }}</span>
            </div>
            <div style="background:#F1F5F9;border-radius:4px;height:10px;overflow:hidden">
              <div style="background:#2a78d6;height:100%;border-radius:4px;width:{{ $maxDays > 0 ? round($days / $maxDays * 100) : 0 }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
