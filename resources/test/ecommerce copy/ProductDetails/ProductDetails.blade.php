 @php
     $product = $Product?->product;
     $sellPrice = (float) ($product?->product_sellprice ?? 0);
     $offerPrice = (float) ($product?->product_offerprice ?? 0);
     $hasOffer = $offerPrice > 0 && $offerPrice < $sellPrice;
     $discount = $hasOffer ? round((($sellPrice - $offerPrice) / $sellPrice) * 100) : 0;
     $stock = (int) ($product?->{'232021'} ?? 0);
     $mainImage = $product?->product_image ?? null;
     $extraImages = array_filter([
         $product?->product_image2 ?? null,
         $product?->product_image3 ?? null,
         $product?->product_image4 ?? null,
     ]);
     if (empty($extraImages) && $mainImage) {
         $extraImages = [$mainImage, $mainImage, $mainImage];
     }
     $allThumbs = $mainImage ? array_values(array_merge([$mainImage], array_values($extraImages))) : [];
     $hasRealReviews = ($reviewsCount ?? 0) > 0;
     $avgDisplay = $hasRealReviews ? round($avgRating ?? 0) : 4; /* | Specs من الـ
 description | لو كل سطر فيه ":" بيتحول لجدول | لو مفيش — بيعرض placeholder */
     $specs = [];
     if ($product?->product_description) {
         foreach (explode("\n", $product->product_description) as $line) {
             $parts = explode(':', trim($line), 2);
             if (count($parts) === 2 && trim($parts[0]) && trim($parts[1])) {
                 $specs[] = ['key' => trim($parts[0]), 'val' => trim($parts[1])];
             }
         }
     }
     $placeholderSpecs = [
         ['key' => 'الضمان', 'val' => 'سنة واحدة'],
         ['key' => 'اسم الموديل', 'val' => $product?->product_name ?? '—'],
         ['key' => 'ماركة', 'val' => '—'],
         ['key' => 'اللون', 'val' => '—'],
         ['key' => 'سعة التخزين', 'val' => '—'],
         ['key' => 'رام', 'val' => '—'],
         ['key' => 'الكاميرا الخلفية', 'val' => '—'],
         [
             'key' => 'الكاميرا
الأمامية',
             'val' => '—',
         ],
     ];
     $displaySpecs = !empty($specs) ? $specs : $placeholderSpecs;
     $placeholderReviews = [
         [
             'name' => 'Mohamed ali',
             'rating' => 5,
             'date' => '08 نوفمبر 2024',
             'comment' => 'موبايل رائع و مريح و بطاريته
ممتازة',
         ],
         ['name' => 'Ahmed maged', 'rating' => 5, 'date' => '08 نوفمبر 2024', 'comment' => 'مريح جدا و عملي جدا'],
     ];
 @endphp @extends('layouts.app')
 @section('title', ($product?->product_name ?? 'تفاصيل المنتج') . ' — ' . ($ecommerceSharedData['branchName'] ?? ''))
 @section('description', $product?->product_description ? Str::limit(strip_tags($product->product_description), 160) :
     '') @if ($mainImage)
         @section('og_image', asset('images/productsimages/' . $mainImage))
     @endif
     @section('content')
         <div class="container py-4">
             {{-- ══════════════════════════════════════════════ TOP SECTION
    ══════════════════════════════════════════════ --}}
             <div class="pd-top">

                 {{-- العمود 1: الصور الصغيرة --}}
                 <div class="pd-col-thumbs">
                     @foreach ($allThumbs as $i => $img)
                         <button class="pd-thumb {{ $i === 0 ? 'is-active' : '' }}" type="button"
                             aria-label="صورة {{ $i + 1 }}"
                             onclick="switchImg(this, '{{ asset('images/productsimages/' . $img) }}')">
                             <img src="{{ asset('images/productsimages/' . $img) }}" alt="{{ $product?->product_name ?? '' }}"
                                 width="80" height="120" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" />
                         </button>
                     @endforeach
                 </div>
                 {{-- العمود 2: الصورة الكبيرة --}}
                 <div class="pd-col-main-img">
                     <div class="pd-main-img-wrap">
                         @if ($hasOffer)
                             <span class="pd-offer-badge">{{ $discount }}%</span>
                         @endif
                         <img id="mainImg"
                             src="{{ $mainImage ? asset('images/productsimages/' . $mainImage) : asset('images/placeholder.png') }}"
                             alt="{{ $product?->product_name ?? '' }}" class="pd-main-img" width="500" height="500"
                             loading="eager" fetchpriority="high" />
                     </div>

                     {{-- الصور الصغيرة في الموبايل — تحت الصورة الكبيرة --}}
                     {{-- <div class="pd-thumbs-mobile d-flex d-lg-none mt-2">
                         @foreach ($allThumbs as $i => $img)
                             <button class="pd-thumb {{ $i === 0 ? 'is-active' : '' }}" type="button"
                                 onclick="switchImg(this, '{{ asset('images/productsimages/' . $img) }}')">
                                 <img src="{{ asset('images/productsimages/' . $img) }}"
                                     alt="{{ $product?->product_name ?? '' }}" width="80" height="120" loading="lazy" />
                             </button>
                         @endforeach
                     </div> --}}
                 </div>

                 {{-- العمود 3: Info --}}
                 <div class="pd-col-info">
                     {{-- الاسم --}}
                     <h1 class="pd-title">{{ $product?->product_name ?? '' }}</h1>

                     {{-- الوصف --}}
                     @if ($product?->product_description && empty($specs))
                         <p class="pd-short-desc">
                             {{ Str::limit($product->product_description, 200) }}
                         </p>
                     @endif {{-- السعر --}}
                     <div class="pd-price-block">
                         @if ($hasOffer)
                             <span class="pd-price pd-price--offer">{{ number_format($offerPrice) }} جنية</span>
                             <span class="pd-price pd-price--old">{{ number_format($sellPrice) }}</span>
                             <span class="pd-discount-chip">خصم {{ $discount }}%</span>
                         @else
                             <span class="pd-price">{{ number_format($sellPrice) }} جنية</span>
                         @endif
                     </div>

                     {{-- Stock --}}
                     <div class="pd-stock-row">
                         المنتج: @if ($stock > 0)
                             <span class="pd-stock-chip pd-stock-chip--in">
                                 <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                                 متوفر
                             </span>
                             (عدد {{ $stock }})
                         @else
                             <span class="pd-stock-chip pd-stock-chip--out">
                                 <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                                 غير متوفر
                             </span>
                         @endif
                     </div>

                     {{-- Actions --}}
                     <!-- <div class="pd-actions">
                         {{-- القلب --}}
                         <button class="btn product-card__wish-btn" type="button" aria-label="المفضلة">
                             <i class="bi bi-heart"></i>
                         </button>

                         {{-- زرار السلة --}}
                         @if (session('customer_phone'))
                             <form method="POST"
                                 action="{{ route('CustomerRequestIncreseQuantityPost', $Product?->ecommerceproduct_id) }}"
                                 class="pd-cart-form">
                                 @csrf
                                 <input type="hidden" name="quantity" id="hiddenQty" value="1" />
                                 <button type="submit" class="btn hero__btn pd-cart-btn">
                                     <i class="bi bi-basket" aria-hidden="true"></i>
                                     أضف الى السلة
                                 </button>
                             </form>
                         @else
                             <a href="{{ route('CustomerLogin') }}" class="btn hero__btn pd-cart-btn">
                                 <i class="bi bi-basket" aria-hidden="true"></i>
                                 أضف الى السلة
                             </a>
                         @endif {{-- Qty --}}
                         <div class="pd-qty">
                             <button type="button" class="pd-qty-btn" id="qtyPlus">
                                 <i class="bi bi-plus" aria-hidden="true"></i>
                             </button>
                             <output id="qtyInput" class="pd-qty-val">1</output>
                             <button type="button" class="pd-qty-btn" id="qtyMinus">
                                 <i class="bi bi-dash" aria-hidden="true"></i>
                             </button>
                         </div>
                     </div> -->

                    {{-- في ProductDetails.blade.php -- قسم الـ Actions --}}

                    {{-- Actions --}}
                    <div class="pd-actions">

                        {{-- القلب — Wishlist Toggle --}}
                        @php $inWishlist = in_array($Product?->ecommerceproduct_id, session('wishlist', [])); @endphp
                        <button type="button"
                                class="btn pd-wish-btn js-wishlist-toggle {{ $inWishlist ? 'is-wishlisted' : '' }}"
                                data-id="{{ $Product?->ecommerceproduct_id }}"
                                aria-label="{{ $inWishlist ? 'إزالة من المفضلة' : 'أضف للمفضلة' }}"
                                aria-pressed="{{ $inWishlist ? 'true' : 'false' }}">
                            <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}" aria-hidden="true"></i>
                        </button>

                        {{-- زرار السلة — Add to Cart AJAX --}}
                        <button type="button"
                                class="btn hero__btn pd-cart-btn js-add-to-cart"
                                data-id="{{ $Product?->ecommerceproduct_id }}"
                                aria-label="أضف {{ $product?->product_name ?? '' }} إلى السلة">
                            <i class="bi bi-basket" aria-hidden="true"></i>
                            <span class="btn-text">أضف الى السلة</span>
                        </button>

                        {{-- Qty --}}
                        <div class="pd-qty" role="group" aria-label="الكمية">
                            <button type="button" class="pd-qty-btn" id="qtyPlus" aria-label="زيادة">
                                <i class="bi bi-plus" aria-hidden="true"></i>
                            </button>
                            <output id="qtyInput" class="pd-qty-val" aria-live="polite">1</output>
                            <button type="button" class="pd-qty-btn" id="qtyMinus" aria-label="تقليل">
                                <i class="bi bi-dash" aria-hidden="true"></i>
                            </button>
                        </div>

                    </div>

                     {{-- Payment --}}
                     <div class="pd-payment">
                         <img src="{{ asset('/images/socialmediacontacts/backingmobile.webp') }}" alt="وسائل الدفع"
                             loading="lazy" width="280" height="60" />
                     </div>

                     {{-- Related Products --}} @if (isset($RelatedProducts) && $RelatedProducts->count())
                         <div class="pd-related">
                             <h2 class="pd-related-title">منتجات ممكن تحتاجها</h2>
                             <div class="pd-related-list">
                                 @foreach ($RelatedProducts as $rel)
                                     <a href="{{ route('ProductDetails', $rel->ecommerceproduct_id) }}"
                                         class="pd-related-card">
                                         <span class="pd-related-img__wrapper">
                                             <img src="{{ asset('images/productsimages/' . ($rel->product?->product_image ?? 'placeholder.png')) }}"
                                                 alt="{{ $rel->product?->product_name ?? '' }}" width="80" height="120"
                                                 loading="lazy" />
                                         </span>
                                         <span class="pd-related-card__name">
                                             {{ Str::limit($rel->product?->product_name ?? '', 18) }}
                                         </span>
                                         <span class="pd-related-card__price">
                                             <i class="bi bi-basket-fill" aria-hidden="true"></i>
                                             {{ number_format($rel->product?->product_sellprice ?? 0) }}
                                             ج.م
                                         </span>
                                     </a>
                                 @endforeach
                             </div>
                         </div>
                     @endif
                 </div>

                 {{-- العمود 4: التقييم — ديسكتوب فقط --}}
                 <div class="pd-col-rating d-none d-lg-flex">
                     <div class="pd-rating-col">
                         @for ($i = 1; $i <= 5; $i++)
                             <i class="bi bi-star-fill {{ $i <= $avgDisplay ? 'star-on' : 'star-off' }}"
                                 aria-hidden="true"></i>
                             @endfor @if ($hasRealReviews)
                                 <span class="pd-rating-count">(الآراء {{ $reviewsCount }})</span>
                             @else
                                 <span class="pd-rating-count pd-rating-count--muted">(الآراء)</span>
                             @endif
                     </div>
                 </div>
             </div>

             {{-- ══════════════════════════════════ SPECS + REVIEWS
    ══════════════════════════════════ --}}
             <div class="pd-section mt-4">
                 {{-- Specs --}}
                 <div class="pd-acc">
                     <button class="pd-acc__btn" type="button" data-bs-toggle="collapse" data-bs-target="#specsBody"
                         aria-expanded="true">
                         <span>مواصفات المنتج</span>
                         <i class="bi bi-chevron-up pd-acc__chevron" aria-hidden="true"></i>
                     </button>
                     <div class="collapse show" id="specsBody">
                         <div class="pd-acc__body p-0">
                             <table class="pd-specs-tbl">
                                 <tbody>
                                     @foreach ($displaySpecs as $spec)
                                         <tr>
                                             <td class="pd-specs-tbl__label">
                                                 {{ $spec['key'] }}
                                             </td>
                                             <td class="pd-specs-tbl__val">
                                                 {{ $spec['val'] }}
                                             </td>
                                         </tr>
                                     @endforeach
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>

                 {{-- Reviews --}}
                 <div class="pd-acc mt-3">
                     <button class="pd-acc__btn" type="button" data-bs-toggle="collapse" data-bs-target="#reviewsBody"
                         aria-expanded="true">
                         <span>
                             الأراء @if ($hasRealReviews)
                                 <span class="pd-acc__count">{{ $reviewsCount }}</span>
                             @endif
                         </span>
                         <i class="bi bi-chevron-up pd-acc__chevron" aria-hidden="true"></i>
                     </button>
                     <div class="collapse show" id="reviewsBody">
                         <div class="pd-acc__body">
                             {{-- Summary @if ($hasRealReviews) --}}
                             <div class="pd-reviews-summary mb-4">
                                 <small class="text-muted">تقييمات و آراء العملاء</small>
                                 <div class="d-flex justify-content-center align-items-center">
                                     <div class="pd-reviews-summary__score">
                                         {{ number_format($avgRating ?? 0, 1) }}
                                     </div>
                                     <div class="pd-review-stars mb-1">
                                         @for ($i = 1; $i <= 5; $i++)
                                             <i class="bi bi-star-fill {{ $i <= round($avgRating ?? 0) ? 'star-on' : 'star-off' }}"
                                                 aria-hidden="true"></i>
                                         @endfor
                                     </div>
                                 </div>
                             </div>
                             {{-- @endif  --}}
                             {{-- Reviews List --}}
                             @if ($hasRealReviews)
                                 @foreach ($Reviews as $review)
                                     <article class="pd-review">
                                         <div class="pd-review__header">
                                             <div class="pd-review__right">
                                                 <span class="text-muted" style="font-size: 0.78rem">بواسطة</span>
                                                 <span class="pd-review__author">
                                                     {{ $review->customer?->customer_name ?? 'مجهول' }}
                                                 </span>
                                             </div>
                                             <div class="pd-review__left">
                                                 <time class="pd-review__date"
                                                     datetime="{{ $review->created_at?->toISOString() }}">
                                                     {{ $review->created_at?->format('d نوفمبر
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               Y') }}
                                                 </time>
                                                 <div class="pd-review-stars">
                                                     @php$r = $review->customerproductcomment_rating;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           @endphp ?> ?> ?> ?> ?> ?> ?> ?> ?> ?> @for ($i = 1; $i <= 5; $i++)
                                                         <i class="bi bi-star-fill {{ $i <= $r ? 'star-on' : 'star-off' }}"
                                                             aria-hidden="true"></i>
                                                     @endfor
                                                     <span
                                                         style="
                                            font-size: 0.75rem;
                                            color: var(--text);
                                        ">{{ $r }}/5</span>
                                                 </div>
                                             </div>
                                         </div>
                                         <p class="pd-review__text">
                                             {{ $review->customerproductcomment_comment }}
                                         </p>
                                     </article>
                                 @endforeach
                             @else
                                 @foreach ($placeholderReviews as $pr)
                                     <article class="pd-review">
                                         <div class="pd-review__header">
                                             <div class="pd-review__right">
                                                 <span class="text-muted" style="font-size: 0.78rem">بواسطة</span>
                                                 <span class="pd-review__author">{{ $pr['name'] }}</span>
                                             </div>
                                             <div class="pd-review__left">
                                                 <time class="pd-review__date">{{ $pr['date'] }}</time>
                                                 <div class="pd-review-stars">
                                                     @for ($i = 1; $i <= 5; $i++)
                                                         <i class="bi bi-star-fill {{ $i <= $pr['rating'] ? 'star-on' : 'star-off' }}"
                                                             aria-hidden="true"></i>
                                                     @endfor
                                                     <span
                                                         style="
                                            font-size: 0.75rem;
                                            color: var(--text);
                                        ">{{ $pr['rating'] }}/5</span>
                                                 </div>
                                             </div>
                                         </div>
                                         <p class="pd-review__text">{{ $pr['comment'] }}</p>
                                     </article>
                                 @endforeach
                             @endif {{-- Write Review --}}
                             <div class="pd-write-review">
                                 <p class="pd-write-review__label">أكتب رأيك</p>

                                 {{-- النجوم --}}
                                 <div class="pd-star-input mb-3">
                                     @for ($i = 5; $i >= 1; $i--)
                                         <input type="radio" name="rating" id="star{{ $i }}"
                                             value="{{ $i }}" class="pd-star-input__radio" form="reviewForm" />
                                         <label for="star{{ $i }}" class="pd-star-input__label">
                                             <i class="bi bi-star-fill" aria-hidden="true"></i>
                                         </label>
                                     @endfor
                                 </div>

                                 {{-- الـ textarea دايماً ظاهر --}}
                                 <div class="pd-form-wrapper mb-3">
                                     <textarea name="comment" class="form-control" rows="1" placeholder="أكتب هنا" maxlength="500"
                                         form="reviewForm" aria-label="تعليقك"></textarea>

                                     @if (session('customer_phone'))
                                         <form method="POST" id="reviewForm"
                                             action="{{ route('CustomerAddProductCommentPost', $Product?->ecommerceproduct_id) }}">
                                             @csrf
                                             <button type="submit" class="btn hero__btn">
                                                 إرسال
                                             </button>
                                         </form>
                                     @else
                                         <a href="{{ route('CustomerLogin') }}" class="btn hero__btn w-100">
                                             إرسال
                                         </a>
                                     @endif
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             {{-- Similar Products --}} @if (isset($SimilarProducts) && $SimilarProducts->count())
                 <div class="mt-5">
                     <div class="d-flex justify-content-between align-items-center mb-4">
                         <h2 class="section-title mb-0">منتجات بديلة</h2>
                         <a href="{{ route('EcommerceAllProducts') }}" class="section-more-link">
                             <i class="bi bi-arrow-right" aria-hidden="true"></i>
                             عرض المزيد
                         </a>
                     </div>
                     <div class="products-grid">
                         @foreach ($SimilarProducts as $ep)
                             <x-product-card :id="$ep->ecommerceproduct_id" :name="$ep->product?->product_name ?? ''" :price="$ep->product?->product_sellprice ?? 0" :offerPrice="$ep->product?->product_offerprice ?? null"
                                 :image="$ep->product?->product_image ?? ''" route="ProductDetails" :hasOffer="($ep->product?->product_offerprice ?? 0) > 0 &&
                                     $ep->product?->product_offerprice < $ep->product?->product_sellprice" />
                         @endforeach
                     </div>
                 </div>
             @endif
         </div>

         @endsection 
         @push('scripts')
        <script>
        (function() {
            'use strict';

            // ── صور المنتج ──
            function switchImg(btn, src) {
                const main = document.getElementById('mainImg');
                if (main) main.src = src;
                document.querySelectorAll('.pd-thumb').forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');
            }
            window.switchImg = switchImg;

            // ── Qty Control ──
            /*
            | qtyInput: الـ <output> اللي بيظهر الرقم
            | الـ js-add-to-cart في layouts/app.blade.php
            | بيقرأ منه قبل ما يبعت الـ request
            */
            const qtyDisplay = document.getElementById('qtyInput');
            let qty    = 1;
            const maxQty = {{ $stock > 0 ? min($stock, 10) : 10 }};

            function updateQty(val) {
                qty = Math.max(1, Math.min(val, maxQty));
                // textContent — آمن
                if (qtyDisplay) qtyDisplay.textContent = qty;
            }

            document.getElementById('qtyMinus')?.addEventListener('click', () => updateQty(qty - 1));
            document.getElementById('qtyPlus')?.addEventListener('click',  () => updateQty(qty + 1));

            // ── Collapse Chevron ──
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
                const target  = document.querySelector(btn.getAttribute('data-bs-target'));
                const chevron = btn.querySelector('.pd-acc__chevron');
                if (!target || !chevron) return;

                target.addEventListener('show.bs.collapse', () => {
                    chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
                });
                target.addEventListener('hide.bs.collapse', () => {
                    chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
                });
            });

        })();
        </script>
    @endpush
     
