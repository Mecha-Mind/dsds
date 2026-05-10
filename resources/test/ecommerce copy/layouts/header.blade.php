<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $Page_title }}</title>
    @php
        use App\Models\Branche;
        $Branch = Branche::where('branch_id', '1')->first();
    @endphp
    <link rel="icon" href="{{ url('/images/brancheslogo/' . $Branch->branch_image) }}" type="image/x-icon">

    <link rel="canonical" href="{{ url()->current() }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta name="robots" content="index, follow">
    <meta name="language" content="ar">

    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta property="og:title" content="{{ $og_title }}" />
    <meta property="og:description" content="{{ $og_description }}" />
    <meta property="og:image" content="{{ $og_image }}" />
    <meta property="og:image:width" content="1080" />
    <meta property="og:image:height" content="1080" />
    <meta property="og:type" content="{{ $og_type }}" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $og_title ?? '' }}">
    <meta name="twitter:description" content="{{ $og_description ?? '' }}">
    <meta name="twitter:image" content="{{ $og_image ?? asset('default-image.png') }}">

    @php
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'url' => config('app.url'),
            'name' => $Branch->branch_name ?? 'المتجر',
            'logo' => $og_image ?? asset('default-image.png'),
        ];
    @endphp

    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <link rel="stylesheet" href="{{ url('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/bootstrap.css') }}">
    <link rel="stylesheet"
        href="{{ asset('css/ecomerce-style.css') }}?v={{ filemtime(public_path('css/ecomerce-style.css')) }}"
        type="text/css">
    <link rel="stylesheet" href="{{ asset('css/apponwer.css') }}?v={{ filemtime(public_path('css/apponwer.css')) }}">
    @php
        use App\Models\ApplicationColor;
        $ApplicationColor = ApplicationColor::find(1); // simpler than where()->first()
    @endphp

    <style>
        :root {

            --primary-color: {{ $ApplicationColor->ecommerceprimary_color ?? '#710000' }};
            --secondary-color: {{ $ApplicationColor->ecommercesecondary_color ?? '#020202' }};
            --light-color: {{ $ApplicationColor->ecommercetext_color ?? '#f8f9fa' }};

            --secondarytext-color: {{ $ApplicationColor->secondarytext_color ?? '#f8f9fa' }};
            --accent-color: {{ $ApplicationColor->accent_color ?? '#edb9b3' }};
            --dark-color: {{ $ApplicationColor->dark_color ?? '#606a55' }};

            --inactive-color: {{ $ApplicationColor->inactive_color ?? '#606a55' }};

        }
    </style>

    @yield('css')
    <script>
        function toggleElements(desktopId, mobileId) {
            var isDesktop = window.innerWidth >= 1024;
            var desktopElement = document.getElementById(desktopId);
            var mobileElement = document.getElementById(mobileId);

            if (desktopElement) {
                desktopElement.style.display = isDesktop ? 'block' : 'none';
            }
            if (mobileElement) {
                mobileElement.style.display = isDesktop ? 'none' : 'block';
            }
        }

        function checkScreenSize() {
            toggleElements('desktop_content_navbar', 'mobile_content_navbar');
            toggleElements('desktop_content_scrollingoffer', 'mobile_content_scrollingoffer');
            toggleElements('desktop_content_partnercompany', 'mobile_content_partnercompany');
            toggleElements('desktop_content_themostsellingproducts', 'mobile_content_themostsellingproducts');
            toggleElements('desktop_content_HomePageCategories', 'mobile_content_HomePageCategories');
            toggleElements('desktop_content_Productswithoffersanddiscounts',
                'mobile_content_Productswithoffersanddiscounts');
            toggleElements('desktop_content_Offersfromtheowner', 'mobile_content_Offersfromtheowner');
            toggleElements('desktop_content_footerfirst', 'mobile_content_footerfirst');
            toggleElements('desktop_content_footercenter', 'mobile_content_footercenter');
            toggleElements('desktop_content_footerlast', 'mobile_content_footerlast');
            toggleElements('desktop_content_EcommerceAllProductsDivofchangingpages',
                'mobile_content_EcommerceAllProductsDivofchangingpages');
            toggleElements('desktop_content_EcommerceAllProductsproductsdivs',
                'mobile_content_EcommerceAllProductsproductsdivs');
            toggleElements('desktop_content_ProductDetails', 'mobile_content_ProductDetails');
            toggleElements('desktop_content_EcommerceKnowAboutUs', 'mobile_content_EcommerceKnowAboutUs');
            toggleElements('desktop_content_ShoppingCart', 'mobile_content_ShoppingCart');
            toggleElements('desktop_content_Doyouneedhelp', 'mobile_content_Doyouneedhelp');
            toggleElements('desktop_content_ourservice', 'mobile_content_ourservice');
            toggleElements('desktop_content_ourcustomer', 'mobile_content_ourcustomer');
            toggleElements('desktop_SpecificationsProduct', 'mobile_SpecificationsProduct');




            toggleElements('system_forms', 'system_forms');


        }

        window.addEventListener('load', function() {
            checkScreenSize();

            var styleDiv = document.getElementById('system_formsstyle');
            if (styleDiv) {
                styleDiv.style.display = 'block';
            }
        });

        window.addEventListener('resize', checkScreenSize);
    </script>

    {{-- @vitereactrefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>

<body>
    <main>
        <div id="system_formsstyle">
