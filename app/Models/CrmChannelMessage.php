<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmChannelMessage extends Model
{
    protected $table = 'crm_channel_messages';
    protected $fillable = [
        'external_message_id', 'crm_contact_id', 'crm_deal_id', 'user_id',
        'channel', 'direction', 'subject', 'content', 'occurred_at', 'raw_payload',
    ];
    protected $casts = ['occurred_at' => 'datetime', 'raw_payload' => 'array'];

    public function contact() { return $this->belongsTo(CrmContact::class, 'crm_contact_id'); }
    public function deal()    { return $this->belongsTo(CrmDeal::class, 'crm_deal_id'); }
    public function author()  { return $this->belongsTo(User::class, 'user_id'); }

    public function getChannelLabelAttribute(): string {
        return match($this->channel) {
            'whatsapp'  => 'WhatsApp',
            'instagram' => 'Instagram',
            'email'     => 'E-mail',
            default     => $this->channel,
        };
    }
}
