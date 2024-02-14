<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->name = 'gios dua cay cai ve troi ' . $i;
            $category->featured_img = null;
            $category->description = 'category description' . $i;
            $category->status = 1;
            $category->save();
        }
    }
}
