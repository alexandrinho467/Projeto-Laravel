<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model
{
    protected $table = 'crm_activities';
    protected $fillable = ['crm_contact_id','crm_deal_id','user_id','type','description','due_date','completed_at'];
    protected $casts    = ['due_date' => 'datetime', 'completed_at' => 'datetime'];

    public function contact() { return $this->belongsTo(CrmContact::class, 'crm_contact_id'); }
    public function deal()    { return $this->belongsTo(CrmDeal::class, 'crm_deal_id'); }
    public function author()  { return $this->belongsTo(User::class, 'user_id'); }

    public function getIsOverdueAttribute(): bool {
        return $this->due_date && !$this->completed_at && $this->due_date->isPast();
    }

    public function getTypeLabelAttribute(): string {
        return match($this->type) {
            'nota'     => 'Nota',
            'ligacao'  => 'Ligação',
            'email'    => 'E-mail',
            'whatsapp' => 'WhatsApp',
            'reuniao'  => 'Reunião',
            'tarefa'   => 'Tarefa',
            default    => $this->type,
        };
    }
}
