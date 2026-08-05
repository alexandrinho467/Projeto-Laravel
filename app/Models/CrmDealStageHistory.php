<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmDealStageHistory extends Model
{
    public $timestamps = false;

    protected $table = 'crm_deal_stage_history';
    protected $fillable = ['crm_deal_id', 'stage', 'entered_at'];
    protected $casts    = ['entered_at' => 'datetime'];

    public function deal() { return $this->belongsTo(CrmDeal::class, 'crm_deal_id'); }
}
