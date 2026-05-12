1. CheckoutController الكامل
php<?php

namespace App\Http\Controllers\Ecommerce\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerRequestProduct;
use App\Models\EcommerceProduct;
use App\Models\Branche;
use App\Services\EcommerceSharedDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // ══════════════════════════════════════════════
    // عرض صفحة الـ Checkout
    // محتاج تسجيل دخول
    // ══════════════════════════════════════════════
    public function index()
    {
        // لازم يكون مسجل دخول
        $customerPhone = session('customer_phone');
        if (!$customerPhone) {
            return redirect()->route('CustomerLogin')
                ->with('intended', route('checkout'));
        }

        $customer = Customer::where('customer_phone', $customerPhone)
            ->where('customer_delete', 0)
            ->where('customer_block', 0)
            ->first();

        if (!$customer) {
            return redirect()->route('CustomerLogin');
        }

        $ecommerceSharedData = EcommerceSharedDataService::get();
        $ecommerceSharedData['pageTitle'] = 'إتمام الطلب';

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('ShoppingCart')
                ->with('error', 'سلتك فارغة، أضف منتجات أولاً');
        }

        // حساب الإجمالي
        $cartTotal = $this->calculateTotal($cart);

        // جلب الفروع للاستلام
        $branches = Branche::where('branch_delete', 0)
            ->where('branch_services', 0)
            ->get();

        // جلب البطاقات المحفوظة
        $savedCards = session('saved_cards', []);

        return view('ecommerce.Checkout.Checkout', compact(
            'ecommerceSharedData',
            'cart',
            'cartTotal',
            'customer',
            'branches',
            'savedCards'
        ));
    }

    // ══════════════════════════════════════════════
    // تأكيد الطلب — تحويل السلة للـ DB
    // ══════════════════════════════════════════════
    public function confirm(Request $request)
    {
        $customerPhone = session('customer_phone');
        if (!$customerPhone) {
            return response()->json(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401);
        }

        $customer = Customer::where('customer_phone', $customerPhone)
            ->where('customer_delete', 0)
            ->where('customer_block', 0)
            ->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'حساب غير صالح'], 401);
        }

        // Validation
        $validated = $request->validate([
            'delivery_type'  => ['required', 'string', 'in:home,branch'],
            'payment_method' => ['required', 'string', 'in:cash,card'],
            // لو home delivery
            'city'           => ['required_if:delivery_type,home', 'nullable', 'string', 'max:100'],
            'district'       => ['required_if:delivery_type,home', 'nullable', 'string', 'max:100'],
            'address'        => ['required_if:delivery_type,home', 'nullable', 'string', 'max:500'],
            'floor'          => ['nullable', 'string', 'max:50'],
            // لو branch
            'branch_id'      => ['required_if:delivery_type,branch', 'nullable', 'integer'],
            // لو card payment
            'card_number'    => ['required_if:payment_method,card', 'nullable', 'string', 'max:19'],
            'card_expiry'    => ['required_if:payment_method,card', 'nullable', 'string', 'max:5'],
            'card_name'      => ['required_if:payment_method,card', 'nullable', 'string', 'max:100'],
            'save_card'      => ['nullable', 'boolean'],
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'السلة فارغة'], 422);
        }

        try {
            DB::transaction(function () use ($request, $customer, $cart, $validated) {

                $now = now()->toDateTimeString();
                $deliveryFee = $validated['delivery_type'] === 'home' ? 100 : 0;

                foreach ($cart as $id => $item) {
                    $ep = EcommerceProduct::with('product')
                        ->where('ecommerceproduct_id', $id)
                        ->where('ecommerceproduct_displaystatus', 1)
                        ->first();

                    if (!$ep || !$ep->product) continue;

                    $product   = $ep->product;
                    $paidPrice = $ep->ecommerceproduct_appearinthelistofoffers == 1
                        ? $product->product_offerprice
                        : $product->product_sellprice;

                    $qty   = $item['quantity'] ?? 1;
                    $total = $paidPrice * $qty;

                    CustomerRequestProduct::create([
                        'customerrequestproduct_customeraccount'           => $customer->customer_account,
                        'customerrequestproduct_delete'                    => 0,
                        'customerrequestproduct_billstatus'                => 0,
                        'customerrequestproduct_billreference'             => null,
                        'customerrequestproduct_preparedbilldatetime'      => null,
                        'customerrequestproduct_productname'               => $product->product_id,
                        'customerrequestproduct_productstockavailability'  => 0,
                        'customerrequestproduct_productquantity'           => $qty,
                        'customerrequestproduct_productbuyprice'           => $product->product_buyprice,
                        'customerrequestproduct_productwholesaleprice'     => $product->product_wholesaleprice,
                        'customerrequestproduct_productofferprice'         => $product->product_offerprice,
                        'customerrequestproduct_productsellprice'          => $product->product_sellprice,
                        'customerrequestproduct_productpaidprice'          => $paidPrice,
                        'customerrequestproduct_producttotalquantityprice' => $total,
                        'customerrequestproduct_requestdatetime'           => $now,
                        'user_name' => 'Customer_' . $customer->customer_phone,
                    ]);
                }

                // حفظ البطاقة لو اختار
                if (
                    $validated['payment_method'] === 'card' &&
                    !empty($validated['save_card']) &&
                    !empty($validated['card_number'])
                ) {
                    $cards   = session('saved_cards', []);
                    $last4   = substr(preg_replace('/\D/', '', $validated['card_number']), -4);
                    $cards[] = [
                        'last4'  => $last4,
                        'expiry' => $validated['card_expiry'] ?? '',
                        'name'   => $validated['card_name'] ?? '',
                    ];
                    session(['saved_cards' => $cards]);
                }

                // تفاصيل للـ session عشان صفحة التأكيد
                session([
                    'last_order' => [
                        'delivery_type'  => $validated['delivery_type'],
                        'payment_method' => $validated['payment_method'],
                        'city'           => $validated['city'] ?? null,
                        'district'       => $validated['district'] ?? null,
                        'address'        => $validated['address'] ?? null,
                        'branch_id'      => $validated['branch_id'] ?? null,
                        'delivery_fee'   => $deliveryFee,
                        'order_number'   => rand(1000000000, 9999999999),
                        'customer_name'  => $customer->customer_name,
                        'customer_phone' => $customer->customer_phone,
                    ]
                ]);
            });

            // مسح السلة
            session()->forget('cart');

            return response()->json([
                'success'  => true,
                'redirect' => route('checkout.success'),
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout confirm error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ، حاول مرة أخرى',
            ], 500);
        }
    }

    // ══════════════════════════════════════════════
    // صفحة النجاح بعد الطلب
    // ══════════════════════════════════════════════
    public function success()
    {
        $lastOrder = session('last_order');
        if (!$lastOrder) {
            return redirect()->route('home');
        }

        $ecommerceSharedData = EcommerceSharedDataService::get();

        // لو استلام من فرع — جيب بيانات الفرع
        $branch = null;
        if ($lastOrder['delivery_type'] === 'branch' && $lastOrder['branch_id']) {
            $branch = Branche::find($lastOrder['branch_id']);
        }

        return view('ecommerce.Checkout.CheckoutSuccess', compact(
            'ecommerceSharedData',
            'lastOrder',
            'branch'
        ));
    }

    // ══════════════════════════════════════════════
    // Helper — حساب الإجمالي
    // ══════════════════════════════════════════════
    private function calculateTotal(array $cart): float
    {
        return collect($cart)->sum(function ($item) {
            $price      = (float) ($item['price']       ?? 0);
            $offerPrice = (float) ($item['offer_price'] ?? 0);
            $finalPrice = ($offerPrice > 0 && $offerPrice < $price) ? $offerPrice : $price;
            return $finalPrice * ($item['quantity'] ?? 1);
        });
    }
}

