<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Payment extends Model
{
    use HasFactory, Auditable;
    protected $fillable = ['order_id','provider','txn_id','amount','status','payload_json'];
    protected $casts = ['amount'=>'decimal:2','payload_json'=>'array'];
    public function order(){ return $this->belongsTo(Order::class); }
}