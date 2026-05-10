<?php
// app/Http/Controllers/ShopController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\MaintenanceCompany;

class ShopController extends BaseController
{
    private function sharedData(): array
    {
        return $this->getSharedData();
    }

    public function home()
    {

        $data = $this->sharedData();

        $data['hero'] = [
            'title'   => 'أقوى عروض التخفيض على الموبايلات',
            'subtitle'=> 'أسعار مميزة وتخفيضات كبيرة خلال شهر اكتوبر.',
            'image'   => 'images/hero.png',
            'btnText' => 'تسوق الآن',
            'btnLink' => route('allproducts'),
        ];

        $data['brands'] = [
            ['name' => 'Anker',   'img' => 'images/anker.png'],
            ['name' => 'Samsung', 'img' => 'images/samsung.png'],
            ['name' => 'Realme',  'img' => 'images/realme.png'],
            ['name' => 'Infinix', 'img' => 'images/infinix.png'],
            ['name' => 'Xiaomi',  'img' => 'images/mi.png'],
            ['name' => 'Apple',   'img' => 'images/apple.png'],
        ];

        $data['featuredProducts'] = [
            ['id'=>1,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>35000,'old_price'=>50000,'discount'=>null,'image'=>'images/phone0.png','slug'=>'product-1'],
            ['id'=>2,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>35000,'old_price'=>50000,'discount'=>null,'image'=>'images/phone1.png','slug'=>'product-2'],
            ['id'=>3,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>35000,'old_price'=>null, 'discount'=>null,'image'=>'images/phone2.png','slug'=>'product-3'],
            ['id'=>4,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>35000,'old_price'=>null, 'discount'=>null,'image'=>'images/phone3.png','slug'=>'product-4'],
        ];

        $data['homeCategories'] = array_slice($data['navData']['categories'], 0, 6);

        $data['discountedProducts'] = [
            ['id'=>5,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>22000,'old_price'=>35000,'discount'=>20,'image'=>'images/phone0.png','slug'=>'product-5'],
            ['id'=>6,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>22000,'old_price'=>35000,'discount'=>20,'image'=>'images/phone1.png','slug'=>'product-6'],
            ['id'=>7,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>22000,'old_price'=>35000,'discount'=>20,'image'=>'images/phone2.png','slug'=>'product-7'],
            ['id'=>8,'name'=>'ايفون 13 برو ماكس 256 جيجا','price'=>22000,'old_price'=>35000,'discount'=>20,'image'=>'images/phone3.png','slug'=>'product-8'],
        ];

        $data['maintenanceOffers'] = [
            ['image' => 'images/offers/offer1.jpg', 'title' => 'عرض صيانة 1', 'link' => '#'],
            ['image' => 'images/offers/offer2.jpg', 'title' => 'عرض صيانة 2', 'link' => '#'],
            ['image' => 'images/offers/offer3.jpg', 'title' => 'عرض صيانة كبير', 'link' => '#'],
        ];

        $data['features'] = [
            ['icon'=>'bi-credit-card',    'title'=>'وسائل الدفع',     'text'=>'فيزا، ماستركارد، كاش، أونا باي'],
            ['icon'=>'bi-headset',        'title'=>'خدمة العملاء',    'text'=>'تواصل معنا 24 ساعة 7 أيام'],
            ['icon'=>'bi-arrow-clockwise','title'=>'سياسة الاسترجاع', 'text'=>'وفقاً لقانون حماية المستهلك'],
            ['icon'=>'bi-shield-check',   'title'=>'نظام دفع آمن',    'text'=>'نضمن الدفع الآمن مع PVE'],
        ];

        return view('home', $data);
    }

    public function products()
    {
        $data = $this->sharedData();
        $data['products'] = [];
        return view('pages.allproducts', $data);
    }
    public function allproducts()
    {
        $data = $this->sharedData();
        $data['products'] = [];
        return view('pages.allproducts', $data);
    }

    public function categories()
    {
        $data = $this->sharedData();
        // عنوان الصفحة عربي
        $data['pageTitle'] = 'التصنيفات';
        $data['categoryName'] = 'التصنيفات'; // ← أضف السطر ده

        return view('pages.categories', $data);
    }

    public function category(Category $category)
    {
        $data = $this->sharedData();
 
        $data['category']     = $category;
        $data['categoryName'] = $category->category_name; // عربي للعرض
        $data['pageTitle']    = $category->category_name; // عربي لعنوان التاب
        return view('pages.category', $data);
    }

    public function subcategory(SubCategory $subcategory)
    {
        $data = $this->sharedData();

        $data['subcategory']     = $subcategory;
        $data['subcategoryName'] = $subcategory->subcategory_name;
        $data['categoryName']    = $subcategory->category->category_name ?? '';
        $data['pageTitle']       = $subcategory->category->category_name . ' / ' . $subcategory->subcategory_name;
        return view('pages.subcategory', $data);
    }

    public function product($slug)
    {
        $data = $this->sharedData();
        $data['slug'] = $slug;
        return view('pages.product', $data);
    }

    public function offers()
    {
        return view('pages.offers', $this->sharedData());
    }

    public function ShoppingCart()        { return view('pages.cart',        $this->sharedData()); }
    public function checkout()    { return view('pages.checkout',    $this->sharedData()); }
    public function account()     { return view('pages.account',     $this->sharedData()); }
    public function orders()      { return view('pages.orders',      $this->sharedData()); }
    public function maintenance() { return view('pages.maintenance', $this->sharedData()); }
    public function maintenanceOffers() { return view('pages.maintenance-offers', $this->sharedData()); }
    public function about()       { return view('pages.about',       $this->sharedData()); }
    public function contact()     { return view('pages.contact',     $this->sharedData()); }
    public function popular()     { return view('pages.popular',     $this->sharedData()); }
    public function recent()      { return view('pages.recent',      $this->sharedData()); }
    public function faq()         { return view('pages.faq',         $this->sharedData()); }
    public function games()       { return view('pages.games',       $this->sharedData()); }

    public function orderDetail($id)
    {
        $data = $this->sharedData();
        $data['orderId'] = $id;
        return view('pages.order-detail', $data);
    }
  
}