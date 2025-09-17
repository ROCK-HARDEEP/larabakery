<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySlot extends Model
{
    use HasFactory;
    protected $fillable = ['date','start_time','end_time','capacity','booked_count'];
    protected $casts = ['date'=>'date','capacity'=>'int','booked_count'=>'int'];
}