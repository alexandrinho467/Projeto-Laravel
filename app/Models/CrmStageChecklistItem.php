<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmStageChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'crm_stage_checklist_items';
    protected $fillable = ['stage', 'key', 'label', 'position'];
}
