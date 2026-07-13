<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $furniture = Category::firstOrCreate(['name' => 'Furniture'], ['brand_required' => false]);
        $electronics = Category::firstOrCreate(['name' => 'Electronics'], ['brand_required' => true]);
        Category::firstOrCreate(['name' => 'Home Appliance'], ['brand_required' => true]);
        Category::firstOrCreate(['name' => 'Accessories'], ['brand_required' => true]);

        collect([
            'Bed', 'Wardrobe', 'Almirah', 'Dining Table', 'Chair',
            'Sofa', 'Dressing Table', 'Showcase', 'Office Table',
        ])->each(fn (string $name) => SubCategory::firstOrCreate([
            'category_id' => $furniture->id,
            'name' => $name,
        ]));

        collect([
            'TV', 'Refrigerator', 'Air Conditioner', 'Fan', 'Rice Cooker', 'Washing Machine', 'Oven',
        ])->each(fn (string $name) => SubCategory::firstOrCreate([
            'category_id' => $electronics->id,
            'name' => $name,
        ]));

        collect(['Samsung', 'LG', 'Walton', 'Singer', 'Sony', 'Vision'])
            ->each(fn (string $name) => Brand::firstOrCreate(['name' => $name]));
    }
}
