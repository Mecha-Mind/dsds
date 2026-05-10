<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSlugs extends Command
{
    protected $signature = 'slugs:generate';
    protected $description = 'Generate slugs for categories and subcategories';

    public function handle()
    {
        $this->info('Generating slugs for categories and subcategories...');

        // تحديث Categories
        $categoriesUpdated = 0;
        Category::whereNull('category_slug')
                ->orWhere('category_slug', '')
                ->each(function ($category) use (&$categoriesUpdated) {
                    $category->update([
                        'category_slug' => Str::slug($category->category_name)
                    ]);
                    $categoriesUpdated++;
                });

        // تحديث SubCategories
        $subcategoriesUpdated = 0;
        SubCategory::whereNull('subcategory_slug')
                   ->orWhere('subcategory_slug', '')
                   ->each(function ($subcategory) use (&$subcategoriesUpdated) {
                       $subcategory->update([
                           'subcategory_slug' => Str::slug($subcategory->subcategory_name)
                       ]);
                       $subcategoriesUpdated++;
                   });

        $this->info("✓ Updated {$categoriesUpdated} categories with slugs");
        $this->info("✓ Updated {$subcategoriesUpdated} subcategories with slugs");
        $this->info('✓ Done! All slugs have been generated.');

        return Command::SUCCESS;
    }
}
