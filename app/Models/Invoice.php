<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','number','pdf_path','totals_json'];
    protected $casts = ['totals_json'=>'array'];
    public function order(){ return $this->belongsTo(Order::class); }
}