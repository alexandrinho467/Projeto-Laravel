<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmDeal extends Model
{
    use HasFactory;

    protected $table = 'crm_deals';
    protected $fillable = ['crm_contact_id','assigned_to','order_id','title','value','stage','lost_reason','position','stage_changed_at','checklist_state'];
    protected $casts    = ['value' => 'decimal:2', 'stage_changed_at' => 'datetime', 'checklist_state' => 'array'];

    const STAGES = ['novo_lead','contato_feito','qualificado','proposta','negociacao','ganho','perdido'];

    protected static function booted(): void
    {
        static::created(function (CrmDeal $deal) {
            CrmDealStageHistory::create([
                'crm_deal_id' => $deal->id,
                'stage'       => $deal->stage,
                'entered_at'  => $deal->stage_changed_at ?? $deal->created_at ?? now(),
            ]);
        });

        static::updated(function (CrmDeal $deal) {
            if ($deal->wasChanged('stage')) {
                CrmDealStageHistory::create([
                    'crm_deal_id' => $deal->id,
                    'stage'       => $deal->stage,
                    'entered_at'  => $deal->stage_changed_at ?? now(),
                ]);
            }
        });
    }

    public function stageHistory() { return $this->hasMany(CrmDealStageHistory::class)->orderBy('entered_at'); }

    public function checklistItems(): array {
        return CrmStageChecklistItem::where('stage', $this->stage)
            ->orderBy('position')
            ->pluck('label', 'key')
            ->all();
    }

    public function checklistProgress(): array {
        $items = $this->checklistItems();
        if (empty($items)) return [0, 0];

        $state = $this->checklist_state ?? [];
        $done  = collect(array_keys($items))->filter(fn ($key) => !empty($state[$key]))->count();

        return [$done, count($items)];
    }

    public function contact()         { return $this->belongsTo(CrmContact::class, 'crm_contact_id'); }
    public function assignee()        { return $this->belongsTo(User::class, 'assigned_to'); }
    public function order()           { return $this->belongsTo(Order::class); }
    public function activities()      { return $this->hasMany(CrmActivity::class)->latest(); }
    public function channelMessages() { return $this->hasMany(CrmChannelMessage::class)->orderBy('occurred_at'); }

    public function getStageLabelAttribute(): string {
        return match($this->stage) {
            'novo_lead'     => 'Novo Lead',
            'contato_feito' => 'Contato Feito',
            'qualificado'   => 'Qualificado',
            'proposta'      => 'Proposta',
            'negociacao'    => 'Negociação',
            'ganho'         => 'Ganho',
            'perdido'       => 'Perdido',
            default         => $this->stage,
        };
    }

    public function getValueFormattedAttribute(): string {
        return 'AED ' . number_format($this->value, 2, '.', ',');
    }
}