2. Routes الـ Checkout
php// في routes/web.php
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])
    ->name('checkout.confirm')
    ->middleware('throttle:5,1');
Route::get('/checkout/success', [CheckoutController::class, 'success'])
    ->name('checkout.success');

3. Checkout.blade.php الكامل
blade{{-- resources/views/ecommerce/Checkout/Checkout.blade.php --}}
@extends('layouts.app')
@section('title', 'إتمام الطلب — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

{{-- Checkout Layout --}}
<div class="checkout-page">

    {{-- ── Header ── --}}
    <header class="checkout-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lock-fill" style="color:var(--primary)"></i>
                <span style="font-size:.85rem;color:var(--text)">نظام دفع آمن</span>
            </div>
            <a href="{{ route('home') }}" class="checkout-header__logo">
                <img src="{{ asset($ecommerceSharedData['logo']) }}"
                     alt="{{ $ecommerceSharedData['branchName'] ?? '' }}"
                     height="40" loading="eager">
            </a>
            <div class="checkout-header__cart">
                <i class="bi bi-bag" style="font-size:1.2rem"></i>
                <span class="cart-badge d-inline-flex"
                      style="background:var(--heading);color:#fff;border-radius:50%;width:20px;height:20px;align-items:center;justify-content:center;font-size:.75rem">
                    {{ count($cart) }}
                </span>
            </div>
        </div>
    </header>

    <div class="container py-4">
        <div class="row g-4 flex-row-reverse">

            {{-- ── ملخص الطلب (يمين) ── --}}
            <div class="col-lg-5">
                <div class="checkout-summary" id="orderSummary">
                    <h3 class="checkout-summary__title">ملخص الطلب</h3>

                    @php $deliveryFee = 0; @endphp

                    @foreach($cart as $id => $item)
                    @php
                        $p      = (float)($item['price'] ?? 0);
                        $op     = (float)($item['offer_price'] ?? 0);
                        $fp     = ($op > 0 && $op < $p) ? $op : $p;
                        $qty    = $item['quantity'] ?? 1;
                        $iTotal = $fp * $qty;
                    @endphp
                    <div class="checkout-summary__item">
                        <div class="checkout-summary__item-info">
                            <img src="{{ asset('images/productsimages/' . ($item['image'] ?? 'placeholder.png')) }}"
                                 alt="{{ $item['name'] ?? '' }}"
                                 width="48" height="48" loading="lazy">
                            <div class="flex-grow-1 text-end">
                                <p class="checkout-summary__item-name">
                                    {{ Str::limit($item['name'] ?? '', 35) }}
                                </p>
                                <div class="d-flex justify-content-between mt-1">
                                    <span style="font-size:.82rem;color:var(--text)">{{ $qty }} × {{ number_format($fp) }}</span>
                                    <strong>{{ number_format($iTotal) }} ج.م</strong>
                                </div>
                                @if($op > 0 && $op < $p)
                                <span style="font-size:.75rem;color:var(--red)">
                                    خصم {{ number_format($p - $op) }} ج.م
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="checkout-summary__fees">
                        <div class="d-flex justify-content-between py-2"
                             style="border-top:1px solid var(--stroke);font-size:.88rem">
                            <span>المنتجات</span>
                            <span>{{ number_format($cartTotal) }} ج.م</span>
                        </div>
                        <div class="d-flex justify-content-between py-2"
                             style="border-top:1px solid var(--stroke);font-size:.88rem"
                             id="deliveryFeeRow">
                            <span>رسوم التوصيل</span>
                            <span id="deliveryFeeVal">0 ج.م</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 fw-bold"
                             style="border-top:2px solid var(--stroke);font-size:1rem">
                            <span>إجمالي الطلب</span>
                            <span id="grandTotal">{{ number_format($cartTotal) }} EGP</span>
                        </div>
                    </div>

                    {{-- زرار التأكيد --}}
                    <button type="button"
                            id="confirmOrderBtn"
                            class="btn hero__btn w-100 mt-3"
                            disabled>
                        تأكيد الطلب
                    </button>

                    <div class="mt-3 text-center" style="font-size:.78rem;color:var(--text)">
                        <i class="bi bi-shield-check me-1" style="color:var(--primary)"></i>
                        بياناتك محمية ومشفرة بالكامل
                    </div>
                </div>
            </div>

            {{-- ── خطوات الـ Checkout (يسار) ── --}}
            <div class="col-lg-7">

                {{-- Step 1: البيانات الأساسية ── مكتمل ── --}}
                <div class="checkout-step checkout-step--done">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num checkout-step__num--done">
                            <i class="bi bi-check" aria-hidden="true"></i>
                        </span>
                        <div>
                            <h3 class="checkout-step__title">البيانات الأساسية</h3>
                            <p class="checkout-step__subtitle">
                                {{ $customer->customer_name }}
                                · {{ $customer->customer_phone }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Step 2: تفاصيل التسليم --}}
                <div class="checkout-step" id="deliveryStep">
                    <div class="checkout-step__header" id="deliveryHeader" role="button">
                        <span class="checkout-step__num" id="deliveryStepNum">2</span>
                        <h3 class="checkout-step__title">تفاصيل التسليم</h3>
                    </div>

                    <div class="checkout-step__body" id="deliveryBody">

                        {{-- اختيار طريقة التوصيل --}}
                        <p class="mb-2 fw-semibold" style="font-size:.9rem">إختيار طريقة التوصيل</p>
                        <div class="delivery-type-btns mb-4">
                            <button type="button"
                                    class="delivery-type-btn"
                                    data-type="branch"
                                    id="btnBranch">
                                الإستلام من الفرع
                            </button>
                            <button type="button"
                                    class="delivery-type-btn delivery-type-btn--active"
                                    data-type="home"
                                    id="btnHome">
                                التوصيل لحد البيت
                            </button>
                        </div>

                        {{-- Home Delivery Fields --}}
                        <div id="homeFields">
                            <p class="mb-2 fw-semibold" style="font-size:.9rem">منطقة التوصيل</p>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <select class="checkout-input" id="citySelect" name="city">
                                        <option value="">إختار المدينة</option>
                                        <option value="القاهرة">القاهرة</option>
                                        <option value="الجيزة">الجيزة</option>
                                        <option value="الإسكندرية">الإسكندرية</option>
                                        <option value="الإسماعيلية">الإسماعيلية</option>
                                        <option value="السويس">السويس</option>
                                        <option value="بورسعيد">بورسعيد</option>
                                        {{-- ← أضف المحافظات من الـ DB لو موجودة --}}
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="checkout-input" id="districtSelect" name="district">
                                        <option value="">إختار المنطقة</option>
                                        {{-- بيتعبى بـ AJAX بعد اختيار المدينة --}}
                                    </select>
                                </div>
                            </div>

                            <p class="mb-2 fw-semibold" style="font-size:.9rem">اختيار طريقة التوصيل</p>
                            <div class="delivery-option-card mb-3" id="homeDeliveryCard">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-semibold" style="font-size:.88rem">التوصيل لحد البيت</p>
                                        <p class="mb-0" style="font-size:.78rem;color:var(--text)">
                                            يصل خلال الخميس، 5 ديسمبر
                                        </p>
                                    </div>
                                    <span class="delivery-fee-badge">رسوم التوصيل 100 جنية</span>
                                </div>
                            </div>

                            <p class="mb-2 fw-semibold" style="font-size:.9rem">عنوان التوصيل</p>

                            <div class="mb-2">
                                <div class="checkout-info-row">
                                    <span>{{ $customer->customer_name }}</span>
                                </div>
                                <div class="checkout-info-row">
                                    <span>{{ $customer->customer_phone }}</span>
                                </div>
                            </div>

                            <div class="checkout-note mb-2">
                                <i class="bi bi-info-circle me-1" style="color:var(--primary)"></i>
                                يجب إستلام الطلب لذلك تأكد من حمل البطاقة الشخصية.
                            </div>

                            <input type="text"
                                   class="checkout-input mb-2"
                                   id="addressStreet"
                                   placeholder="رقم المبنى و إسم الشارع (مثال: 55 كورنيش النيل)">

                            <input type="text"
                                   class="checkout-input mb-3"
                                   id="addressFloor"
                                   placeholder="رقم الدور والشقة (مثال: الدور السادس شقة 10)">

                            <button type="button"
                                    class="btn hero__btn w-100"
                                    id="continueToPayment">
                                متابعة
                            </button>
                        </div>

                        {{-- Branch Pickup Fields --}}
                        <div id="branchFields" class="d-none">
                            <p class="mb-2 fw-semibold" style="font-size:.9rem">اختر الفرع</p>
                            @foreach($branches as $branch)
                            <div class="branch-option" data-branch-id="{{ $branch->branch_id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-semibold" style="font-size:.88rem">
                                            {{ $branch->branch_name }}
                                        </p>
                                        <p class="mb-0" style="font-size:.78rem;color:var(--text)">
                                            {{ $branch->branch_place }}
                                        </p>
                                    </div>
                                    <span class="delivery-fee-badge" style="background:var(--bg-secondary);color:var(--heading)">
                                        مجاناً
                                    </span>
                                </div>
                                <p class="mb-0 mt-1" style="font-size:.75rem;color:var(--text)">
                                    تاريخ الإستلام: {{ now()->addDays(2)->format('d/m/Y') }}
                                </p>
                            </div>
                            @endforeach

                            <div class="checkout-note mb-3">
                                <i class="bi bi-info-circle me-1" style="color:var(--primary)"></i>
                                يجب إستلام الطلب بنفسك لذلك تأكد من حمل البطاقة الشخصية.
                            </div>

                            <button type="button"
                                    class="btn hero__btn w-100"
                                    id="continueToPaymentBranch">
                                متابعة
                            </button>
                        </div>

                    </div>
                </div>

                {{-- Step 3: تفاصيل السداد --}}
                <div class="checkout-step checkout-step--disabled" id="paymentStep">
                    <div class="checkout-step__header">
                        <span class="checkout-step__num" id="paymentStepNum">3</span>
                        <h3 class="checkout-step__title">تفاصيل السداد</h3>
                    </div>

                    <div class="checkout-step__body d-none" id="paymentBody">

                        {{-- طرق الدفع --}}
                        <div class="payment-methods mb-4">
                            <label class="payment-method-option payment-method-option--active" id="cashOption">
                                <input type="radio" name="payment_method" value="cash" checked class="d-none">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-cash-coin fs-5" style="color:var(--primary)"></i>
                                    <span>كاش عند الإستلام</span>
                                </div>
                            </label>

                            <label class="payment-method-option" id="cardOption">
                                <input type="radio" name="payment_method" value="card" class="d-none">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-credit-card fs-5" style="color:var(--primary)"></i>
                                    <span>أونلاين بطاقة الدفع</span>
                                </div>
                            </label>
                        </div>

                        {{-- Card Fields --}}
                        <div id="cardFields" class="d-none">
                            <div class="mb-2">
                                <input type="text"
                                       class="checkout-input"
                                       id="cardNumber"
                                       placeholder="0000 0000 0000 0000"
                                       maxlength="19"
                                       inputmode="numeric">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-4">
                                    <input type="text"
                                           class="checkout-input"
                                           id="cardExpiry"
                                           placeholder="MM/YY"
                                           maxlength="5">
                                </div>
                                <div class="col-4">
                                    <input type="text"
                                           class="checkout-input"
                                           id="cardCvv"
                                           placeholder="رقم الكود الموجود خلف بطاقة الإئتمان ..."
                                           maxlength="4"
                                           inputmode="numeric">
                                </div>
                                <div class="col-4">
                                    <input type="text"
                                           class="checkout-input"
                                           id="cardName"
                                           placeholder="الإسم (كما في البطاقة)">
                                </div>
                            </div>
                            <label class="d-flex align-items-center gap-2 mb-3" style="font-size:.85rem;cursor:pointer">
                                <input type="checkbox" id="saveCard">
                                حفظ البطاقة
                            </label>
                        </div>

                        {{-- البطاقات المحفوظة --}}
                        @if(!empty($savedCards))
                        <div id="savedCardsSection" class="mb-3">
                            <p class="fw-semibold mb-2" style="font-size:.88rem">بطاقاتك المحفوظة</p>
                            @foreach($savedCards as $i => $card)
                            <label class="saved-card-option" data-card-index="{{ $i }}">
                                <input type="radio" name="saved_card" value="{{ $i }}" class="d-none">
                                <i class="bi bi-credit-card me-2" style="color:var(--primary)"></i>
                                **** **** **** {{ $card['last4'] }}
                                · {{ $card['expiry'] }}
                            </label>
                            @endforeach
                        </div>
                        @endif

                        <button type="button"
                                class="btn hero__btn w-100"
                                id="continueToConfirm">
                            متابعة
                        </button>

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── State ──────────────────────────────────────
    const state = {
        deliveryType:  'home',   // 'home' | 'branch'
        paymentMethod: 'cash',   // 'cash' | 'card'
        selectedBranch: null,
        city: '',
        district: '',
        street: '',
        floor: '',
        cardNumber: '',
        cardExpiry: '',
        cardCvv: '',
        cardName: '',
        saveCard: false,
    };

    const DELIVERY_FEE = 100;
    const BASE_TOTAL   = {{ $cartTotal }};

    // ── Elements ───────────────────────────────────
    const btnHome      = document.getElementById('btnHome');
    const btnBranch    = document.getElementById('btnBranch');
    const homeFields   = document.getElementById('homeFields');
    const branchFields = document.getElementById('branchFields');
    const deliveryFeeVal   = document.getElementById('deliveryFeeVal');
    const grandTotal       = document.getElementById('grandTotal');
    const deliveryFeeRow   = document.getElementById('deliveryFeeRow');
    const confirmOrderBtn  = document.getElementById('confirmOrderBtn');
    const paymentStep      = document.getElementById('paymentStep');
    const paymentBody      = document.getElementById('paymentBody');
    const paymentStepNum   = document.getElementById('paymentStepNum');
    const deliveryStepNum  = document.getElementById('deliveryStepNum');
    const deliveryHeader   = document.getElementById('deliveryHeader');
    const cashOption       = document.getElementById('cashOption');
    const cardOption       = document.getElementById('cardOption');
    const cardFields       = document.getElementById('cardFields');

    // ── Delivery Type Toggle ───────────────────────
    function setDeliveryType(type) {
        state.deliveryType = type;

        if (type === 'home') {
            btnHome.classList.add('delivery-type-btn--active');
            btnBranch.classList.remove('delivery-type-btn--active');
            homeFields.classList.remove('d-none');
            branchFields.classList.add('d-none');
            updateTotal(DELIVERY_FEE);
        } else {
            btnBranch.classList.add('delivery-type-btn--active');
            btnHome.classList.remove('delivery-type-btn--active');
            branchFields.classList.remove('d-none');
            homeFields.classList.add('d-none');
            updateTotal(0);
        }
    }

    btnHome?.addEventListener('click', () => setDeliveryType('home'));
    btnBranch?.addEventListener('click', () => setDeliveryType('branch'));

    // ── Update Total ───────────────────────────────
    function updateTotal(fee) {
        const total = BASE_TOTAL + fee;
        if (deliveryFeeVal) deliveryFeeVal.textContent = fee > 0 ? fee.toLocaleString('ar-EG') + ' ج.م' : 'مجاناً';
        if (grandTotal)     grandTotal.textContent     = total.toLocaleString('ar-EG') + ' EGP';
    }

    // ── Branch Selection ───────────────────────────
    document.querySelectorAll('.branch-option').forEach(el => {
        el.addEventListener('click', function () {
            document.querySelectorAll('.branch-option').forEach(b => b.classList.remove('is-selected'));
            this.classList.add('is-selected');
            state.selectedBranch = this.dataset.branchId;
        });
    });

    // ── Continue to Payment (Home) ─────────────────
    document.getElementById('continueToPayment')?.addEventListener('click', function () {
        state.city     = document.getElementById('citySelect')?.value ?? '';
        state.district = document.getElementById('districtSelect')?.value ?? '';
        state.street   = document.getElementById('addressStreet')?.value ?? '';
        state.floor    = document.getElementById('addressFloor')?.value ?? '';

        if (!state.city) {
            alert('اختر المدينة أولاً');
            return;
        }

        markDeliveryDone();
        openPayment();
    });

    // ── Continue to Payment (Branch) ──────────────
    document.getElementById('continueToPaymentBranch')?.addEventListener('click', function () {
        if (!state.selectedBranch) {
            alert('اختر فرعاً أولاً');
            return;
        }

        markDeliveryDone();
        openPayment();
    });

    function markDeliveryDone() {
        if (deliveryStepNum) {
            deliveryStepNum.innerHTML = '<i class="bi bi-check"></i>';
            deliveryStepNum.classList.add('checkout-step__num--done');
        }
        document.getElementById('deliveryStep')?.classList.add('checkout-step--done');
        document.getElementById('deliveryBody')?.classList.add('d-none');
    }

    function openPayment() {
        paymentStep?.classList.remove('checkout-step--disabled');
        paymentBody?.classList.remove('d-none');
        paymentStep?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── Payment Method Toggle ─────────────────────
    cashOption?.addEventListener('click', function () {
        state.paymentMethod = 'cash';
        cashOption.classList.add('payment-method-option--active');
        cardOption?.classList.remove('payment-method-option--active');
        cardFields?.classList.add('d-none');
    });

    cardOption?.addEventListener('click', function () {
        state.paymentMethod = 'card';
        cardOption.classList.add('payment-method-option--active');
        cashOption?.classList.remove('payment-method-option--active');
        cardFields?.classList.remove('d-none');
    });

    // ── Card Number Formatting ────────────────────
    document.getElementById('cardNumber')?.addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '').slice(0, 16);
        this.value = val.replace(/(.{4})/g, '$1 ').trim();
        state.cardNumber = val;
    });

    document.getElementById('cardExpiry')?.addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '').slice(0, 4);
        if (val.length > 2) val = val.slice(0, 2) + '/' + val.slice(2);
        this.value = val;
        state.cardExpiry = val;
    });

    document.getElementById('cardCvv')?.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
        state.cardCvv = this.value;
    });

    // ── Continue to Confirm ───────────────────────
    document.getElementById('continueToConfirm')?.addEventListener('click', function () {
        if (state.paymentMethod === 'card') {
            if (!state.cardNumber || state.cardNumber.length < 16) {
                alert('أدخل رقم البطاقة كاملاً');
                return;
            }
            if (!state.cardExpiry) {
                alert('أدخل تاريخ انتهاء البطاقة');
                return;
            }
        }

        // Mark payment done
        if (paymentStepNum) {
            paymentStepNum.innerHTML = '<i class="bi bi-check"></i>';
            paymentStepNum.classList.add('checkout-step__num--done');
        }
        paymentStep?.classList.add('checkout-step--done');
        paymentBody?.classList.add('d-none');

        // تفعيل زرار التأكيد
        if (confirmOrderBtn) confirmOrderBtn.disabled = false;
        confirmOrderBtn?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // ── Confirm Order ─────────────────────────────
    confirmOrderBtn?.addEventListener('click', function () {
        this.disabled = true;
        this.textContent = 'جاري الإرسال...';

        const payload = {
            delivery_type:  state.deliveryType,
            payment_method: state.paymentMethod,
            city:           state.city,
            district:       state.district,
            address:        state.street,
            floor:          state.floor,
            branch_id:      state.selectedBranch,
        };

        if (state.paymentMethod === 'card') {
            payload.card_number = state.cardNumber;
            payload.card_expiry = state.cardExpiry;
            payload.card_name   = document.getElementById('cardName')?.value ?? '';
            payload.save_card   = document.getElementById('saveCard')?.checked ?? false;
        }

        fetch('{{ route("checkout.confirm") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type':  'application/json',
                'Accept':        'application/json',
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message ?? 'حدث خطأ');
                this.disabled = false;
                this.textContent = 'تأكيد الطلب';
            }
        })
        .catch(() => {
            alert('حدث خطأ في الاتصال');
            this.disabled = false;
            this.textContent = 'تأكيد الطلب';
        });
    });

    // ── City → District (مثال) ────────────────────
    // ← لو عندك districts من DB ضيف AJAX هنا
    document.getElementById('citySelect')?.addEventListener('change', function () {
        state.city = this.value;
        const districtSelect = document.getElementById('districtSelect');
        if (!districtSelect) return;

        // Placeholder — استبدل بـ AJAX لو عندك districts في الـ DB
        const districts = {
            'القاهرة':       ['مدينة نصر', 'المعادي', 'مصر الجديدة', 'النزهة', 'شبرا'],
            'الجيزة':        ['الدقي', 'المهندسين', '6 أكتوبر', 'الشيخ زايد'],
            'الإسماعيلية':  ['حي أول', 'حي ثاني', 'حي ثالث'],
            'الإسكندرية':   ['سموحة', 'المنتزه', 'محرم بك', 'بحري'],
        };

        districtSelect.innerHTML = '<option value="">إختار المنطقة</option>';
        (districts[this.value] ?? []).forEach(d => {
            const opt = document.createElement('option');
            opt.value = d;
            opt.textContent = d;
            districtSelect.appendChild(opt);
        });
    });

})();
</script>
@endpush

