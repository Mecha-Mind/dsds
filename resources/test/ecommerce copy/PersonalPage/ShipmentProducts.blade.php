@php
    $Page_title = $Branch->branch_name . ' - المنتجات المطلوبة ';
    $description = 'تصفح المنتجات المطلوبة في فرع ' . $Branch->branch_name . ' مع تفاصيل محدثة وأسعار تنافسية.';
    $keywords = 'منتجات, طلبات, ' . $Branch->branch_name . ', شراء, مبيعات, مخزون';
    $og_title = $Branch->branch_name . ' - المنتجات المطلوبة';
    $og_description =
        'اكتشف قائمة المنتجات المطلوبة في فرع ' . $Branch->branch_name . ' وتابع التحديثات والطلبات الحالية.';
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
                                <h5 class="text-end fw-bold footerfirstheadline">
                                    طلباتي
                                </h5>
                                <p class="text-center fw-bold footerfirstdescription" style="direction: rtl">
                                    رقم الطلب {{ $CustomerBillRefrance }}
                                </p>
                                <div class="timeline-container" style="direction: rtl">
                                    @if ($OrderConfirmationTime != null)
                                        @php
                                            \Carbon\Carbon::setLocale('ar');
                                            $formattedDateOrderConfirmationTime = \Carbon\Carbon::parse(
                                                $OrderConfirmationTime,
                                            )->translatedFormat('l، j F Y، g:i A');
                                            $formattedDateOrderConfirmationTime = str_replace(
                                                ['AM', 'PM'],
                                                ['صباحاً', 'مساءً'],
                                                $formattedDateOrderConfirmationTime,
                                            );
                                        @endphp
                                        <div class="order-step">
                                            <div class="order-icon completed">✔</div>
                                            <div class="order-title timelineName">تأكيد الطلب</div>
                                            <div class="order-date timelinePrice">
                                                {{ $formattedDateOrderConfirmationTime }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($TimePrepareShipping != null)
                                        @php
                                            \Carbon\Carbon::setLocale('ar');
                                            $formattedDateTimePrepareShipping = \Carbon\Carbon::parse(
                                                $TimePrepareShipping,
                                            )->translatedFormat('l، j F Y، g:i A');
                                            $formattedDateTimePrepareShipping = str_replace(
                                                ['AM', 'PM'],
                                                ['صباحاً', 'مساءً'],
                                                $formattedDateTimePrepareShipping,
                                            );
                                        @endphp
                                        <div class="order-step">
                                            <div class="order-icon completed">✔</div>
                                            <div class="order-title timelineName">طلبك جاهز للشحن</div>
                                            <div class="order-date timelinePrice">
                                                {{ $formattedDateTimePrepareShipping }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="order-step">
                                        <div class="order-icon">✔</div>
                                        <div class="order-title timelinePrice">طلبك خارج للتوصيل</div>
                                    </div>

                                </div>
                                @php
                                    $ProductsById = collect($Products)->keyBy('product_id');
                                    $CustomerRequestProductsByProductId = collect($CustomerSaleProductBillProducts)->keyBy(
                                        'saleproductbill_productname',
                                    );
                                @endphp
                                @foreach ($AllEcommerceProducts as $product)
                                    @php
                                        $customerRequest =
                                            $CustomerRequestProductsByProductId[$product->product_id] ?? null;
                                        $productData = $ProductsById[$product->product_id] ?? null;
                                    @endphp

                                    @if ($customerRequest && $productData)
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
                                                            {{ $customerRequest->saleproductbill_productquantity }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row ShoppingCartProductPrice pt-3 pr-0 w-100 m-auto"
                                                    style="direction: rtl">
                                                    <div
                                                        class="col-12 d-flex align-items-center justify-content-start gap-2 m-0 p-0">
                                                        <p class="m-0 fw-bold">
                                                            {{ $customerRequest->saleproductbill_productpaidprice }}
                                                            جنية
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row ShoppingCartProductName pl-0 w-100 m-auto"
                                                style="direction: ltr">
                                                <a href="{{ route('ProductDetails', $product->product_id) }}"
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
                                                    <a href="{{ $AllEcommerceProducts->previousPageUrl() }}"
                                                        class="pagination-button decorationnone">
                                                        <button class="btn apponwer_systemprimarybtn">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-1 m-auto">
                                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                                    <a href="{{ $AllEcommerceProducts->nextPageUrl() }}"
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
                                طلباتي
                            </h5>
                            <p class="text-center fw-bold footerfirstdescription" style="direction: rtl">
                                رقم الطلب {{ $CustomerBillRefrance }}
                            </p>
                            <div class="row p-2">
                                <div class="col-12">
                                    @if ($OrderConfirmationTime != null)
                                        @php
                                            \Carbon\Carbon::setLocale('ar');
                                            $formattedDateOrderConfirmationTime = \Carbon\Carbon::parse(
                                                $OrderConfirmationTime,
                                            )->translatedFormat('l، j F Y، g:i A');
                                            $formattedDateOrderConfirmationTime = str_replace(
                                                ['AM', 'PM'],
                                                ['صباحاً', 'مساءً'],
                                                $formattedDateOrderConfirmationTime,
                                            );
                                        @endphp
                                        <div class="order-step">
                                            <div class="order-icon completed">✔</div>
                                            <div class="order-title timelineName">تأكيد الطلب</div>
                                            <div class="order-date timelinePrice">
                                                {{ $formattedDateOrderConfirmationTime }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-12">

                                    @if ($TimePrepareShipping != null)
                                        @php
                                            \Carbon\Carbon::setLocale('ar');
                                            $formattedDateTimePrepareShipping = \Carbon\Carbon::parse(
                                                $TimePrepareShipping,
                                            )->translatedFormat('l، j F Y، g:i A');
                                            $formattedDateTimePrepareShipping = str_replace(
                                                ['AM', 'PM'],
                                                ['صباحاً', 'مساءً'],
                                                $formattedDateTimePrepareShipping,
                                            );
                                        @endphp
                                        <div class="order-step">
                                            <div class="order-icon completed">✔</div>
                                            <div class="order-title timelineName">طلبك جاهز للشحن</div>
                                            <div class="order-date timelinePrice">
                                                {{ $formattedDateTimePrepareShipping }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-12">
                                    <div class="order-step">
                                        <div class="order-icon">✔</div>
                                        <div class="order-title timelinePrice">طلبك خارج للتوصيل</div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $ProductsById = collect($Products)->keyBy('product_id');
                                $CustomerRequestProductsByProductId = collect($CustomerSaleProductBillProducts)->keyBy(
                                    'saleproductbill_productname',
                                );
                            @endphp
                            @foreach ($AllEcommerceProducts as $product)
                                @php
                                    $customerRequest = $CustomerRequestProductsByProductId[$product->product_id] ?? null;
                                    $productData = $ProductsById[$product->product_id] ?? null;
                                @endphp

                                @if ($customerRequest && $productData)
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
                                                        {{ $customerRequest->saleproductbill_productquantity }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row ShoppingCartProductPrice pt-3 pr-0 w-100 m-auto"
                                                style="direction: rtl">
                                                <div
                                                    class="col-12 d-flex align-items-center justify-content-start gap-2 m-0 p-0">
                                                    <p class="m-0 fw-bold">
                                                        {{ $customerRequest->saleproductbill_productpaidprice }}
                                                        جنية
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row ShoppingCartProductName pl-0 w-100 m-auto" style="direction: ltr">
                                            <a href="{{ route('ProductDetails', $product->product_id) }}"
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
                                                <a href="{{ $AllEcommerceProducts->previousPageUrl() }}"
                                                    class="pagination-button decorationnone">
                                                    <button class="btn apponwer_systemprimarybtn">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-2 m-auto">
                                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                                <a href="{{ $AllEcommerceProducts->nextPageUrl() }}"
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