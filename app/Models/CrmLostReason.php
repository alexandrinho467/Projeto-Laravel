<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmLostReason extends Model
{
    use HasFactory;

    protected $table = 'crm_lost_reasons';
    protected $fillable = ['name', 'active'];
    protected $casts    = ['active' => 'boolean'];
}
