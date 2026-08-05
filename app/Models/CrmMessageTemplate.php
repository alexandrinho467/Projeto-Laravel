<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmMessageTemplate extends Model
{
    use HasFactory;

    protected $table = 'crm_message_templates';
    protected $fillable = ['name', 'body'];

    public function render(CrmContact $contact, ?CrmDeal $deal = null): string
    {
        $deal ??= $contact->deals()->latest()->first();

        return strtr($this->body, [
            '{{cliente}}' => $contact->name,
            '{{sneaker}}' => $deal?->title ?? '',
            '{{valor}}'   => $deal?->value_formatted ?? '',
        ]);
    }
}
