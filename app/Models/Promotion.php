<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    //
    protected $fillable = ['title', 'discount_percentage', 'start_date', 'end_date', 'vendor_id', 'product_id'];
    
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
