<?php

namespace App\Http\Controllers\Ecommerce\Products;

use App\Http\Controllers\Controller;
use App\Models\Branche;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceCompany;
use App\Models\MaintenanceModel;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Subcategory;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EcommerceAllProductsController extends Controller
{
    public function EcommerceAllProducts(Request $request)
    {
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';

        // ── الفروع ──
        $branches = Branche::where('branch_delete', '0')
            ->where('branch_services', '0')
            ->get();

        $FilterCategories = Category::where('category_displaystatus', 1)
            ->get()
            ->map(function ($cat) {
                $cat->ecommerce_products_count = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
                    ->whereHas(
                        'product',
                        fn($q) =>
                        $q->where('product_category', $cat->category_id)
                            ->where('product_delete', 0)
                    )->count();
                return $cat;
            })
            ->filter(fn($cat) => $cat->ecommerce_products_count > 0)
            ->values();

        // ── الشركات/الماركات ──
        $FilterBrands = MaintenanceCompany::where('maintenancecompany_active', 1)
            ->where('maintenancecompany_delete', '0')
            ->get();

        // ── السعر min/max ──
        $priceStats = DB::table('products')
            ->where('product_delete', 0)
            ->selectRaw('MIN(product_sellprice) as min_price, MAX(product_sellprice) as max_price')
            ->first();


        $minPrice = (int)($priceStats->min_price ?? 0);
        $maxPrice = (int)($priceStats->max_price ?? 100000);


        // ── الألوان من الـ DB ──
        // ← لما تتضاف قيم في product_color هتظهر تلقائياً
        $dbColors = Product::where('product_delete', 0)
            ->whereNotNull('product_color')
            ->where('product_color', '!=', '')
            ->distinct()
            ->pluck('product_color')
            ->filter()
            ->values()
            ->toArray();

        /*
         | Placeholder للألوان لو مفيش بيانات في الـ DB
         | ← لما تتضاف قيم في product_color في الـ DB
         |   هتُشال الـ placeholder تلقائياً (لأن $dbColors مش هيكون فاضي)
        */
        $colorPlaceholders = [
            ['val' => 'أبيض',   'hex' => '#f5f5f5'],
            ['val' => 'أسود',   'hex' => '#111111'],
            ['val' => 'ذهبي',   'hex' => '#d4a017'],
            ['val' => 'أزرق',   'hex' => '#1e40af'],
            ['val' => 'أخضر',   'hex' => '#166534'],
            ['val' => 'أحمر',   'hex' => '#991b1b'],
            ['val' => 'برتقالي', 'hex' => '#ea580c'],
            ['val' => 'وردي',   'hex' => '#db2777'],
        ];

        $usingColorPlaceholder = empty($dbColors);
        $availableColors = $usingColorPlaceholder ? $colorPlaceholders : $dbColors;

        /*
         | ─────────────────────────────────────────────
         | PLACEHOLDERS للفلاتر غير الموجودة في الـ DB حالياً
         |
         | لما يتضافوا Columns في جدول products:
         |   product_ram       ← سعة الرامات
         |   product_storage   ← المساحة الداخلية
         |   product_cpu       ← البروسيسور
         |   product_charger   ← الشاحن
         |
         | ← ابحث عن تعليق "← غيّر هنا" في الـ Controller
         |   واستبدل الـ placeholder بـ query حقيقية
         | ─────────────────────────────────────────────
        */

        // ← غيّر هنا لما يتضاف product_ram في الـ DB
        // $availableRam = Product::where('product_delete', 0)
        //     ->whereNotNull('product_ram')
        //     ->where('product_ram', '!=', '')
        //     ->distinct()->pluck('product_ram')->filter()->sort()->values()->toArray();
        $availableRam = []; // placeholder — هيتعبى من الـ DB
        $ramPlaceholders = ['4 جيجا', '8 جيجا', '12 جيجا', '16 جيجا', '32 جيجا'];

        // ← غيّر هنا لما يتضاف product_storage في الـ DB
        // $availableStorage = Product::where('product_delete', 0)
        //     ->whereNotNull('product_storage')
        //     ->where('product_storage', '!=', '')
        //     ->distinct()->pluck('product_storage')->filter()->sort()->values()->toArray();
        $availableStorage = []; // placeholder
        $storagePlaceholders = ['64 جيجا', '128 جيجا', '256 جيجا', '512 جيجا'];

        // ← غيّر هنا لما يتضاف product_cpu في الـ DB
        $availableCpu = [];
        $cpuPlaceholders = ['رباعي النواة', 'سداسي النواة', 'ثماني النواة'];

        // ← غيّر هنا لما يتضاف product_charger في الـ DB
        $availableCharger = [];
        $chargerPlaceholders = ['18 وات', '33 وات', '65 وات'];

        // ── المودييلز والتصنيفات الفرعية ──
        // ← لو maintenancemodel_delete مش موجود — استبدل بـ ::all()
        try {
            $FilterModels = MaintenanceModel::where('maintenancemodel_delete', 0)->get();
        } catch (\Exception $e) {
            $FilterModels = MaintenanceModel::all();
        }

        try {
            $FilterMaintenanceCategories = MaintenanceCategory::where('maintenancecategory_delete', 0)->get();
        } catch (\Exception $e) {
            $FilterMaintenanceCategories = MaintenanceCategory::all();
        }

        $FilterSubcategories = Subcategory::where('subcategory_displaystatus', 1)->get();

        // ══════════════════════════════════
        // Query المنتجات
        // ══════════════════════════════════
        $query = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
            ->with('product');

        // 1. Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas(
                'product',
                fn($q) => $q
                    ->where(
                        fn($sub) => $sub
                            ->where('product_name', 'LIKE', "%{$search}%")
                            ->orWhere('product_description', 'LIKE', "%{$search}%")
                    )
                    ->where('product_delete', 0)
            );
        }

        // 2. الفروع — عن طريق جدول stocks
        if ($request->filled('branchs')) {
            $productIdsInBranch = Stock::whereIn('stock_branch', $request->branchs)
                ->where('stock_delete', 0)
                ->where('stock_quantity', '>', 0)
                ->pluck('stock_product')
                ->unique()
                ->toArray();

            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_id', $productIdsInBranch)
            );
        }

        // 3. الماركة — product_maintenancecompany
        if ($request->filled('maintenancecompanies')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecompany', $request->maintenancecompanies)
            );
        }

        // 4. السعر
        if ($request->filled('max_price')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('product_sellprice', '<=', (int) $request->max_price)
            );
        }

        // 5. اللون — product_color
        if ($request->filled('color')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_color', (array) $request->color)
            );
        }

        // 6. الرامات
        // ← غيّر هنا لما يتضاف product_ram في الـ DB
        // if ($request->filled('ram')) {
        //     $query->whereHas('product', fn($q) =>
        //         $q->whereIn('product_ram', $request->ram)
        //     );
        // }

        // 7. المساحة
        // ← غيّر هنا لما يتضاف product_storage في الـ DB
        // if ($request->filled('storage')) {
        //     $query->whereHas('product', fn($q) =>
        //         $q->whereIn('product_storage', $request->storage)
        //     );
        // }

        // 8. التصنيف الفرعي
        if ($request->filled('subcategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_category2', $request->subcategory)
            );
        }

        // 9. الموديل
        if ($request->filled('model')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancemodel', $request->model)
            );
        }

        // 10. نوع الجهاز
        if ($request->filled('maintenancecategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecategory', $request->maintenancecategory)
            );
        }

        // 11. الترتيب
        if ($request->sort === 'price_asc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'asc')
                ->select('ecommerceproducts.*');
        } elseif ($request->sort === 'price_desc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'desc')
                ->select('ecommerceproducts.*');
        } else {
            $query->orderBy('ecommerceproducts.updated_at', 'desc');
        }

        $Products = $query->paginate(12)->withQueryString();
        // Category filter
        if ($request->filled('category')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_category', $request->category)
            );
        }

        // Brand/Company filter
        if ($request->filled('maintenancecompanies')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecompany', $request->maintenancecompanies)
            );
        }

        // Price filter
        if ($request->filled('max_price')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->where('product_sellprice', '<=', (int)$request->max_price)
            );
        }
        // في EcommerceAllProducts() — بعد Color filter وقبل Sort

        // Color filter — مرة واحدة بس (كان متكرر 3 مرات)
        // if ($request->filled('color')) {
        //     $query->whereHas('product', fn($q) =>
        //         $q->where('product_color', $request->color)
        //     );
        // }
        // وغيّر من filled('color') لـ array
        if ($request->filled('color')) {
            $colors = (array)$request->color; // يشتغل مع color واحد أو array
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_color', $colors)
            );
        }
        /*
        | Stock/Branches filter
        | جدول stocks فيه: stock_branch, stock_product, stock_quantity
        | لما المستخدم يختار فرع بنجيب المنتجات اللي عندها stock في الفرع ده
        */
        if ($request->filled('branchs')) {
            $branchIds = $request->branchs;
            $productIdsInBranch = \App\Models\Stock::whereIn('stock_branch', $branchIds)
                ->where('stock_delete', 0)
                ->where('stock_quantity', '>', 0)
                ->pluck('stock_product')
                ->unique()
                ->toArray();

            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_id', $productIdsInBranch)
            );
        }

        /*
        | Subcategory filter (اللي احنا بنسميه storage/ram في الـ view)
        | product_category2 بيخزن الـ subcategory_id
        | دلوقتي هنعمل فلتر بالـ subcategory
        | ← لما تبقا عاوز تربطه بـ storage/ram حقيقي محتاج columns جديدة في الـ DB
        */
        if ($request->filled('subcategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_category2', $request->subcategory)
            );
        }

        /*
        | MaintenanceModel filter
        | product_maintenancemodel بيخزن الـ model (شاومي ريدمي، ايفون 14 ...)
        */
        if ($request->filled('model')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancemodel', $request->model)
            );
        }

        /*
        | MaintenanceCategory filter
        | product_maintenancecategory بيخزن نوع الجهاز (موبايل، تابلت ...)
        */
        if ($request->filled('maintenancecategory')) {
            $query->whereHas(
                'product',
                fn($q) =>
                $q->whereIn('product_maintenancecategory', $request->maintenancecategory)
            );
        }
        // Sort
        if ($request->sort === 'price_asc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'asc')
                ->select('ecommerceproducts.*');
        } elseif ($request->sort === 'price_desc') {
            $query->join('products as p_sort', 'ecommerceproducts.product_id', '=', 'p_sort.product_id')
                ->orderBy('p_sort.product_sellprice', 'desc')
                ->select('ecommerceproducts.*');
        } else {
            $query->orderBy('ecommerceproducts.updated_at', 'desc');
        }

        // $Products = $query->paginate(12)->withQueryString();

        $FilterModels = \App\Models\MaintenanceModel::where('maintenancemodel_delete', 0)
            ->get();
        // ← لو مفيش column maintenancemodel_delete امسح الـ where

        $FilterMaintenanceCategories = \App\Models\MaintenanceCategory::where('maintenancecategory_delete', 0)
            ->get();
        // ← نفس الكلام

        $FilterSubcategories = \App\Models\Subcategory::where('subcategory_displaystatus', 1)
            ->get();

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProducts', compact(
            'ecommerceSharedData',
            'FilterCategories',
            'FilterBrands',
            'FilterSubcategories',
            'FilterModels',
            'FilterMaintenanceCategories',
            'Products',
            'minPrice',
            'maxPrice',
            'branches',
            'availableColors',
            'usingColorPlaceholder',
            'availableRam',
            'ramPlaceholders',
            'availableStorage',
            'storagePlaceholders',
            'availableCpu',
            'cpuPlaceholders',
            'availableCharger',
            'chargerPlaceholders',
        ));
        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';
        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();


        $Products = Product::all();

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProductsproductineachrow', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }

    public function EcommerceAllProductsserachforproductPost(Request $request)
    {

        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            'search' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $search = $request->search;

        $search = trim($request->input('search'));

        $ProductsIDS = Product::where(function ($query) use ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%")
                ->orWhere('product_category', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancemodel', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecategory', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecompany', 'LIKE', "%{$search}%")
                ->orWhere('product_description', 'LIKE', "%{$search}%");
        })->pluck('product_id');

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->whereIn('product_id', $ProductsIDS)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $Products = Product::all();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';
        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProductsserachforproduct', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }

    public function EcommerceAllProductsserachforproductrowPost(Request $request)
    {

        $validator = Validator::make($request->all(), [
            '_token' => 'required|string',
            'search' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $search = $request->search;

        $search = trim($request->input('search'));

        $ProductsIDS = Product::where(function ($query) use ($search) {
            $query->where('product_name', 'LIKE', "%{$search}%")
                ->orWhere('product_category', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancemodel', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecategory', 'LIKE', "%{$search}%")
                ->orWhere('product_maintenancecompany', 'LIKE', "%{$search}%")
                ->orWhere('product_description', 'LIKE', "%{$search}%");
        })->pluck('product_id');

        $AllEcommerceProducts = EcommerceProduct::where('ecommerceproduct_displaystatus', '1')
            ->whereIn('product_id', $ProductsIDS)
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $Products = Product::all();


        $PartnerCompanies = MaintenanceCompany::where('maintenancecompany_active', '1')
            ->whereNotNull('maintenancecompany_image')
            ->orderBy('updated_at', 'desc')
            ->get();

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'جميع المنتجات';

        return view('ecommerce.EcommerceAllProducts.EcommerceAllProductsserachforproductrow', compact('Products', 'AllEcommerceProducts', 'ecommerceSharedData', 'PartnerCompanies'));
    }
}
