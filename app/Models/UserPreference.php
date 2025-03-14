<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    //
    protected $table = 'user_preferences';
    protected $fillable = ['colors','brands','user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
