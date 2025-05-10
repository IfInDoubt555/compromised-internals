<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Compromised Internals T-Shirt',
            'description' => 'Soft cotton shirt with rally-style branding.',
            'price' => 24.99,
            'type' => 'apparel',
            'image_path' => 'products/skull-design-white.png',
            'slug' => Str::slug('Compromised Internals T-Shirt'),
            'has_variants' => true,  /** false removes size and color, true shows size and color */
        ]);
    
        Product::create([
            'name' => 'Rally Course Map Pack',
            'description' => 'Downloadable PDF of legendary rally routes.',
            'price' => 4.99,
            'type' => 'digital',
            'image_path' => 'products/map-pack.png',
            'slug' => Str::slug('Rally Course Map Pack'),
            'has_variants' => false,
        ]);
    }
}
