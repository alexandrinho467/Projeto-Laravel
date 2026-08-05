<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code','type','value','min_order','max_uses','uses','active','expires_at'];
    protected $casts    = ['value' => 'decimal:2', 'min_order' => 'decimal:2', 'active' => 'boolean', 'expires_at' => 'datetime'];

    public function isValid(float $orderTotal): bool {
        if (!$this->active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->uses >= $this->max_uses) return false;
        if ($orderTotal < $this->min_order) return false;
        return true;
    }

    public function calculateDiscount(float $orderTotal): float {
        if ($this->type === 'percent') return round($orderTotal * ($this->value / 100), 2);
        return min($this->value, $orderTotal);
    }
}
