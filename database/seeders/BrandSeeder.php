<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $brands = ['adidas','dior','gucci','prada','versace','zara', 'shein', 'lacoste'];
        foreach($brands as $brand){
            Brand::create([
                'name' => $brand,
                'image' => "storage/brands/$brand.png"
            ]);
        }
    }
}
