<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;
    protected $table = 'returns';
    protected $fillable = ['order_id','type','reason','status','resolution_notes'];
    public function order(){ return $this->belongsTo(Order::class); }
}
