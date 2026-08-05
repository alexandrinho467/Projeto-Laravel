<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmTag extends Model
{
    use HasFactory;

    protected $table = 'crm_tags';
    protected $fillable = ['name', 'color'];

    public function contacts()
    {
        return $this->belongsToMany(CrmContact::class, 'crm_contact_tag');
    }
}
