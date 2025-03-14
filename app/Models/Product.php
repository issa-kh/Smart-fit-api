<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = ['name','description','price','color','size','stock','image_url','first_category','category_id','brand_id','is_deleted','vendor_id'];
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function vendor(){
        return $this->belongsTo(User::class);
    }
    public function promotions(){
        return $this->hasMany(Promotion::class);
    }
}
