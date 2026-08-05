<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id','color_name','img1','img2','sort_order'];
    public function product() { return $this->belongsTo(Product::class); }
}
