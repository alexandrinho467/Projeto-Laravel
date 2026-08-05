<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmContact extends Model
{
    use HasFactory;

    protected $table = 'crm_contacts';
    protected $fillable = ['user_id','assigned_to','name','email','phone','whatsapp_number','instagram_user_id','instagram_username','source','status','notes','archived_at'];
    protected $casts    = ['archived_at' => 'datetime'];

    public function user()            { return $this->belongsTo(User::class); }
    public function assignee()        { return $this->belongsTo(User::class, 'assigned_to'); }
    public function deals()           { return $this->hasMany(CrmDeal::class); }
    public function activities()      { return $this->hasMany(CrmActivity::class)->latest(); }
    public function tags()            { return $this->belongsToMany(CrmTag::class, 'crm_contact_tag'); }
    public function channelMessages() { return $this->hasMany(CrmChannelMessage::class)->latest('occurred_at'); }

    public function scopeActive($query)   { return $query->whereNull('archived_at'); }
    public function scopeArchived($query) { return $query->whereNotNull('archived_at'); }

    public function getIsArchivedAttribute(): bool {
        return $this->archived_at !== null;
    }

    public function getLtvAttribute(): float {
        return (float) $this->deals()->where('stage', 'ganho')->sum('value');
    }

    public function getLtvFormattedAttribute(): string {
        return 'AED ' . number_format($this->ltv, 2, '.', ',');
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'lead'    => 'Lead',
            'ativo'   => 'Ativo',
            'inativo' => 'Inativo',
            default   => $this->status,
        };
    }

    public function getSourceLabelAttribute(): string {
        return match($this->source) {
            'manual'    => 'Manual',
            'site'      => 'Site',
            'instagram' => 'Instagram',
            'whatsapp'  => 'WhatsApp',
            'email'     => 'E-mail',
            'indicacao' => 'Indicação',
            'outro'     => 'Outro',
            default     => $this->source,
        };
    }
}
