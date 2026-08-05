<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id','user_id','name','rating','body','img1','img2','img3','video','approved'];

    public function product() { return $this->belongsTo(Product::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function images(): array
    {
        return array_filter([$this->img1, $this->img2, $this->img3]);
    }
}
