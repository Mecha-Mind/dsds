@php
    $Page_title = $Branch->branch_name . ' - كشف الحساب ';
    $description = 'تفاصيل كشف الحساب لفرع ' . $Branch->branch_name . '، متابعة شاملة للمعاملات المالية والعملاء.';
    $keywords = 'كشف حساب, ' . $Branch->branch_name . ', حسابات العملاء, مبيعات, معاملات مالية';
    $og_title = $Branch->branch_name . ' - كشف الحساب';
    $og_description = 'استعرض كشف الحساب المالي لفرع ' . $Branch->branch_name . ' لتتبع المبيعات والمشتريات والعملاء.';
    $og_image = url('/images/brancheslogo/' . $Branch->branch_image);
    $og_type = 'website';
    
@endphp
@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

@if ($Customer)
    <div id="desktop_content_ShoppingCart">

        <div class="container pb-5">
            <div class="row g-4">
                <div class="col-md-9">
                    <div class="card p-4 text-center">
                        @if ($AllEcommerceProducts->isEmpty())
                            <div class="row m-auto">
                                <div class="col-12 d-flex align-items-center justify-content-center text-center m-auto">
                                    <img src="{{ url('/images/socialmediacontacts/emptybasket.png') }}" alt=" سلة التسوق "
                                        class="ShoppingCartimg">
                                </div>
                            </div>
                            <div class="row m-auto">
                                <div
                                    class="col-12 categorytitlehomepagemobile d-flex align-items-center justify-content-center text-center m-auto">
                                    ليس لديك طلبات في حالة الشحن حاليا
                                </div>
                            </div>
                            <div class="row m-auto pt-3">
                                <div
                                    class="col-12 footerfirstdescription d-flex align-items-center justify-content-center text-center m-auto">
                                    {{ $Branch->branch_name }} مكانك الأول لكل احتياجات
                                    <br />
                                    تسوق الأن مع أكبر تشكيلة منتجات
                                </div>
                            </div>
                            <div class="row m-auto">
                                <div class="col-12 d-flex align-items-center justify-content-center text-center m-auto">
                                    <a href="{{ route('EcommerceAllProducts') }}" class="btn btn-primary mt-3 w-100">
                                        تسوق الآن
                                    </a>
                                </div>
                            </div>
                        @else
                            <h5 class="text-end fw-bold footerfirstheadline pb-2">
                                كشف حسابي
                            </h5>
                            <p class="text-center fw-bold footerfirstdescription" style="direction: rtl">
                                قائمة المنتجات
                            </p>

                            @php
                                $ProductsById = collect($Products)->keyBy('product_id');
                                $AllEcommerceProductsId = collect($AllEcommerceProducts)->keyBy('product_id');
                            @endphp
                            @foreach ($CustomerSaleProductBillProducts as $product)
                                @php
                                    $ecommperceProductData =
                                        $AllEcommerceProductsId[$product->saleproductbill_productname] ?? null;
                                    $productData = $ProductsById[$product->saleproductbill_productname] ?? null;
                                @endphp

                                @if ($ecommperceProductData && $productData)
                                    <div class="ShoppingCartProductCard d-flex align-items-center justify-content-between"
                                        style="direction: rtl">
                                        <div class="row ShoppingCartProductName pt-0 pr-3 w-100 m-auto"
                                            style="direction: rtl">
                                            {{ $productData->product_name }}
                                            <div class="row ShoppingCartProductPrice pt-3 pr-0 w-100 m-auto"
                                                style="direction: rtl">
                                                <div
                                                    class="col-12 d-flex align-items-center justify-content-start gap-2 m-0 p-0">
                                                    <p class="m-0 fw-bold">
                                                        الكمية
                                                        {{ $product->saleproductbill_productquantity }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row ShoppingCartProductPrice pt-3 pr-0 w-100 m-auto"
                                                style="direction: rtl">
                                                <div
                                                    class="col-12 d-flex align-items-center justify-content-start gap-2 m-0 p-0">
                                                    <p class="m-0 fw-bold">
                                                        {{ $product->saleproductbill_productpaidprice }}
                                                        جنية
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row ShoppingCartProductName pl-0 w-100 m-auto"
                                            style="direction: ltr">
                                            <a href="{{ route('ProductDetails', $product->saleproductbill_productname) }}"
                                                class="d-flex align-items-center justify-content-start">
                                                <img src="{{ url('/images/productsimages/' . $productData->product_image) }}"
                                                    class="ShoppingCartProductImg me-3" alt="product">
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            <div class="d-flex justify-content-center mt-4" style="height:36px; overflow:hidden;">
                                <nav role="navigation" aria-label="Pagination Navigation"
                                    class="flex items-center justify-between w-100">
                                    <div class="row">
                                        <div class="col-5"></div>
                                        <div class="col-1 m-auto">
                                            <div class="hidden sm:hidden">
                                                <a href="{{ $CustomerSaleProductBillProducts->previousPageUrl() }}"
                                                    class="pagination-button decorationnone">
                                                    <button class="btn apponwer_systemprimarybtn">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-1 m-auto">
                                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                                <a href="{{ $CustomerSaleProductBillProducts->nextPageUrl() }}"
                                                    class="pagination-button decorationnone">
                                                    <button class="btn apponwer_systemprimarybtn">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-5"></div>
                                    </div>
                                </nav>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-0 footerfirstdescription">
                        <a href="{{ route('UserPersonalPage') }}"
                            class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            معلوماتي الشخصية
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-user"></i>
                            </button>
                        </a>
                        <a href="{{ route('UserPersonalUnderRequstProducts') }}"
                            class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            طلباتي
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                        </a>
                        <a href="{{ route('UserPersonalStatement') }}"
                            class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            كشف حسابي
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-credit-card"></i>
                            </button>
                        </a>
                        <a href="{{ route('UserPersonalLogOut') }}" class="UserPersonalPageLogoutLink text-right">تسجيل
                            الخروج</a>
                    </div>

                    @if ($TotalAfterAiscountAndServiceAddition != '0.00')
                        <div class="card p-0 footerfirstdescription">
                            <p class="text-center fw-bold footerfirstdescription pt-4" style="direction: rtl">
                                الحسابات
                            </p>
                            <div class="UserPersonalPageSideBarLink footerfirstdescription d-flex justify-content-between"
                                style="direction: rtl">
                                <span>
                                    اجمالي المنتجات
                                </span>
                                <span>
                                    {{ $TotalAfterAiscountAndServiceAddition }}
                                </span>
                            </div>
                            @if ($TotalCustomerForHim != '0.00')
                                <div class="UserPersonalPageSideBarLink footerfirstdescription d-flex justify-content-between"
                                    style="direction: rtl">
                                    <span>مجموع دفعته</span>
                                    <span>{{ $TotalCustomerForHim }}</span>
                                </div>
                            @endif

                            @if ($TotalCustomerAgainstHim != '0.00')
                                <div class="UserPersonalPageSideBarLink footerfirstdescription d-flex justify-content-between"
                                    style="direction: rtl">
                                    <span>
                                        الديون القديمة
                                    </span>
                                    <span>
                                        {{ $TotalCustomerAgainstHim }}
                                    </span>
                                </div>
                            @endif

                            @if ($TotalAfterAiscountAndServiceAddition != '0.00')
                                @if ($TotalAfterAiscountAndServiceAddition > '0.00')
                                    <div class="UserPersonalPageSideBarLink footerfirstdescription d-flex justify-content-between"
                                        style="direction: rtl">
                                        <span>
                                            الباقي عليا
                                        </span>
                                        <span>
                                            {{ $TheTotalCustomerRestAmount }}
                                        </span>
                                    </div>
                                @else
                                    <div class="UserPersonalPageSideBarLink footerfirstdescription d-flex justify-content-between"
                                        style="direction: rtl">
                                        <span>
                                            الباقي لي
                                        </span>
                                        <span>
                                            {{ $TheTotalCustomerRestAmount }}
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div id="mobile_content_ShoppingCart">
        <div class="row g-4 pb-4">
            <div class="col-md-12">
                <div class="card p-0 footerfirstdescription">
                    <a href="{{ route('UserPersonalPage') }}"
                        class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                        معلوماتي الشخصية
                        <button class="btn apponwer_systemprimarybtn">
                            <i class="fas fa-user"></i>
                        </button>
                    </a>
                    <a href="{{ route('UserPersonalUnderRequstProducts') }}"
                        class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                        طلباتي
                        <button class="btn apponwer_systemprimarybtn">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </a>
                    <a href="{{ route('UserPersonalStatement') }}"
                        class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                        كشف حسابي
                        <button class="btn apponwer_systemprimarybtn">
                            <i class="fas fa-credit-card"></i>
                        </button>
                    </a>
                    <a href="{{ route('UserPersonalLogOut') }}" class="UserPersonalPageLogoutLink text-right">تسجيل
                        الخروج</a>
                </div>
            </div>

            @if ($TotalAfterAiscountAndServiceAddition != '0.00')
                <div class="col-md-12">
                    <div class="card p-0 footerfirstdescription">
                        <div class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                            <span>اجمالي المنتجات</span>
                            <span>{{ $TotalAfterAiscountAndServiceAddition }}</span>
                        </div>
                        @if ($TotalCustomerForHim != '0.00')
                            <div class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                                <span>مجموع دفعته</span>
                                <span>{{ $TotalCustomerForHim }}</span>
                            </div>
                        @endif
                        @if ($TotalCustomerAgainstHim != '0.00')
                            <div class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                                <span>الديون القديمة</span>
                                <span>{{ $TotalCustomerAgainstHim }}</span>
                            </div>
                        @endif
                        @if ($TotalAfterAiscountAndServiceAddition != '0.00')
                            <div class="UserPersonalPageSideBarLink footerfirstdescription" style="direction: rtl">
                                <span>
                                    {{ $TotalAfterAiscountAndServiceAddition > '0.00' ? 'الباقي عليا' : 'الباقي لي' }}
                                </span>
                                <span>{{ $TheTotalCustomerRestAmount }}</span>
                            </div>
                        @endif

                    </div>
                </div>
            @endif


            <div class="col-md-12">
                <div class="card p-4 text-center">
                    @if ($AllEcommerceProducts->isEmpty())
                        <div class="row m-auto">
                            <div class="col-12 d-flex align-items-center justify-content-center text-center m-auto">
                                <img src="{{ url('/images/socialmediacontacts/emptybasket.png') }}"
                                    alt=" سلة التسوق " class="ShoppingCartimg">
                            </div>
                        </div>
                        <div class="row m-auto">
                            <div
                                class="col-12 categorytitlehomepagemobile d-flex align-items-center justify-content-center text-center m-auto">
                                ليس لديك طلبات في حالة الشحن حاليا
                            </div>
                        </div>
                        <div class="row m-auto pt-3">
                            <div
                                class="col-12 footerfirstdescription d-flex align-items-center justify-content-center text-center m-auto">
                                {{ $Branch->branch_name }} مكانك الأول لكل احتياجات
                                <br />
                                تسوق الأن مع أكبر تشكيلة منتجات
                            </div>
                        </div>
                        <div class="row m-auto">
                            <div class="col-12 d-flex align-items-center justify-content-center text-center m-auto">
                                <a href="{{ route('EcommerceAllProducts') }}" class="btn btn-primary mt-3 w-100">
                                    تسوق
                                </a>
                            </div>
                        </div>
                    @else
                        <h5 class="text-end fw-bold footerfirstheadline">
                            كشف حسابي
                        </h5>
                        <p class="text-center fw-bold footerfirstdescription" style="direction: rtl">
                            قائمة المنتجات
                        </p>
                        @php
                            $ProductsById = collect($Products)->keyBy('product_id');
                            $AllEcommerceProductsId = collect($AllEcommerceProducts)->keyBy('product_id');
                        @endphp

                        @foreach ($CustomerSaleProductBillProducts as $product)
                            @php
                                $ecommperceProductData =
                                    $AllEcommerceProductsId[$product->saleproductbill_productname] ?? null;
                                $productData = $ProductsById[$product->saleproductbill_productname] ?? null;
                            @endphp

                            @if ($ecommperceProductData && $productData)
                                <div class="ShoppingCartProductCardMobile d-flex align-items-center justify-content-between"
                                    style="direction: rtl">
                                    <div class="row ShoppingCartProductName pt-0 pr-3 w-100 m-auto"
                                        style="direction: rtl">
                                        {{ $productData->product_name }}
                                        <div class="row ShoppingCartProductPrice pt-3 pr-0 w-100 m-auto"
                                            style="direction: rtl">
                                            <div
                                                class="col-12 d-flex align-items-center justify-content-start gap-2 m-0 p-0">
                                                <p class="m-0 fw-bold">
                                                    الكمية
                                                    {{ $product->saleproductbill_productquantity }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row ShoppingCartProductPrice pt-3 pr-0 w-100 m-auto"
                                            style="direction: rtl">
                                            <div
                                                class="col-12 d-flex align-items-center justify-content-start gap-2 m-0 p-0">
                                                <p class="m-0 fw-bold">
                                                    {{ $product->saleproductbill_productpaidprice }}
                                                    جنية
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ShoppingCartProductName pl-0 w-100 m-auto" style="direction: ltr">
                                        <a href="{{ route('ProductDetails', $product->saleproductbill_productname) }}"
                                            class="d-flex align-items-center justify-content-start">
                                            <img src="{{ url('/images/productsimages/' . $productData->product_image) }}"
                                                class="ShoppingCartProductImg me-3" alt="product">
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div class="d-flex justify-content-center mt-4" style="height:36px; overflow:hidden;">
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex items-center justify-between w-100">
                                <div class="row">
                                    <div class="col-4"></div>
                                    <div class="col-2 m-auto">
                                        <div class="hidden sm:hidden">
                                            <a href="{{ $CustomerSaleProductBillProducts->previousPageUrl() }}"
                                                class="pagination-button decorationnone">
                                                <button class="btn apponwer_systemprimarybtn">
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-2 m-auto">
                                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                            <a href="{{ $CustomerSaleProductBillProducts->nextPageUrl() }}"
                                                class="pagination-button decorationnone">
                                                <button class="btn apponwer_systemprimarybtn">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-4"></div>
                                </div>
                            </nav>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
@endif

@endsection