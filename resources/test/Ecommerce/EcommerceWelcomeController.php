<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCompany;
use App\Models\Offersfromtheowner;
use App\Models\Ourcustomer;
use App\Models\Ourservice;
use App\Models\Product;
use App\Models\ScrollingOffer;
use App\Models\Subcategory;
use App\Services\EcommerceSharedDataService;

class EcommerceWelcomeController extends Controller
{
    public function index()
    {

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'الرئيسية';
        // ══════════════════════════════════════════
        // Navbar Data
        // ══════════════════════════════════════════
        // $navCategories = Category::where('category_displaystatus', 1)
        //     ->with(['subCategories' => fn($q) =>
        //         $q->where('subcategory_displaystatus', 1)
        //     ])
        //     ->get()
        //     ->map(fn($cat) => [
        //         'name'     => $cat->category_name,
        //         'slug'     => $cat->category_id,
        //         'image'    => $cat->category_image,
        //         'children' => $cat->subCategories
        //             ->map(fn($sub) => [
        //                 'name' => $sub->subcategory_name,
        //                 'slug' => $sub->subcategory_id,
        //             ])->toArray(),
        //     ])->toArray();

        // $staticLinks = [
        //     ['name'=>'الرئيسية',    'route'=>'home',                  'has_db'=>false, 'db_key'=>null],
        //     ['name'=>'المنتجات',    'route'=>'EcommerceAllProducts',  'has_db'=>true,  'db_key'=>'categories'],
        //     ['name'=>'التصنيفات',   'route'=>'EcommerceAllCategories','has_db'=>true,  'db_key'=>'categories'],
        //     ['name'=>'الصيانة',     'route'=>'UserMaintenance',       'has_db'=>false, 'db_key'=>null],
        //     ['name'=>'عروض الصيانة','route'=>'EcommerceOffers',       'has_db'=>false, 'db_key'=>null],
        //     ['name'=>'أحدث العروض', 'route'=>'EcommerceOffers',       'has_db'=>false, 'db_key'=>null],
        //     ['name'=>'عنا',         'route'=>'EcommerceKnowAboutUs',  'has_db'=>false, 'db_key'=>null],
        //     ['name'=>'تواصل معنا',  'route'=>'EcommerceContactUs',    'has_db'=>false, 'db_key'=>null],
        // ];

        // // ══════════════════════════════════════════
        // // Branch & Social
        // // ══════════════════════════════════════════
        // $Branch             = Branche::where('branch_id', '1')->first();
        // $SocialMediaContact = SocialMediaContact::where('branch_id', '1')->first();

        // $mapUrl = '#';
        // if ($SocialMediaContact && preg_match(
        //     '/!2d([0-9\.\-]+)!3d([0-9\.\-]+)/',
        //     $SocialMediaContact->location ?? '', $m
        // )) {
        //     $mapUrl = "https://www.google.com/maps?q={$m[2]},{$m[1]}";
        // }

        // ══════════════════════════════════════════
        // Hero — من ScrollingOffers
        // ══════════════════════════════════════════
        $ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
            ->orderBy('updated_at', 'desc')
            ->get();

        // dd($ScrollingOffers);

        $firstOffer = $ScrollingOffers->first();
        $hero = [
            'title'   => $firstOffer?->scrollingoffer_headline    ?? 'مرحباً بك',
            'subtitle' => $firstOffer?->scrollingoffer_description ?? '',
            'image'   => $firstOffer?->scrollingoffer_image       ?? 'images/hero.png',
            'btnText' => 'تسوق الآن',
            'btnLink' => $firstOffer?->scrollingoffer_url         ?? route('EcommerceAllProducts'),
        ];

        // ══════════════════════════════════════════
        // Partner Companies (Brands)
        // ══════════════════════════════════════════
        $FilterBrands = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->where('maintenancecompany_image', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();

        // ══════════════════════════════════════════
        // Featured Products — مع العلاقة بجدول products
        // ══════════════════════════════════════════
        /*
         | EcommerceProduct بيحتوي على product_id
         | Product بيحتوي على product_name, product_image, product_sellprice
         | العلاقة: ecommerceproducts.product_id -> products.product_id
        */
        $ThemostsellingEcommerceproducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinbestsellers', '1')
            ->where('ecommerceproduct_appearonhomepage', '1')
            ->with('product') // ← لازم تعمل العلاقة دي في الـ Model
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        // ══════════════════════════════════════════
        // Categories للهوم
        // ══════════════════════════════════════════
        $Categories = Category::where('category_displaystatus', '1')
            ->where('category_appearonhomepage', '1')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        // ══════════════════════════════════════════
        // Discounted Products
        // ══════════════════════════════════════════
        $Productswithoffersanddiscounts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->where('ecommerceproduct_appearinthelistofoffers', '1')
            ->where('ecommerceproduct_appearonhomepage', '1')
            ->with('product')
            ->orderBy('updated_at', 'desc')
            ->paginate(4);

        // ══════════════════════════════════════════
        // Maintenance Offers
        // ══════════════════════════════════════════
        $Offersfromtheowners = Offersfromtheowner::where('offerfromtheowner_active', '1')
            ->whereNotNull('offerfromtheowner_image')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        // ══════════════════════════════════════════
        // Footer Categories
        // ══════════════════════════════════════════
        $FooterCategories = Category::where('category_displaystatus', '1')
            ->where('category_appearonhomepage', '1')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        $Ourservices = Ourservice::where('ourservice_displaystatus', '1')
            ->where('ourservice_appearonhomepage', '1')
            ->whereNotNull('ourservice_image')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $Ourcustomers = Ourcustomer::where('ourcustomer_displaystatus', '1')
            ->where('ourcustomer_appearonhomepage', '1')
            ->whereNotNull('ourcustomer_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $Products = Product::all();

        return view('welcome', compact(
            'ecommerceSharedData',
            'hero',
            'ScrollingOffers',
            // Home Sections
            'FilterBrands',
            'ThemostsellingEcommerceproducts',
            'Products',
            'Categories',
            'Productswithoffersanddiscounts',
            'Offersfromtheowners',
            'Ourservices',
            'Ourcustomers',
            // Footer
            'FooterCategories'
        ));
    }
}
