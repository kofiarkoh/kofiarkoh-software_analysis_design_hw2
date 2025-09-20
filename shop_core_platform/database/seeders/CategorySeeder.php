<?php

namespace Database\Seeders;

use App\Models\Vendor\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics',            'icon' => 'ph-device-mobile'],
            ['name' => 'Fashion',                'icon' => 'ph-tshirt'],
            ['name' => 'Home & Kitchen',         'icon' => 'ph-cooking-pot'],
            ['name' => 'Beauty & Personal Care', 'icon' => 'ph-hand-soap'],
            ['name' => 'Health & Wellness',      'icon' => 'ph-heartbeat'],
            ['name' => 'Sports & Outdoors',      'icon' => 'ph-soccer-ball'],
            ['name' => 'Toys & Games',           'icon' => 'ph-puzzle-piece'],
            ['name' => 'Automotive',             'icon' => 'ph-car'],
            ['name' => 'Books & Stationery',     'icon' => 'ph-book'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
            ]);
        }
    }
}
