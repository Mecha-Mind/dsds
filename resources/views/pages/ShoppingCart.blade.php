{{-- resources/views/ShoppingCart/index.blade.php --}}
@extends('layouts.app')

@section('title', 'سلة التسوق — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

<x-page-header
    title="سلة التسوق"
    :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'سلة التسوق', 'url' => route('cart')],
    ]"
/>

<div class="container py-4">

    @if(isset($CartItems) && count($CartItems) > 0)

    {{-- ════════════ السلة فيها منتجات ════════════ --}}
    <div class="row g-4">

        {{-- المنتجات --}}
        <div class="col-lg-8">

            {{-- عدد المنتجات --}}
            <div class="cart-header mb-3">
                <span class="fw-semibold">عدد المنتجات: {{ count($CartItems) }}</span>
            </div>

            {{-- قائمة المنتجات --}}
            <div class="d-flex flex-column gap-3">
                @foreach($CartItems as $item)
                <div class="cart-item">

                    {{-- الصورة --}}
                    <a href="{{ route('ProductDetails', $item->customerrequestproduct_productname) }}"
                       class="cart-item__img-wrap">
                        <img src="{{ asset('images/products/' . ($item->product?->product_image ?? 'placeholder.png')) }}"
                             alt="{{ $item->customerrequestproduct_productname }}"
                             loading="lazy">
                    </a>

                    {{-- البيانات --}}
                    <div class="cart-item__info">
                        <h3 class="cart-item__name">
                            {{ $item->customerrequestproduct_productname }}
                        </h3>
                        <div class="cart-item__price">
                            {{ number_format($item->customerrequestproduct_productsellprice ?? 0) }} ج.م
                        </div>
                    </div>

                    {{-- الكمية --}}
                    <div class="cart-item__qty">
                        <form method="POST" action="{{ route('CustomerRequestDecreaseQuantityPost', $item->id) }}" class="d-inline">
                            @csrf
                            <button class="qty-btn" type="submit">
                                <i class="bi bi-dash"></i>
                            </button>
                        </form>

                        <span class="qty-display">{{ $item->customerrequestproduct_productquantity }}</span>

                        <form method="POST" action="{{ route('CustomerRequestIncreseQuantityPost', $item->id) }}" class="d-inline">
                            @csrf
                            <button class="qty-btn" type="submit">
                                <i class="bi bi-plus"></i>
                            </button>
                        </form>
                    </div>

                    {{-- المجموع + حذف --}}
                    <div class="cart-item__total">
                        <span>{{ number_format(($item->customerrequestproduct_productsellprice ?? 0) * ($item->customerrequestproduct_productquantity ?? 1)) }} ج.م</span>
                        <form method="POST" action="{{ route('CustomerRequestDeletePost', $item->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0 ms-2" title="حذف">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>

        </div>

        {{-- ملخص الطلب --}}
        <div class="col-lg-4">
            <div class="cart-summary">
                <h3 class="cart-summary__title">إجمالي الطلب</h3>

                <div class="cart-summary__row">
                    <span>المجموع</span>
                    <span>{{ number_format($subtotal ?? 0) }} ج.م</span>
                </div>
                <div class="cart-summary__row">
                    <span>الشحن</span>
                    <span class="text-success">مجاناً</span>
                </div>
                <div class="cart-summary__row cart-summary__total">
                    <span>الإجمالي</span>
                    <span>{{ number_format($subtotal ?? 0) }} ج.م</span>
                </div>

                <button class="btn hero__btn w-100 mt-3">
                    متابعة الشراء
                </button>

                <a href="{{ route('EcommerceAllProducts') }}" class="btn btn-outline-secondary w-100 mt-2">
                    متابعة التسوق
                </a>
            </div>
        </div>

    </div>

    {{-- منتجات ممكن تحبها --}}
    @if(isset($SuggestedProducts) && $SuggestedProducts->count())
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('EcommerceAllProducts') }}" class="section-more-link">
                <i class="bi bi-arrow-right"></i>
                عرض المزيد
            </a>
            <h2 class="section-title mb-0">منتجات قد تحبها</h2>
        </div>

        {{-- Horizontal Scroll على الموبايل --}}
        <div class="suggested-products-scroll">
            @foreach($SuggestedProducts as $ep)
            <div class="suggested-product-item">
                <x-product-card
                    :id="$ep->ecommerceproduct_id"
                    :name="$ep->product?->product_name ?? ''"
                    :price="$ep->product?->product_sellprice ?? 0"
                    :offer-price="$ep->product?->product_offerprice ?? null"
                    :image="$ep->product?->product_image ?? ''"
                    route="ProductDetails"
                    :has-offer="false"
                />
            </div>
            @endforeach
        </div>

        {{-- Pagination منتجات السلة --}}
        @if(isset($SuggestedProducts) && method_exists($SuggestedProducts, 'hasPages') && $SuggestedProducts->hasPages())
        <div class="d-flex justify-content-center gap-2 mt-3">
            <a href="{{ $SuggestedProducts->previousPageUrl() ?? '#' }}" class="pagination-btn">
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ $SuggestedProducts->nextPageUrl() ?? '#' }}" class="pagination-btn">
                <i class="bi bi-chevron-left"></i>
            </a>
        </div>
        @endif
    </div>
    @endif

    @else

    {{-- ════════════ السلة فاضية ════════════ --}}
    <div class="cart-empty text-center py-5">
        <i class="bi bi-basket" style="font-size: 4rem; color: var(--text); opacity: .4"></i>
        <h3 class="mt-3">سلة التسوق الخاصة بك فارغة</h3>
        <p class="text-muted mb-4">
            اضغط على زر اضافة الى السلة لإضافة منتجاتك المفضلة
        </p>
        <!-- EcommerceAllProducts : route -->
        <a href="{{ route('allproducts') }}" class="btn hero__btn">
            تسوق الآن
        </a>
    </div>

    {{-- منتجات مقترحة حتى لو السلة فاضية --}}
    @if(isset($SuggestedProducts) && $SuggestedProducts->count())
    <div class="mt-4">
        <h2 class="section-title text-end mb-4">منتجات شاهدها مؤخراً</h2>
        <div class="products-grid">
            @foreach($SuggestedProducts as $ep)
            <x-product-card
                :id="$ep->ecommerceproduct_id"
                :name="$ep->product?->product_name ?? ''"
                :price="$ep->product?->product_sellprice ?? 0"
                :offer-price="null"
                :image="$ep->product?->product_image ?? ''"
                route="ProductDetails"
                :has-offer="false"
            />
            @endforeach
        </div>
    </div>
    @endif

    @endif

</div>

@endsection