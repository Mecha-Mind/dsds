@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - ' . $Category->category_name;

    $description =
        'مرحبًا بك في ' .
        $ecommerceSharedData['branch']->branch_name .
        ' - اكتشف كل ما يخص ' .
        $Category->category_name .
        ' بأفضل جودة وخدمة.';

    $keywords = implode(', ', [
        $ecommerceSharedData['branch']->branch_name,
        $Category->category_name,
        'عروض',
        'منتجات',
        'خدمات',
        'أفضل الأسعار',
        'جودة عالية',
    ]);

    $og_title = $Page_title;

    $og_description =
        'اكتشف معنا كل ما يتعلق بـ ' .
        $Category->category_name .
        ' في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        '. عروض حصرية وجودة مضمونة!';

    $og_image = url('/images/categoriesimages/' . $Category->category_image);
    $og_type = 'website';
    

@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    {{-- Div of changing pages --}}
    @include('ecommerce.layouts.CategoryProductchangingpages')

    @include('ecommerce.layouts.partnercompany')


    @if (!$Subcategories->isEmpty())

        <div id="desktop_content_HomePageCategories">
            <div class="row pt-5">
                <div class="container">
                    <div class="row g-3 g-md-4 justify-content-center pb-5">
                        @foreach ($Subcategories as $Subcategory)
                            @if ($Subcategory->subcategory_image)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <a href="{{ route('SubcategoryProduct', $Subcategory->subcategory_id) }}"
                                        class="text-decoration-none">
                                        <div class="category-card h-100 d-flex flex-column align-items-center p-3">
                                            <div class="category-image-container ratio ratio-1x1 mb-2 w-100">
                                                <img src="{{ url('/images/subcategoriesimages/' . $Subcategory->subcategory_image) }}"
                                                    alt="{{ $Subcategory->subcategory_name }}"
                                                    class="object-fit-contain w-100 h-100" loading="lazy">
                                            </div>
                                            <h3 class="category-title text-center mb-0 fs-6 fw-semibold">
                                                {{ $Subcategory->subcategory_name }}
                                            </h3>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="mobile_content_HomePageCategories">
            <div class="row pt-5">
                <div class="col-12 p-3 m-auto">
                    <div class="row p-3 justify-content-center">
                        @foreach ($Subcategories as $Subcategory)
                            @if ($Subcategory->subcategory_image)
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 pb-2">
                                    <a href="{{ route('SubcategoryProduct', $Subcategory->subcategory_id) }}"
                                        class="text-decoration-none">
                                        <div class="category-card h-100 d-flex flex-column align-items-center p-3">
                                            <div class="category-image-container ratio ratio-1x1 mb-2 w-100">
                                                <img src="{{ url('/images/subcategoriesimages/' . $Subcategory->subcategory_image) }}"
                                                    alt="{{ $Subcategory->subcategory_name }}"
                                                    class="object-fit-contain w-100 h-100" loading="lazy">
                                            </div>
                                            <h3 class="category-title text-center mb-0 fs-6 fw-semibold">
                                                {{ $Subcategory->subcategory_name }}
                                            </h3>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- products div --}}

    @include('ecommerce.layouts.CategoryProductproductsdivs')

@endsection