<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['category_name' => 'قطع غيار', 'category_slug' => 'spare-parts', 'category_displaystatus' => 1],
            ['category_name' => 'موبايلات', 'category_slug' => 'mobiles', 'category_displaystatus' => 1],
            ['category_name' => 'إكسسوارات', 'category_slug' => 'accessories', 'category_displaystatus' => 1],
            ['category_name' => 'ساعات', 'category_slug' => 'watches', 'category_displaystatus' => 1],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $subcategories = [
            ['subcategory_name' => 'شاشات', 'subcategory_slug' => 'screens', 'category_id' => 1, 'subcategory_displaystatus' => 1],
            ['subcategory_name' => 'بطاريات', 'subcategory_slug' => 'batteries', 'category_id' => 1, 'subcategory_displaystatus' => 1],
            ['subcategory_name' => 'ايفون', 'subcategory_slug' => 'iphone', 'category_id' => 2, 'subcategory_displaystatus' => 1],
            ['subcategory_name' => 'سامسونج', 'subcategory_slug' => 'samsung', 'category_id' => 2, 'subcategory_displaystatus' => 1],
            ['subcategory_name' => 'جرابات', 'subcategory_slug' => 'cases', 'category_id' => 3, 'subcategory_displaystatus' => 1],
        ];

        foreach ($subcategories as $sub) {
            SubCategory::create($sub);
        }
    }
}