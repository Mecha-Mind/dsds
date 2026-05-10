{{-- resources/views/ProductDetails/index.blade.php --}}
@extends('layouts.app')

@section('title', ($Product?->product?->product_name ?? 'تفاصيل المنتج') . ' — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

<div class="container py-4">

    {{-- ════════════════════ Product Main ════════════════════ --}}
    <div class="row g-4">

        {{-- الصور --}}
        <div class="col-lg-5">
            <div class="product-gallery">

                {{-- الصورة الرئيسية --}}
                <div class="product-gallery__main">
                    <img id="mainProductImg"
                         src="{{ asset('images/products/' . ($Product?->product?->product_image ?? 'placeholder.png')) }}"
                         alt="{{ $Product?->product?->product_name ?? '' }}"
                         class="product-gallery__img"
                         loading="eager">
                </div>

                {{-- الصور الصغيرة --}}
                {{--
                    ← لما ترجع الشغل:
                    لو في جدول للـ product images منفصل، اجيبهم هنا
                    دلوقتي بنعرض الصورة الرئيسية بس
                --}}
                <div class="product-gallery__thumbs">
                    <button class="product-gallery__thumb active"
                            onclick="changeImage('{{ asset('images/products/' . ($Product?->product?->product_image ?? '')) }}', this)">
                        <img src="{{ asset('images/products/' . ($Product?->product?->product_image ?? 'placeholder.png')) }}"
                             alt="صورة 1">
                    </button>
                </div>

            </div>
        </div>

        {{-- البيانات --}}
        <div class="col-lg-4">

            {{-- التقييم --}}
            <div class="product-detail__rating mb-2">
                @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star-fill {{ $i <= ($avgRating ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
                <span class="text-muted ms-1">({{ $reviewsCount ?? 0 }})</span>
            </div>

            {{-- الاسم --}}
            <h1 class="product-detail__name">
                {{ $Product?->product?->product_name ?? '' }}
            </h1>

            {{-- الوصف --}}
            <p class="product-detail__desc">
                {{ $Product?->product?->product_description ?? '' }}
            </p>

            {{-- السعر --}}
            <div class="product-detail__prices mb-3">
                @php
                    $sellPrice  = $Product?->product?->product_sellprice  ?? 0;
                    $offerPrice = $Product?->product?->product_offerprice ?? 0;
                    $hasOffer   = $offerPrice > 0 && $offerPrice < $sellPrice;
                @endphp

                @if($hasOffer)
                <span class="product-detail__price text-danger">
                    {{ number_format($offerPrice) }} جنية
                </span>
                <span class="product-detail__old-price">
                    {{ number_format($sellPrice) }} جنية
                </span>
                <span class="product-detail__discount-badge">
                    خصم {{ round((($sellPrice - $offerPrice) / $sellPrice) * 100) }}%
                </span>
                @else
                <span class="product-detail__price">
                    {{ number_format($sellPrice) }} جنية
                </span>
                @endif
            </div>

            {{-- المخزون --}}
            <div class="product-detail__stock mb-3">
                @php
                    $stock = $Product?->product?->{232021} ?? 0;
                    // ← لما ترجع الشغل: اسم الـ column للـ stock
                    // في الـ DB كان اسمه '232021' — تأكد من الاسم الصح
                @endphp
                @if($stock > 0)
                <span class="badge bg-success-subtle text-success">
                    <i class="bi bi-check-circle me-1"></i>
                    متاح ({{ $stock }})
                </span>
                @else
                <span class="badge bg-danger-subtle text-danger">
                    <i class="bi bi-x-circle me-1"></i>
                    غير متاح
                </span>
                @endif
            </div>

            {{-- الكمية + السلة --}}
            <div class="product-detail__actions mb-4">
                <div class="quantity-control">
                    <button type="button" class="qty-btn" onclick="changeQty(-1)">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" id="qtyInput" class="qty-input"
                           value="1" min="1" max="{{ $stock }}">
                    <button type="button" class="qty-btn" onclick="changeQty(1)">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                @if(session('customer_phone'))
                <form method="POST"
                      action="{{ route('CustomerRequestIncreseQuantityPost', $Product?->ecommerceproduct_id) }}"
                      class="flex-1">
                    @csrf
                    <input type="hidden" name="quantity" id="hiddenQty" value="1">
                    <button type="submit" class="btn hero__btn w-100">
                        <i class="bi bi-bag-plus me-1"></i>
                        أضف إلى السلة
                    </button>
                </form>
                @else
                <a href="{{ route('CustomerLogin') }}" class="btn hero__btn flex-1">
                    <i class="bi bi-person me-1"></i>
                    سجل دخول للشراء
                </a>
                @endif

                <button class="btn btn-outline-secondary wishlist-btn" title="المفضلة">
                    <i class="bi bi-heart"></i>
                </button>
            </div>

            {{-- وسائل الدفع --}}
            <div class="product-detail__payment">
                <small class="text-muted d-block mb-2">وسائل الدفع المتاحة</small>
                <div class="d-flex gap-2 align-items-center">
                    @foreach(['visa.png', 'mastercard.png', 'paypal.png', 'stripe.png'] as $pm)
                    <img src="{{ asset('images/socialmediacontacts/backing.png') }}"
                         alt="وسيلة دفع" height="24" loading="lazy">
                    @endforeach
                </div>
            </div>

        </div>

        {{-- منتجات مجاورة --}}
        <div class="col-lg-3 d-none d-lg-block">
            <h3 class="sidebar-title mb-3">منتجات تحبها مؤخراً</h3>
            <div class="d-flex flex-column gap-3">
                @foreach($RelatedProducts ?? [] as $related)
                <a href="{{ route('ProductDetails', $related->ecommerceproduct_id) }}"
                   class="related-product-card">
                    <img src="{{ asset('images/products/' . ($related->product?->product_image ?? '')) }}"
                         alt="{{ $related->product?->product_name ?? '' }}"
                         loading="lazy">
                    <div class="related-product-card__info">
                        <p class="related-product-card__name">
                            {{ $related->product?->product_name ?? '' }}
                        </p>
                        <span class="related-product-card__price">
                            {{ number_format($related->product?->product_sellprice ?? 0) }} ج.م
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ════════════════════ Specs Accordion ════════════════════ --}}
    <div class="row mt-5">
        <div class="col-lg-9">

            {{-- المواصفات --}}
            <div class="accordion specs-accordion mb-4" id="specsAccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button specs-accordion__btn collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#specsContent">
                            <i class="bi bi-list-ul me-2"></i>
                            مواصفات المنتج
                        </button>
                    </h2>
                    <div id="specsContent" class="accordion-collapse collapse show">
                        <div class="accordion-body p-0">
                            @if($Product?->product?->product_description)
                            <table class="specs-table">
                                {{--
                                    ← لما ترجع الشغل:
                                    لو في جدول specs منفصل، اجيب الـ specs من هناك
                                    دلوقتي بنعرض الـ description
                                --}}
                                <tr>
                                    <td class="specs-table__key">الوصف</td>
                                    <td class="specs-table__val">{{ $Product->product->product_description }}</td>
                                </tr>
                                @if($Product?->product?->product_category2)
                                <tr>
                                    <td class="specs-table__key">التصنيف</td>
                                    <td class="specs-table__val">{{ $Product->product->product_category2 }}</td>
                                </tr>
                                @endif
                            </table>
                            @else
                            <p class="text-muted p-3">لا توجد مواصفات متاحة</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- الآراء --}}
            <div class="accordion specs-accordion" id="reviewsAccordion">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button specs-accordion__btn"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#reviewsContent">
                            <i class="bi bi-chat-square-text me-2"></i>
                            الآراء ({{ $reviewsCount ?? 0 }})
                        </button>
                    </h2>
                    <div id="reviewsContent" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            @forelse($Reviews ?? [] as $review)
                            <div class="review-item">
                                <div class="review-item__header">
                                    <span class="review-item__name">{{ $review->customer?->customer_name ?? 'مجهول' }}</span>
                                    <div class="review-item__stars">
                                        @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= $review->customerproductcomment_rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="review-item__text">{{ $review->customerproductcomment_comment }}</p>
                            </div>
                            @empty
                            <p class="text-muted">لا توجد آراء بعد</p>
                            @endforelse

                            {{-- إضافة رأي --}}
                            @if(session('customer_phone'))
                            <div class="add-review mt-4">
                                <h5 class="mb-3">أضف رأيك</h5>
                                <form method="POST"
                                      action="{{ route('CustomerAddProductCommentPost', $Product?->ecommerceproduct_id) }}">
                                    @csrf
                                    <div class="star-rating mb-3">
                                        @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}">
                                        <label for="star{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                        @endfor
                                    </div>
                                    <textarea name="comment" class="form-control mb-3"
                                              rows="3" placeholder="اكتب رأيك هنا..." required></textarea>
                                    <button type="submit" class="btn hero__btn">إرسال</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ════════════════════ منتجات بديلة ════════════════════ --}}
    @if(isset($SimilarProducts) && $SimilarProducts->count())
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('EcommerceAllProducts') }}" class="section-more-link">
                <i class="bi bi-arrow-right"></i>
                عرض المزيد
            </a>
            <h2 class="section-title mb-0">منتجات بديلة</h2>
        </div>
        <div class="products-grid">
            @foreach($SimilarProducts as $ep)
            <x-product-card
                :id="$ep->ecommerceproduct_id"
                :name="$ep->product?->product_name ?? ''"
                :price="$ep->product?->product_sellprice ?? 0"
                :offer-price="$ep->product?->product_offerprice ?? null"
                :image="$ep->product?->product_image ?? ''"
                route="ProductDetails"
                :has-offer="($ep->product?->product_offerprice ?? 0) > 0"
            />
            @endforeach
        </div>
    </div>
    @endif

</div>

@endsection

@section('scripts')
<script>
// ── تغيير الصورة الرئيسية --
function changeImage(src, btn) {
    document.getElementById('mainProductImg').src = src;
    document.querySelectorAll('.product-gallery__thumb').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// ── تغيير الكمية ──
function changeQty(delta) {
    const input   = document.getElementById('qtyInput');
    const hidden  = document.getElementById('hiddenQty');
    const max     = parseInt(input.max) || 99;
    let   val     = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value  = val;
    if(hidden) hidden.value = val;
}
</script>
@endsection