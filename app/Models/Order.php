<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['user_id','total_price','status','payment_status','shipping_address','city','state','zip_code','country','phone_number',];

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