4. CheckoutSuccess.blade.php
blade{{-- resources/views/ecommerce/Checkout/CheckoutSuccess.blade.php --}}
@extends('layouts.app')
@section('title', 'تم استلام طلبك — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="container py-5" style="max-width:580px">
    <div class="text-center mb-4">
        <img src="{{ asset('images/ecommerce/success-gift.webp') }}"
             alt="تم الطلب" width="120" height="120"
             onerror="this.style.display='none'"
             loading="eager">
        <h1 class="mt-3" style="font-size:1.5rem;font-weight:700">شكراً! إستلمنا طلبك</h1>
        <p style="font-size:.88rem;color:var(--text)">
            لقد أرسلنا رسالة تأكيد طلب رسالة قصيرة إلى
            {{ $lastOrder['customer_phone'] ?? '' }}
            مع تفاصيل طلبك.
        </p>
    </div>

    <div class="checkout-summary p-4">
        <div class="d-flex justify-content-between mb-2">
            <span style="color:var(--text);font-size:.85rem">رقم الطلب:</span>
            <strong>{{ $lastOrder['order_number'] ?? '—' }}</strong>
        </div>

        @if($lastOrder['delivery_type'] === 'home')
        <div class="d-flex justify-content-between mb-2">
            <span style="color:var(--text);font-size:.85rem">التوصيل:</span>
            <strong>التوصيل لحد البيت</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="color:var(--text);font-size:.85rem">موعد الوصول المتوقع:</span>
            <strong>الخميس، 5 ديسمبر</strong>
        </div>
        @else
        <div class="d-flex justify-content-between mb-2">
            <span style="color:var(--text);font-size:.85rem">مكان الإستلام:</span>
            <strong>
                @if($branch)
                    {{ $branch->branch_name }} - {{ $branch->branch_place }}
                @else
                    الفرع المختار
                @endif
            </strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="color:var(--text);font-size:.85rem">تاريخ الإستلام:</span>
            <strong>{{ now()->addDays(2)->format('d/m/Y') }}</strong>
        </div>
        @endif

        {{-- Progress Bar --}}
        <div class="mt-4 mb-2">
            <div class="d-flex justify-content-between mb-1" style="font-size:.75rem;color:var(--text)">
                <span>تم التوصيل</span>
                <span>تم التجهيز</span>
                <span>تم الطلب</span>
            </div>
            <div style="background:var(--stroke);border-radius:99px;height:6px;overflow:hidden">
                <div style="width:20%;height:100%;background:var(--primary);border-radius:99px"></div>
            </div>
        </div>

        <p class="text-center mt-3" style="font-size:.82rem;color:var(--text)">
            هنبعتك رسالة تانية فيها ميعاد التوصيل بالظبط.
        </p>
    </div>

    <div class="d-flex gap-3 mt-4">
        @if(session('customer_phone'))
        <a href="{{ route('UserPersonalPage') }}"
           class="btn btn-outline-secondary flex-1 text-center py-2"
           style="flex:1">
            تابع طلبي
        </a>
        @endif
        <a href="{{ route('EcommerceAllProducts') }}"
           class="btn hero__btn flex-1 text-center py-2"
           style="flex:1">
            تابع التسوق
        </a>
    </div>
</div>
@endsection

5. صفحة حسابي
blade{{-- resources/views/ecommerce/PersonalPage/UserPersonalPage.blade.php --}}
@extends('layouts.app')
@section('title', 'حسابي — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="container py-5" style="max-width:900px">

    {{-- Header --}}
    <div class="personal-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-1" style="font-size:1.3rem;font-weight:700">
                    أهلاً، {{ session('customer_name') ?? 'العميل' }}
                </h2>
                <p class="mb-0" style="font-size:.88rem;color:var(--text)">
                    +2{{ session('customer_phone') ?? '' }}
                </p>
            </div>
            <div class="personal-avatar">
                <i class="bi bi-person-fill" style="font-size:2rem;color:var(--primary)"></i>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- القائمة اليسار --}}
        <div class="col-md-4">
            <div class="personal-card">
                <nav class="personal-nav">
                    <a href="{{ route('UserPersonalPage') }}"
                       class="personal-nav__item {{ request()->routeIs('UserPersonalPage') ? 'is-active' : '' }}">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        حسابي
                    </a>
                    <a href="{{ route('UserPersonalUnderRequstProducts') }}"
                       class="personal-nav__item {{ request()->routeIs('UserPersonalUnderRequstProducts') ? 'is-active' : '' }}">
                        <i class="bi bi-bag" aria-hidden="true"></i>
                        طلباتي
                    </a>
                    <a href="#"
                       class="personal-nav__item">
                        <i class="bi bi-credit-card" aria-hidden="true"></i>
                        إدارة بطاقات الدفع
                    </a>
                    <a href="{{ route('UserPersonalLogOut') }}"
                       class="personal-nav__item personal-nav__item--danger">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        تسجيل الخروج
                    </a>
                </nav>
            </div>
        </div>

        {{-- المحتوى اليمين --}}
        <div class="col-md-8">
            <div class="personal-card">
                <h3 class="mb-4" style="font-size:1rem;font-weight:700">بياناتي</h3>

                <div class="personal-info-row">
                    <span class="personal-info-row__label">الاسم</span>
                    <span class="personal-info-row__value">{{ $customer->customer_name ?? '—' }}</span>
                </div>
                <div class="personal-info-row">
                    <span class="personal-info-row__label">رقم الموبايل</span>
                    <span class="personal-info-row__value" dir="ltr">
                        +2{{ $customer->customer_phone ?? '—' }}
                    </span>
                </div>
                @if($customer->customer_email)
                <div class="personal-info-row">
                    <span class="personal-info-row__label">البريد الإلكتروني</span>
                    <span class="personal-info-row__value">{{ $customer->customer_email }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

6. CSS للصفحات الجديدة
css/* أضف في products.css */

/* ══════════════════════
   Checkout Page
══════════════════════ */
.checkout-page { min-height: 100vh; background: var(--bg-secondary); }

.checkout-header {
    background: var(--white);
    border-bottom: 1px solid var(--stroke);
    padding: 0.75rem 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.checkout-input {
    width: 100%;
    padding: 0.65rem 0.875rem;
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-family: inherit;
    direction: rtl;
    text-align: right;
    background: var(--white);
    transition: border-color 0.2s;
    display: block;
}
.checkout-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(64,102,172,.12);
}

.delivery-type-btns {
    display: flex;
    gap: 0.5rem;
}
.delivery-type-btn {
    flex: 1;
    padding: 0.6rem;
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    background: var(--white);
    font-size: 0.88rem;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.delivery-type-btn--active {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

.delivery-option-card,
.branch-option {
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    padding: 0.875rem;
    cursor: pointer;
    transition: border-color 0.2s;
    margin-bottom: 0.5rem;
}
.branch-option.is-selected { border-color: var(--primary); background: rgba(64,102,172,.04); }

.delivery-fee-badge {
    background: var(--bg-secondary);
    border-radius: 99px;
    padding: 0.2rem 0.6rem;
    font-size: 0.78rem;
    color: var(--text);
}

.checkout-info-row {
    background: var(--bg-secondary);
    border-radius: var(--radius-sm);
    padding: 0.6rem 0.875rem;
    margin-bottom: 0.5rem;
    font-size: 0.88rem;
    text-align: right;
}

.checkout-note {
    background: #eff6ff;
    border-radius: var(--radius-sm);
    padding: 0.6rem 0.875rem;
    font-size: 0.8rem;
    color: var(--text);
}

.checkout-step--done .checkout-step__header {
    background: transparent;
}

.checkout-step__num--done {
    background: #16a34a !important;
}

.payment-methods { display: flex; flex-direction: column; gap: 0.5rem; }
.payment-method-option {
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    padding: 0.875rem;
    cursor: pointer;
    transition: border-color 0.2s;
}
.payment-method-option--active { border-color: var(--primary); background: rgba(64,102,172,.04); }

.saved-card-option {
    display: flex;
    align-items: center;
    border: 1px solid var(--stroke);
    border-radius: var(--radius-sm);
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    font-size: 0.88rem;
    transition: border-color 0.2s;
}
.saved-card-option:has(input:checked) { border-color: var(--primary); }

/* ══════════════════════
   Personal Page
══════════════════════ */
.personal-card {
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}

.personal-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.personal-nav { display: flex; flex-direction: column; gap: 0.25rem; }

.personal-nav__item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    color: var(--text);
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.personal-nav__item:hover { background: var(--bg-secondary); color: var(--heading); }
.personal-nav__item.is-active { background: var(--bg-secondary); color: var(--primary); font-weight: 600; }
.personal-nav__item--danger:hover { background: #fef2f2; color: var(--red); }

.personal-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--stroke);
    font-size: 0.88rem;
}
.personal-info-row:last-child { border-bottom: none; }
.personal-info-row__label { color: var(--text); }
.personal-info-row__value { font-weight: 600; color: var(--heading); }

7. تحسين الـ Middleware — ثغرات مهمة
php// في EcommerceSecurityMiddleware.php
// المشكلة: الـ middleware في $middleware الـ global
// ده معناه بيشتغل على كل request — حتى الـ admin
// الأفضل: نضيفه في $middlewareGroups['web']

// في Kernel.php:
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ← أضف هنا
        \App\Http\Middleware\EcommerceSecurityHeaders::class,
    ],
];

// والـ EcommerceSecurityMiddleware يبقى alias بس
// ويتطبق على الـ ecommerce routes فقط في web.php