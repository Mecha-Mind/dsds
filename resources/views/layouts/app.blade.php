{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO --}}
    <title>@yield('title', 'المتجر الإلكتروني')</title>
    <meta name="description" content="@yield('description', '')">
    
    {{-- Bootstrap RTL --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap">

    {{-- في layouts/app.blade.php --}}
    {{-- حط الـ CSS الجديد --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-details.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">




    <!-- all products controller : 
     // ← FilterCategories للـ sidebar
    $data['FilterCategories'] = Category::withCount(['products' => fn($q) =>
        $q->where('product_delete', 0)
    ])->where('category_displaystatus', 1)->get();

    // ← FilterBrands
    $data['FilterBrands'] = MaintenanceCompany::where('maintenancecompany_active', 1)->get();

    // ← Products مع الفلترة
        $data['Products'] = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)
            ->with('product')
            ->when(request('category'), fn($q, $cats) =>
                $q->whereHas('product', fn($p) => $p->whereIn('product_category', $cats))
            )
            ->when(request('sort') === 'price_asc', fn($q) =>
                $q->join('products', 'ecommerceproducts.product_id', '=', 'products.product_id')
                ->orderBy('products.product_sellprice')
        )
        ->paginate(12); -->

        <!-- product details controller :
            $data['Reviews']       = CustomerProductComment::where('customerproductcomment_productname', $product->product->product_name)->where('customerproductcomment_visibility', 1)->get();
            $data['reviewsCount']  = $data['Reviews']->count();
            $data['avgRating']     = $data['Reviews']->avg('customerproductcomment_rating') ?? 0;
            $data['RelatedProducts'] = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)->with('product')->take(4)->get();
            $data['SimilarProducts'] = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)->with('product')->take(8)->get(); -->


        <!-- cart controller : 
         // ← CartItems بتيجي من customerrequestproducts
            $data['CartItems'] = CustomerRequestProduct::where('customerrequestproduct_customeraccount', session('customer_account'))
            ->where('customerrequestproduct_billstatus', 0)
            ->with('product')
            ->get();
            $data['subtotal'] = $data['CartItems']->sum(fn($i) => ($i->customerrequestproduct_productsellprice ?? 0) * ($i->customerrequestproduct_productquantity ?? 1));
            $data['SuggestedProducts'] = EcommerceProduct::where('ecommerceproduct_displaystatus', 1)->with('product')->inRandomOrder()->take(8)->get(); -->





    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    {{-- CSS بتاعك --}}
    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/css/home.css', 'resources/css/static-pages.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
        <link rel="stylesheet" href="{{ asset('css/static-pages.css') }}">
    @endif

</head>
<body>

    {{-- الـ Navbar Component --}}
     @include('components.navbar', [
        'staticLinks' => $staticLinks ?? [],
        'navData'     => $navData     ?? [],
        'branchName'  => $branchName  ?? '',
        'phone'       => $phone       ?? '',
    ])

    {{-- المحتوى الرئيسي --}}
    <main id="main-content" role="main">
        @yield('content')
    </main>
    @include('components.footer', [
        'footer'     => $footer     ?? [],
        'categories' => isset($navData['categories']) ? $navData['categories'] : [],
        'logo'       => $logo       ?? 'images/primaryLogo.png',
    ])
    

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            defer></script>

    @yield('scripts')
</body>
</html>