{{-- resources/views/ecommerce/Checkout/Checkout.blade.php --}}
@php
    $cartItems = session('cart', []);
    $cartTotal = collect($cartItems)->sum(function($item) {
        $price = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
            ? $item['offer_price'] : $item['price'];
        return $price * ($item['quantity'] ?? 1);
    });
@endphp

@extends('layouts.app')
@section('title', 'إتمام الطلب — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

<x-page-header title="إتمام الطلب" :breadcrumbs="[
    ['name' => 'الرئيسية', 'url' => route('home')],
    ['name' => 'سلة التسوق', 'url' => route('ShoppingCart')],
    ['name' => 'إتمام الطلب', 'url' => route('checkout')],
]" />

<div class="container py-5">
    <div class="row g-4 flex-row-reverse">

        {{-- ── ملخص الطلب (يمين) ── --}}
        <div class="col-lg-5">
            <div class="checkout-summary">
                <h3 class="checkout-summary__title">ملخص الطلب</h3>

                @foreach($cartItems as $i => $item)
                @php
                    $itemPrice = ($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price']
                        ? $item['offer_price'] : $item['price'];
                @endphp
                <div class="checkout-summary__item">
                    <div class="checkout-summary__item-info">
                        <img src="{{ asset('images/productsimages/' . ($item['image'] ?? 'placeholder.png')) }}"
                             alt="{{ $item['name'] ?? '' }}"
                             width="48" height="48"
                             loading="lazy">
                        <div>
                            <p class="checkout-summary__item-name">
                                طلب {{ $i + 1 }}
                            </p>
                            <p class="checkout-summary__item-sub text-muted">
                                {{ Str::limit($item['name'] ?? '', 30) }}
                            </p>
                        </div>
                    </div>
                    <div class="checkout-summary__item-prices">
                        <div class="d-flex justify-content-between">
                            <span>كمية</span>
                            <span>{{ $item['quantity'] ?? 1 }} منتج</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>سعر المنتج</span>
                            <span>{{ number_format($itemPrice) }} جنية</span>
                        </div>
                        @if(($item['offer_price'] ?? 0) > 0 && $item['offer_price'] < $item['price'])
                        <div class="d-flex justify-content-between text-danger">
                            <span>الخصم</span>
                            <span>{{ number_format($item['price'] - $item['offer_price']) }} جنية</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold">
                            <span>إجمالي السعر</span>
                            <span>{{ number_format($itemPrice * ($item['quantity'] ?? 1)) }} جنية</span>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="checkout-summary__total">
                    <span>إجمالي الطلب</span>
                    <span>{{ number_format($cartTotal) }} جنية</span>
                </div>

                @if(session('customer_phone'))
                <form method="POST" action="{{ route('checkout.confirm') }}" id="checkoutForm">
                    @csrf
                    <button type="submit" class="btn hero__btn w-100 mt-3">
                        تأكيد الطلب
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- ── البيانات الأساسية (يسار) ── --}}
        <div class="col-lg-7">
            <div class="checkout-steps">

                {{-- Step 1: البيانات الأساسية --}}
                <div class="checkout-step">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num">1</span>
                        <h3 class="checkout-step__title">البيانات الأساسية</h3>
                    </div>

                    <div class="checkout-step__body">
                        @if(!session('customer_phone'))
                        <div class="checkout-login-prompt">
                            <div class="d-flex align-items-start gap-2 mb-3 p-3"
                                 style="background:var(--bg-secondary);border-radius:var(--radius-md)">
                                <i class="bi bi-info-circle text-primary mt-1" aria-hidden="true"></i>
                                <p class="mb-0 text-muted" style="font-size:.88rem">
                                    وجود حساب خاص بك ضروري لمتابعة تأكيد الطلب. وبمقدورك من ثم اتباع طلباتك أونلاين.
                                </p>
                            </div>
                            <a href="{{ route('CustomerLogin') }}"
                               class="btn hero__btn w-100">
                                تسجيل الدخول
                            </a>
                            <p class="text-center mt-2" style="font-size:.85rem">
                                مستخدم جديد؟
                                <a href="{{ route('NewCustomer') }}" class="text-primary">إنشاء حساب</a>
                            </p>
                        </div>
                        @else
                        <div class="p-3"
                             style="background:var(--bg-secondary);border-radius:var(--radius-md)">
                            <p class="mb-0">
                                <i class="bi bi-person-check-fill text-success me-2" aria-hidden="true"></i>
                                مرحباً، <strong>{{ session('customer_name') }}</strong>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Step 2: تفاصيل التسليم --}}
                <div class="checkout-step checkout-step--disabled">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num">2</span>
                        <h3 class="checkout-step__title">تفاصيل التسليم</h3>
                    </div>
                </div>

                {{-- Step 3: تفاصيل السداد --}}
                <div class="checkout-step checkout-step--disabled">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num">3</span>
                        <h3 class="checkout-step__title">تفاصيل السداد</h3>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection