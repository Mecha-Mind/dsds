<?php

namespace App\Http\Controllers;

use App\Models\Category;

class BaseController extends Controller
{
    /**
     * الحصول على البيانات المشتركة لجميع الـ views
     * (navbar, footer, etc)
     */
    protected function getSharedData(): array
    {
        $staticLinks = [
            [
                'name'   => 'الرئيسية',
                'route'  => 'home',
                'has_db' => false,
                'db_key' => null,
            ],
            [
                'name'   => 'المنتجات',
                'route'  => 'products',
                'has_db' => true,
                'db_key' => 'categories',
            ],
            [
                'name'   => 'التصنيفات',
                'route'  => 'categories',
                'has_db' => true,
                'db_key' => 'categories',
            ],
            [
                'name'   => 'الصيانة',
                'route'  => 'maintenance',
                'has_db' => false,
                'db_key' => null,
            ],
            [
                'name'   => 'عروض الصيانة',
                'route'  => 'maintenance.offers',
                'has_db' => false,
                'db_key' => null,
            ],
            [
                'name'   => 'أحدث العروض',
                'route'  => 'offers',
                'has_db' => false,
                'db_key' => null,
            ],
            [
                'name'   => 'عنا',
                'route'  => 'about',
                'has_db' => false,
                'db_key' => null,
            ],
            [
                'name'   => 'تواصل معنا',
                'route'  => 'contact',
                'has_db' => false,
                'db_key' => null,
            ],
        ];

        try {
            $categoriesData = Category::where('category_displaystatus', 1)
                ->select('id', 'category_name', 'category_slug', 'category_displaystatus')
                ->with(['subCategories' => fn($q) =>
                    $q->where('subcategory_displaystatus', 1)
                      ->select('id', 'category_id', 'subcategory_name', 'subcategory_slug', 'subcategory_displaystatus')
                ])
                ->limit(100)
                ->get()
                ->unique('category_name')
                ->values()
                ->map(fn($cat) => [
                    'name'     => $cat->category_name,
                    'slug'     => $cat->category_slug,
                    'children' => $cat->subCategories
                        ->unique('subcategory_name')
                        ->values()
                        ->map(fn($sub) => [
                            'name' => $sub->subcategory_name,
                            'slug' => $sub->subcategory_slug,
                        ])->toArray(),
                ])->toArray();
        } catch (\Exception $e) {
            \Log::error('Error loading categories: ' . $e->getMessage());
            $categoriesData = [];
        }

        $navData = [
            'categories' => $categoriesData,
        ];

        return [
            'staticLinks' => $staticLinks,
            'navData'     => $navData,
            'branchName'  => 'الفرع الرئيسي',
            'phone'       => '01212345678',
            'logo'        => 'images/primaryLogo.png',
            'footer'      => [
                'address'      => 'الإسماعيلية-الشيخ زايد-الشارع التجاري-بجوار كافية جراند',
                'phone'        => '01212345678',
                'email'        => 'info@store.com',
                'logo'         => 'images/primaryLogo.png',
                'social'       => [
                    ['icon' => 'bi-facebook',  'url' => '#', 'label' => 'فيسبوك'],
                    ['icon' => 'bi-instagram', 'url' => '#', 'label' => 'إنستجرام'],
                    ['icon' => 'bi-twitter-x', 'url' => '#', 'label' => 'تويتر'],
                    ['icon' => 'bi-tiktok',    'url' => '#', 'label' => 'تيك توك'],
                    ['icon' => 'bi-whatsapp',  'url' => '#', 'label' => 'واتساب'],
                ],
                'info_links'   => [
                    ['name' => 'اعرف عنا',        'route' => 'about'],
                    ['name' => 'تواصل معنا',       'route' => 'contact'],
                    ['name' => 'حسابي',            'route' => 'account'],
                    ['name' => 'سياسة الخصوصية',  'route' => 'privacy'],
                    ['name' => 'الشروط والأحكام', 'route' => 'terms'],
                ],
                'payment_icons' => [
                    'images/payment/visa.png',
                    'images/payment/mastercard.png',
                    'images/payment/paypal.png',
                ],
            ],
        ];
    }
}
