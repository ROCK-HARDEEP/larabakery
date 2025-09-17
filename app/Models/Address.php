<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','label','line1','line2','pincode','city','state_iso','country','is_default'];
    protected $casts = ['is_default'=>'bool'];
    public function user(){ return $this->belongsTo(User::class); }
}