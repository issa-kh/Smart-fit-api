<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    //
    protected $table = 'user_measurements';
    protected $fillable = ['height','weight','chest','waist','hips','gender','user_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
