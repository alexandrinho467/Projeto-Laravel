<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = ['product_id','size','available','stock','stock_alert'];
    protected $casts    = ['available' => 'boolean'];
    public function product() { return $this->belongsTo(Product::class); }
}
