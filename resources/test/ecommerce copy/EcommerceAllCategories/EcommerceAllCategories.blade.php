@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - جميع الأقسام';
    $description =
        'اكتشف جميع الأقسام المتوفرة في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        ' لدينا. منتجات وخدمات متنوعة بجودة عالية وأسعار مناسبة.';
    $keywords = 'أقسام, ' . $ecommerceSharedData['branch']->branch_name . ', تسوق, عروض, منتجات, خدمات, جميع الأقسام';
    $og_title = $ecommerceSharedData['branch']->branch_name . ' - جميع الأقسام';
    $og_description =
        'تصفح جميع الأقسام في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        ' واكتشف أفضل العروض والخدمات المتوفرة.';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';

@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')
    {{-- Page Header --}}
    <x-page-header title="التصنيفات" :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'التصنيفات', 'url' => route('EcommerceAllCategories')],
    ]" />

    {{-- Brands Bar --}}
    @include('components.brand-logos')

    <section class="section-padding" aria-labelledby="categories-title">
        <div class="container">
            
            <div class="categories-grid">
                @foreach ($Categories as $cat)
                    <a href="{{ route('CategoryProduct', $cat->category_id) }}" class="category-card">
                        <img rel="preload" src="{{ asset('images/categoriesimages/' . $cat->category_image) }}"
                            alt="{{ $cat->category_name }}" class="category-card__img" loading="lazy">
                        <span class="category-card__name">{{ $cat->category_name }}</span>
                    </a>
                @endforeach
                @foreach ($Categories as $cat)
                    <a href="{{ route('CategoryProduct', $cat->category_id) }}" class="category-card">
                        <img rel="preload" src="{{ asset('images/categoriesimages/' . $cat->category_image) }}"
                            alt="{{ $cat->category_name }}" class="category-card__img" loading="lazy">
                        <span class="category-card__name">{{ $cat->category_name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection
