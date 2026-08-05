<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['brand','name','slug','badge','gender','featured','old_price','price','ref','description','tech','active','sort_order'];
    protected $casts    = ['old_price' => 'decimal:2', 'price' => 'decimal:2', 'active' => 'boolean'];

    public function images()   { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function sizes()    { return $this->hasMany(ProductSize::class); }
    public function features() { return $this->hasMany(ProductFeature::class)->orderBy('sort_order'); }

    public function getDiscountPercentAttribute(): int {
        if (!$this->old_price || $this->old_price <= 0) return 0;
        return (int) round((1 - $this->price / $this->old_price) * 100);
    }

    public function getPriceFormattedAttribute(): string {
        return 'AED ' . number_format($this->price, 2, '.', ',');
    }

    public function getOldPriceFormattedAttribute(): string {
        return 'AED ' . number_format($this->old_price, 2, '.', ',');
    }
}
