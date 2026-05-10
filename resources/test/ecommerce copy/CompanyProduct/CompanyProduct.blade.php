@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - ' . $Company->maintenancecompany_title;

    $description =
        'مرحبًا بك في ' .
        $ecommerceSharedData['branch']->branch_name .
        ' - اكتشف كل ما يخص ' .
        $Company->maintenancecompany_title .
        ' بأفضل جودة وخدمة.';

    $keywords = implode(', ', [
        $ecommerceSharedData['branch']->branch_name,
        $Company->maintenancecompany_title,
        'عروض',
        'منتجات',
        'خدمات',
        'أفضل الأسعار',
        'جودة عالية',
    ]);

    $og_title = $Page_title;

    $og_description =
        'اكتشف معنا كل ما يتعلق بـ ' .
        $Company->maintenancecompany_title .
        ' في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        '. عروض حصرية وجودة مضمونة!';

    $og_image = url('/images/partnercompany/' . $Company->maintenancecompany_image);
    $og_type = 'website';
    
@endphp
@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    {{-- Div of changing pages --}}
    <div id="desktop_content_EcommerceAllProductsDivofchangingpages">
        <div class="row inactiveBtnbackground Divofchangingpages">
            <div class="col-12">
                <div class="container h-100">
                    <div class="row h-100">
                        <div class="col-6 m-auto">
                            <div class="row">
                                <div class="col-12 m-auto Divofchangingpagesheadtitle">
                                    منتجات شركة {{ $Company->maintenancecompany_title }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 m-auto Divofchangingpagesnavlink">
                                    <a href="{{ route('home') }}" class="Divofchangingpagesnavlink decorationnone">
                                        الرئيسية
                                    </a>
                                    <button class="btn apponwer_systemprimarybtn">
                                        <i class="fas fa-chevron-left p-2"></i>
                                    </button>
                                    <a href="{{ route('CompanyProduct', $Company->maintenancecompany_id) }}"
                                        class="Divofchangingpagesnavlink decorationnone">
                                        {{ $Company->maintenancecompany_title }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="mobile_content_EcommerceAllProductsDivofchangingpages">
        <div class="row inactiveBtnbackground Divofchangingpages">
            <div class="col-12">
                <div class="container h-100">
                    <div class="row h-100">
                        <div class="col-12 m-auto">
                            <div class="row">
                                <div class="col-12 m-auto Divofchangingpagesheadtitle">
                                    منتجات شركة {{ $Company->maintenancecompany_title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('ecommerce.layouts.partnercompany')

    {{-- products div --}}

    <div id="desktop_content_EcommerceAllProductsproductsdivs">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row pt-5 pb-3">
                        <div class="col-3 m-auto">
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-vector-square pt-2 pl-2 "></i>
                            </button>

                            <a href="{{ route('CompanyProductRow', $Company->maintenancecompany_id) }}"
                                class="decorationnone Headlinecolor">
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-sliders-h pt-2 pl-2"></i>
                                </button>
                            </a>
                        </div>
                        <div class="col-6 m-auto">
                            <form action="{{ route('EcommerceAllProductsserachforproductPost') }}" method="post"
                                class="row align-items-center rounded">
                                @csrf
                                @method('post')
                                <button type="submit" class="btn btn-circle">
                                    <div class="row">
                                        <div class="col-md-2 d-flex justify-content-center align-items-center">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-md-10 d-flex justify-content-center align-items-center text-end">
                                            <input type="search" name="search" placeholder=" .... البحث عن منتج" required
                                                class="form-control custom-search-input text-end apponwer_descrptiontext">
                                        </div>
                                    </div>
                                </button>
                            </form>
                        </div>
                        <div class="col-3 text-end">
                            <a href="{{ route('EcommerceMostSaleProducts') }}" class="text-decoration-none">
                                <button class="btn btn-primary">
                                    الاكثر مبيعا
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('ecommerce.layouts.MainProductsDesktop')


    </div>


    <div id="mobile_content_EcommerceAllProductsproductsdivs">
        <div class="row py-3" style="padding: 16px;">
            <div class="col-12 col-md-3 d-flex justify-content-center align-items-center mb-2 mb-md-0">
                <a href="{{ route('CompanyProductRow', $Company->maintenancecompany_id) }}"
                    class="text-decoration-none Headlinecolor me-3">
                    <button class="btn apponwer_systemprimarybtn">
                        <i class="fas fa-sliders-h fs-5"></i>
                    </button>
                </a>
                <button class="btn apponwer_systemprimarybtn">
                    <i class="fas fa-vector-square fs-5 "></i>
                </button>
            </div>

            <div class="col-12 col-md-6 mb-2 mb-md-0">
                <form action="{{ route('EcommerceAllProductsserachforproductPost') }}" method="post"
                    class="d-flex align-items-center border rounded p-1 w-100">
                    @csrf
                    @method('post')
                    <button type="submit" class="btn border-0 d-flex align-items-center px-2">
                        <i class="fa fa-search"></i>
                    </button>
                    <input type="search" name="search" placeholder=" .... البحث " required
                        class="form-control border-0 text-end apponwer_descrptiontext">
                </form>
            </div>

            <div class="col-12 col-md-3 d-flex justify-content-center align-items-center">
                <a href="{{ route('EcommerceMostSaleProducts') }}" class="w-100">
                    <button class="btn btn-primary w-100">
                        الاكثر مبيعا
                    </button>
                </a>
            </div>
        </div>

        @include('ecommerce.layouts.MainProductsMobile')

    </div>

@endsection