<?php
// app/Services/SharedDataService.php

namespace App\Services;

use App\Models\Branche;
use App\Models\Category;
use App\Models\SocialMediaContact;
use App\Models\ApplicationColor;

class SharedDataService
{
    public static function get(): array
    {
        $Branch             = Branche::where('branch_id', 1)->first();
        $SocialMediaContact = SocialMediaContact::where('branch_id', 1)->first();

        // ── استخراج Google Maps URL ──
        $mapUrl = '#';
        if ($SocialMediaContact && preg_match(
            '/!2d([0-9\.\-]+)!3d([0-9\.\-]+)/',
            $SocialMediaContact->location ?? '', $m
        )) {
            $mapUrl = "https://www.google.com/maps?q={$m[2]},{$m[1]}";
        }

        // ── الألوان من الـ DB ──
        /*
         | جدول applicationcolors فيه:
         | ecommerceprimary_color, ecommercesecondary_color, ecommercetext_color
         | دول هم الألوان الخاصة بالـ ecommerce
        */
        $AppColors = ApplicationColor::first();

        // ── Categories للـ Navbar ──
        $navCategories = Category::where('category_displaystatus', 1)
            ->with(['subCategories' => fn($q) =>
                $q->where('subcategory_displaystatus', 1)
            ])
            ->get()
            ->map(fn($cat) => [
                'name'     => $cat->category_name,
                'slug'     => $cat->category_id,
                'image'    => $cat->category_image,
                'children' => $cat->subCategories
                    ->map(fn($sub) => [
                        'name' => $sub->subcategory_name,
                        'slug' => $sub->subcategory_id,
                    ])->toArray(),
            ])->toArray();

        $staticLinks = [
            ['name'=>'الرئيسية',    'route'=>'home',                  'has_db'=>false, 'db_key'=>null],
            ['name'=>'المنتجات',    'route'=>'EcommerceAllProducts',  'has_db'=>true,  'db_key'=>'categories'],
            ['name'=>'التصنيفات',   'route'=>'EcommerceAllCategories','has_db'=>true,  'db_key'=>'categories'],
            ['name'=>'الصيانة',     'route'=>'UserMaintenance',       'has_db'=>false, 'db_key'=>null],
            ['name'=>'عروض الصيانة','route'=>'EcommerceOffers',       'has_db'=>false, 'db_key'=>null],
            ['name'=>'أحدث العروض', 'route'=>'EcommerceOffers',       'has_db'=>false, 'db_key'=>null],
            ['name'=>'عنا',         'route'=>'EcommerceKnowAboutUs',  'has_db'=>false, 'db_key'=>null],
            ['name'=>'تواصل معنا',  'route'=>'EcommerceContactUs',    'has_db'=>false, 'db_key'=>null],
        ];

        $FooterCategories = Category::where('category_displaystatus', 1)
            ->where('category_appearonhomepage', 1)
            ->take(5)->get();

        return [
            'Branch'             => $Branch,
            'SocialMediaContact' => $SocialMediaContact,
            'mapUrl'             => $mapUrl,
            'AppColors'          => $AppColors,
            'staticLinks'        => $staticLinks,
            'navCategories'      => $navCategories,
            'FooterCategories'   => $FooterCategories,
            // shortcuts للـ navbar component
            'branchName'         => $Branch?->branch_name  ?? '',
            'branchImage'        => $Branch?->branch_image ?? '',
            'phone'              => $Branch?->branch_phone ?? '',
            'logo'               => 'images/brancheslogo/' . ($Branch?->branch_image ?? ''),
        ];
    }
}