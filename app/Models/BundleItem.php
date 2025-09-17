<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleItem extends Model
{
    use HasFactory;
    protected $fillable = ['bundle_id','product_id','qty'];
    public function bundle(){ return $this->belongsTo(Bundle::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}