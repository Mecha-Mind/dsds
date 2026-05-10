@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - الصيانة ';
    $description =
        'صفحة الصيانة لفرع ' . $ecommerceSharedData['branch']->branch_name . ' - خدمات الصيانة المتخصصة والدعم الفني لجميع الأجهزة والمعدات.';
    $keywords = 'الصيانة, ' . $ecommerceSharedData['branch']->branch_name . ', دعم فني, خدمات الصيانة, إصلاح الأجهزة, صيانة المعدات';
    $og_title = $ecommerceSharedData['branch']->branch_name . ' - خدمات الصيانة والدعم الفني';
    $og_description =
        'اكتشف خدمات الصيانة والدعم الفني المتكاملة في فرع ' . $ecommerceSharedData['branch']->branch_name . '، نحن هنا لخدمتك بأفضل جودة.';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';
    
@endphp


@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    @if ($Customer)

        <div id="desktop_content_ShoppingCart">

            <div class="container">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card p-4 text-center">
                            @if ($MaintenanceOrders->isEmpty())
                                <div class="row m-auto">
                                    <div class="col-12 d-flex align-items-center justify-content-center text-center m-auto">
                                        <img src="{{ url('/images/socialmediacontacts/emptybasket.png') }}" alt=" سلة التسوق "
                                            class="ShoppingCartimg">
                                    </div>
                                </div>
                                <div class="row m-auto">
                                    <div
                                        class="col-12 categorytitlehomepagemobile d-flex align-items-center justify-content-center text-center m-auto">
                                        ليس لديك طلبات في صيانة حاليا
                                    </div>
                                </div>
                                <div class="row m-auto pt-3">
                                    <div
                                        class="col-12 footerfirstdescription d-flex align-items-center justify-content-center text-center m-auto">
                                        {{ $ecommerceSharedData['branch']->branch_name }} مكانك الأول لكل احتياجات
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
                                    صيانتي
                                </h5>
                                @foreach ($MaintenanceOrders as $Order)
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="text-center fw-bold footerfirstdescription">
                                                رقم الطلب {{ $Order->maintenanceorder_ordernumber }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="ShoppingCartProductCard d-flex align-items-center justify-content-center"
                                        style="direction: rtl">
                                        <div class="row w-100">
                                            <div class="col-12">
                                                <div class="timeline-container" style="direction: rtl">
                                                    @php
                                                        \Carbon\Carbon::setLocale('ar');
                                                        $formattedDateOrderConfirmationTime = \Carbon\Carbon::parse(
                                                            $Order->maintenanceorder_orderreciveddate,
                                                        )->translatedFormat('l، j F Y، g:i A');
                                                        $formattedDateOrderConfirmationTime = str_replace(
                                                            ['AM', 'PM'],
                                                            ['صباحاً', 'مساءً'],
                                                            $formattedDateOrderConfirmationTime,
                                                        );
                                                    @endphp
                                                    <div class="order-step">
                                                        <div class="order-icon completed">✔</div>
                                                        <div class="order-title timelineName"> تحت الاصلاح </div>
                                                        <div class="order-date timelinePrice">
                                                            {{ $formattedDateOrderConfirmationTime }}
                                                        </div>
                                                    </div>

                                                    @php
                                                        \Carbon\Carbon::setLocale('ar');
                                                        $formattedDateTimePrepareShipping = \Carbon\Carbon::parse(
                                                            $Order->updated_at,
                                                        )->translatedFormat('l، j F Y، g:i A');
                                                        $formattedDateTimePrepareShipping = str_replace(
                                                            ['AM', 'PM'],
                                                            ['صباحاً', 'مساءً'],
                                                            $formattedDateTimePrepareShipping,
                                                        );
                                                    @endphp

                                                    <div class="order-step">
                                                        <div
                                                            class="order-icon {{ in_array($Order->maintenanceorder_orderstatus, ['repaired', 'Cancelled']) ? 'completed' : '' }}">
                                                            ✔
                                                        </div>
                                                        <div class="order-title timelineName">تم الإصلاح</div>

                                                        @if (in_array($Order->maintenanceorder_orderstatus, ['repaired', 'Cancelled']))
                                                            <div class="order-date timelinePrice">
                                                                {{ $formattedDateTimePrepareShipping }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="order-step">
                                                        <div class="order-icon">✔</div>
                                                        <div class="order-title timelinePrice">طلبك خارج للتوصيل</div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-center mt-4" style="height:36px; overflow:hidden;">
                                    <nav role="navigation" aria-label="Pagination Navigation"
                                        class="flex items-center justify-between w-100">
                                        <div class="row">
                                            <div class="col-5"></div>
                                            <div class="col-1 m-auto">
                                                <div class="hidden sm:hidden">
                                                    <a href="{{ $MaintenanceOrders->previousPageUrl() }}"
                                                        class="pagination-button decorationnone">
                                                        <button class="btn apponwer_systemprimarybtn">
                                                            <i class="fas fa-chevron-left"></i></button>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-1 m-auto">
                                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                                    <a href="{{ $MaintenanceOrders->nextPageUrl() }}"
                                                        class="pagination-button decorationnone">
                                                        <button class="btn apponwer_systemprimarybtn">
                                                            <i class="fas fa-chevron-right"></i></button>
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
                </div>
            </div>
        </div>

        <div id="mobile_content_ShoppingCart">
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card p-4 text-center">
                        @if ($MaintenanceOrders->isEmpty())
                            <div class="row m-auto">
                                <div class="col-12 d-flex align-items-center justify-content-center text-center m-auto">
                                    <img src="{{ url('/images/socialmediacontacts/emptybasket.png') }}" alt=" سلة التسوق "
                                        class="ShoppingCartimg">
                                </div>
                            </div>
                            <div class="row m-auto">
                                <div
                                    class="col-12 categorytitlehomepagemobile d-flex align-items-center justify-content-center text-center m-auto">
                                    ليس لديك طلبات في صيانة حاليا
                                </div>
                            </div>
                            <div class="row m-auto pt-3">
                                <div
                                    class="col-12 footerfirstdescription d-flex align-items-center justify-content-center text-center m-auto">
                                    {{ $ecommerceSharedData['branch']->branch_name }} مكانك الأول لكل احتياجات
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
                                صيانتي
                            </h5>
                            @foreach ($MaintenanceOrders as $Order)
                                <div class="UserMaintenanceCardMobile">
                                    <p class="text-center fw-bold footerfirstdescription">
                                        رقم الطلب {{ $Order->maintenanceorder_ordernumber }}
                                    </p>
                                    <div class="row w-100">
                                        <div class="col-12">
                                            @php
                                                \Carbon\Carbon::setLocale('ar');
                                                $formattedDateOrderConfirmationTime = \Carbon\Carbon::parse(
                                                    $Order->maintenanceorder_orderreciveddate,
                                                )->translatedFormat('l، j F Y، g:i A');
                                                $formattedDateOrderConfirmationTime = str_replace(
                                                    ['AM', 'PM'],
                                                    ['صباحاً', 'مساءً'],
                                                    $formattedDateOrderConfirmationTime,
                                                );
                                            @endphp
                                            <div class="order-step">
                                                <div class="order-icon completed">✔</div>
                                                <div class="order-title timelineName"> تحت الاصلاح </div>
                                                <div class="order-date timelinePrice">
                                                    {{ $formattedDateOrderConfirmationTime }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row w-100 pt-2">
                                        <div class="col-12">
                                            @php
                                                \Carbon\Carbon::setLocale('ar');
                                                $formattedDateTimePrepareShipping = \Carbon\Carbon::parse(
                                                    $Order->updated_at,
                                                )->translatedFormat('l، j F Y، g:i A');
                                                $formattedDateTimePrepareShipping = str_replace(
                                                    ['AM', 'PM'],
                                                    ['صباحاً', 'مساءً'],
                                                    $formattedDateTimePrepareShipping,
                                                );
                                            @endphp

                                            <div class="order-step">
                                                <div
                                                    class="order-icon {{ in_array($Order->maintenanceorder_orderstatus, ['repaired', 'Cancelled']) ? 'completed' : '' }}">
                                                    ✔
                                                </div>
                                                <div class="order-title timelineName">تم الإصلاح</div>

                                                @if (in_array($Order->maintenanceorder_orderstatus, ['repaired', 'Cancelled']))
                                                    <div class="order-date timelinePrice">
                                                        {{ $formattedDateTimePrepareShipping }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row w-100 pt-2">
                                        <div class="col-12">
                                            <div class="order-step">
                                                <div class="order-icon">✔</div>
                                                <div class="order-title timelinePrice">طلبك خارج للتوصيل</div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex justify-content-center mt-4" style="height:36px; overflow:hidden;">
                                <nav role="navigation" aria-label="Pagination Navigation"
                                    class="flex items-center justify-between w-100">
                                    <div class="row">
                                        <div class="col-4"></div>
                                        <div class="col-2 m-auto">
                                            <div class="hidden sm:hidden">
                                                <a href="{{ $MaintenanceOrders->previousPageUrl() }}"
                                                    class="pagination-button decorationnone">
                                                    <button class="btn apponwer_systemprimarybtn">
                                                        <i class="fas fa-chevron-left"></i></button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-2 m-auto">
                                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                                <a href="{{ $MaintenanceOrders->nextPageUrl() }}"
                                                    class="pagination-button decorationnone">
                                                    <button class="btn apponwer_systemprimarybtn">
                                                        <i class="fas fa-chevron-right"></i></button>
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