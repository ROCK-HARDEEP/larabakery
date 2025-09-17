<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\HomePageCategory;

class HomePageCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first few active categories
        $categories = Category::where('is_active', true)
            ->orderBy('position')
            ->take(6)
            ->get();

        foreach ($categories as $index => $category) {
            HomePageCategory::create([
                'category_id' => $category->id,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Home page categories seeded successfully!');
    }
}
