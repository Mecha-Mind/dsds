{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('title', ($ecommerceSharedData['branch']->branch_name ?? '') . ' - الرئيسية')
@section('description', 'تسوق أحدث المنتجات بأفضل الأسعار')

@section('content')

{{-- ══════════════════════ HERO SLIDER ══════════════════════ --}}
<section class="hero-section" aria-label="البانر الرئيسي">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="8000">

        {{-- Indicators --}}
        <div class="carousel-indicators">
            @foreach($ScrollingOffers as $i => $offer)
            <button type="button" data-bs-target="#heroCarousel"
                    data-bs-slide-to="{{ $i }}"
                    class="{{ $i === 0 ? 'active' : '' }}"
                    aria-label="Slide {{ $i + 1 }}">
            </button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @forelse($ScrollingOffers as $i => $offer)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <div class="hero-slide">
                    <div class="container h-100">
                        <div class="row h-100 align-items-center">

                            {{-- النص --}}
                            <div class="col-md-5 order-2 order-md-1 hero-slide__content">
                                <h1 class="hero-slide__title">
                                    {{ $offer->scrollingoffer_headline }}
                                </h1>
                                <p class="hero-slide__subtitle">
                                    {{ $offer->scrollingoffer_description }}
                                </p>
                                @if($offer->scrollingoffer_url)
                                <a href="{{ $offer->scrollingoffer_url }}"
                                   class="btn hero__btn" target="_blank" rel="noopener">
                                    تسوق الآن
                                </a>
                                @endif
                            </div>

                            {{-- الصورة --}}
                            <div class="col-md-7 order-1 order-md-2 text-center">
                                {{-- موبايل --}}
                                @if($offer->scrollingoffer_imagemobile)
                                <img src="{{ asset('images/ScrollingOffer/' . $offer->scrollingoffer_imagemobile) }}"
                                     alt="{{ $offer->scrollingoffer_headline }}"
                                     class="hero-slide__img d-md-none"
                                     loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                                @endif
                                {{-- ديسكتوب --}}
                                <img src="{{ asset('images/ScrollingOffer/' . $offer->scrollingoffer_image) }}"
                                     alt="{{ $offer->scrollingoffer_headline }}"
                                     class="hero-slide__img {{ $offer->scrollingoffer_imagemobile ? 'd-none d-md-block' : '' }}"
                                     loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                     fetchpriority="{{ $i === 0 ? 'high' : 'auto' }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            @empty
            {{-- لو مفيش offers في الـ DB --}}
            <div class="carousel-item active">
                <div class="hero-slide">
                    <div class="container h-100">
                        <div class="row h-100 align-items-center justify-content-center">
                            <div class="col-md-6 text-center">
                                <h1 class="hero-slide__title">
                                    {{ $ecommerceSharedData['branch']->branch_name ?? 'مرحباً بك' }}
                                </h1>
                                <a href="{{ route('EcommerceAllProducts') }}" class="btn hero__btn">
                                    تسوق الآن
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        {{-- أزرار التنقل --}}
        <button class="carousel-control-prev hero-carousel-btn hero-carousel-btn--prev"
                type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <i class="bi bi-chevron-right"></i>
        </button>
        <button class="carousel-control-next hero-carousel-btn hero-carousel-btn--next"
                type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
</section>

{{-- ══════════════════════ BRANDS ══════════════════════ --}}
<section class="brands-bar" aria-label="الماركات الشريكة">
    <div class="container">
        <div class="brands-bar__track">
            @forelse($PartnerCompanies as $company)
            <div class="brands-bar__item">
                <img src="{{ asset('images/partnercompany/' . $company->maintenancecompany_image) }}"
                     alt="{{ $company->maintenancecompany_title }}"
                     loading="lazy" height="32">
            </div>
            @empty
            {{--
                لما ترجع الشغل:
                لو مفيش صور للـ PartnerCompanies، روح الـ admin
                وضيف صور للـ companies اللي عندها maintenancecompany_active = 1
            --}}
            @endforelse
        </div>
    </div>
</section>

{{-- ══════════════════════ الأكثر مبيعاً ══════════════════════ --}}
<section class="section-padding" aria-labelledby="bestsellers-title">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('EcommerceMostSaleProducts') }}" class="section-more-link">
                <i class="bi bi-arrow-right"></i>
                عرض المزيد
            </a>
            <h2 class="section-title mb-0" id="bestsellers-title">الأكثر مبيعاً</h2>
        </div>

        <div class="products-grid">
            @forelse($ThemostsellingEcommerceproducts as $ep)
            @include('components.product-card', [
                'id'    => $ep->ecommerceproduct_id,
                'name'  => $ep->product?->product_name  ?? '',
                'price' => $ep->product?->product_sellprice ?? 0,
                'offer_price' => $ep->product?->product_offerprice ?? null,
                'image' => $ep->product?->product_image ?? '',
                'route' => 'ProductDetails',
            ])
            @empty
            <p class="text-muted text-center py-5 col-span-full">لا توجد منتجات</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ══════════════════════ التصنيفات ══════════════════════ --}}
<section class="section-padding bg-var-secondary" aria-labelledby="categories-title">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('EcommerceAllCategories') }}" class="section-more-link">
                <i class="bi bi-arrow-right"></i>
                عرض المزيد
            </a>
            <h2 class="section-title mb-0" id="categories-title">التصنيفات</h2>
        </div>

        <div class="categories-grid">
            @forelse($Categories as $cat)
            <a href="{{ route('CategoryProduct', $cat->category_id) }}"
               class="category-card" aria-label="{{ $cat->category_name }}">
                @if($cat->category_image)
                <img src="{{ asset('images/category/' . $cat->category_image) }}"
                     alt="{{ $cat->category_name }}"
                     class="category-card__img" loading="lazy">
                @else
                {{-- Placeholder لو مفيش صورة --}}
                <div class="category-card__placeholder">
                    <i class="bi bi-grid"></i>
                </div>
                @endif
                <span class="category-card__name">{{ $cat->category_name }}</span>
            </a>
            @empty
            <p class="text-muted text-center py-5">لا توجد تصنيفات</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ══════════════════════ خصومات وعروض ══════════════════════ --}}
<section class="section-padding" aria-labelledby="offers-title">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('EcommerceProductsWithOffers') }}" class="section-more-link">
                <i class="bi bi-arrow-right"></i>
                عرض المزيد
            </a>
            <h2 class="section-title mb-0" id="offers-title">خصومات و عروض</h2>
        </div>

        <div class="products-grid">
            @forelse($Productswithoffersanddiscounts as $ep)
            @include('components.product-card', [
                'id'         => $ep->ecommerceproduct_id,
                'name'       => $ep->product?->product_name ?? '',
                'price'      => $ep->product?->product_offerprice ?? $ep->product?->product_sellprice ?? 0,
                'offer_price'=> $ep->product?->product_sellprice ?? null,
                'image'      => $ep->product?->product_image ?? '',
                'route'      => 'ProductDetails',
                'has_offer'  => true,
            ])
            @empty
            <p class="text-muted text-center py-5 col-span-full">لا توجد عروض حالياً</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($Productswithoffersanddiscounts->hasPages())
        <div class="d-flex justify-content-center gap-2 mt-4">
            <a href="{{ $Productswithoffersanddiscounts->previousPageUrl() ?? '#' }}"
               class="btn btn-outline-secondary btn-sm px-3"
               @if(!$Productswithoffersanddiscounts->onFirstPage()) aria-label="السابق" @endif>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ $Productswithoffersanddiscounts->nextPageUrl() ?? '#' }}"
               class="btn btn-outline-secondary btn-sm px-3"
               @if($Productswithoffersanddiscounts->hasMorePages()) aria-label="التالي" @endif>
                <i class="bi bi-chevron-left"></i>
            </a>
        </div>
        @endif
    </div>
</section>

{{-- ══════════════════════ عروض الصيانة ══════════════════════ --}}
@if($Offersfromtheowners->count())
<section class="section-padding bg-var-secondary" aria-labelledby="maintenance-offers-title">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('EcommerceOffers') }}" class="section-more-link">
                <i class="bi bi-arrow-right"></i>
                عرض المزيد
            </a>
            <h2 class="section-title mb-0" id="maintenance-offers-title">
                عروض الصيانة من {{ $ecommerceSharedData['branch']->branch_name ?? 'المتخصص' }}
            </h2>
        </div>

        @php
            $topOffers    = $Offersfromtheowners->take(2);
            $bottomOffers = $Offersfromtheowners->skip(2)->take(1);
        @endphp

        <div class="maintenance-grid">
            {{-- صورتين جنب بعض --}}
            <div class="maintenance-grid__top">
                @foreach($topOffers as $offer)
                <a href="{{ $offer->offerfromtheowner_url ?? '#' }}"
                   class="maintenance-card"
                   target="{{ $offer->offerfromtheowner_url ? '_blank' : '_self' }}"
                   rel="noopener">
                    <img src="{{ asset('images/Offersfromtheowner/' . $offer->offerfromtheowner_image) }}"
                         alt="{{ $offer->offerfromtheowner_headline }}"
                         loading="lazy">
                </a>
                @endforeach
            </div>

            {{-- صورة كبيرة تحتهم --}}
            @foreach($bottomOffers as $offer)
            <a href="{{ $offer->offerfromtheowner_url ?? '#' }}"
               class="maintenance-card maintenance-card--wide"
               target="{{ $offer->offerfromtheowner_url ? '_blank' : '_self' }}"
               rel="noopener">
                <img src="{{ asset('images/Offersfromtheowner/' . $offer->offerfromtheowner_image) }}"
                     alt="{{ $offer->offerfromtheowner_headline }}"
                     loading="lazy">
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════ Features Bar ══════════════════════ --}}
<section class="features-bar" aria-label="مميزاتنا">
    <div class="container">
        <div class="row g-4">
            @foreach([
                ['icon'=>'fas fa-credit-card', 'title'=>'وسائل الدفع',     'text'=>'فيزا، ماستركارد، فودافون كاش، إنستا باي'],
                ['icon'=>'fas fa-headset',     'title'=>'خدمة العملاء',    'text'=>'تواصل معنا 24 ساعة 7 أيام في الأسبوع'],
                ['icon'=>'fas fa-undo',        'title'=>'سياسة الاسترجاع', 'text'=>'وفقاً لقانون حماية المستهلك'],
                ['icon'=>'fas fa-lock',        'title'=>'نظام دفع آمن',    'text'=>'نضمن الدفع الآمن مع PVE'],
            ] as $feature)
            <div class="col-6 col-md-3">
                <div class="feature-item d-flex align-items-start gap-3 text-end">
                    <div class="feature-item__icon-wrap">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="feature-item__title">{{ $feature['title'] }}</div>
                        <div class="feature-item__text">{{ $feature['text'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

{{-- resources/views/components/product-card.blade.php --}}
@props([
    'id'         => 0,
    'name'       => '',
    'price'      => 0,
    'offer_price'=> null,
    'image'      => '',
    'route'      => 'ProductDetails',
    'has_offer'  => false,
])

<article class="product-card">

    {{-- Badge الخصم --}}
    @if($has_offer && $offer_price && $offer_price > $price)
    <span class="product-card__badge">
        %{{ round((($offer_price - $price) / $offer_price) * 100) }}
    </span>
    @endif

    {{-- الصورة --}}
    <a href="{{ route($route, $id) }}" class="product-card__img-wrap">
        @if($image)
        <img src="{{ asset('images/products/' . $image) }}"
             alt="{{ $name }}"
             class="product-card__img"
             loading="lazy">
        @else
        <div class="product-card__no-img">
            <i class="bi bi-image"></i>
        </div>
        @endif
    </a>

    {{-- البيانات --}}
    <div class="product-card__body">
        <h3 class="product-card__name">
            <a href="{{ route($route, $id) }}">{{ $name }}</a>
        </h3>

        <div class="product-card__prices">
            <span class="product-card__price {{ $has_offer ? 'text-danger' : '' }}">
                {{ number_format($price) }} ج.م
            </span>
            @if($has_offer && $offer_price && $offer_price > $price)
            <span class="product-card__old-price">
                {{ number_format($offer_price) }}
            </span>
            @endif
        </div>

        <div class="product-card__actions">
            <a href="{{ route($route, $id) }}"
               class="btn product-card__add-btn">
                <i class="bi bi-bag"></i>
                أضف الى السلة
            </a>
            <button class="btn product-card__wish-btn" type="button" aria-label="المفضلة">
                <i class="bi bi-heart"></i>
            </button>
        </div>
    </div>

</article>



/* resources/css/home.css */

/* ── Hero ── */
.hero-section { background: var(--bg-secondary); overflow: hidden; }

.hero-slide {
    min-height: 560px;
    display: flex;
    align-items: center;
    padding-block: 2rem;
}

.hero-slide__content { padding-block: 2rem; }

.hero-slide__title {
    font-size: clamp(1.4rem, 3.5vw, 2.5rem);
    font-weight: 800;
    color: var(--heading);
    line-height: 1.4;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hero-slide__subtitle {
    font-size: clamp(.85rem, 1.5vw, 1rem);
    color: var(--text);
    line-height: 1.8;
    margin-bottom: 1.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hero-slide__img {
    max-height: 480px;
    max-width: 100%;
    object-fit: contain;
}

.hero-carousel-btn {
    width: 40px !important;
    height: 40px !important;
    background: rgba(255,255,255,.85) !important;
    border-radius: 50% !important;
    opacity: 1 !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-sm);
    transition: background var(--transition);
}
.hero-carousel-btn:hover { background: var(--white) !important; }
.hero-carousel-btn i { color: var(--heading); font-size: 1rem; }
.hero-carousel-btn--prev { right: 1rem !important; left: auto !important; }
.hero-carousel-btn--next { left: 1rem !important;  right: auto !important; }

.carousel-indicators button {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: rgba(0,0,0,.25);
    border: none;
    transition: background var(--transition), transform var(--transition);
}
.carousel-indicators button.active {
    background: var(--primary);
    transform: scale(1.3);
}

/* ── Brands ── */
.brands-bar {
    border-block: 1px solid var(--stroke);
    padding-block: 1.5rem;
    background: var(--white);
}
.brands-bar__track {
    display: flex;
    align-items: center;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 1rem;
}
.brands-bar__item img {
    max-height: 32px;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: .7;
    transition: filter var(--transition), opacity var(--transition);
}
.brands-bar__item:hover img { filter: grayscale(0); opacity: 1; }

/* ── Section Helpers ── */
.section-padding   { padding-block: 3rem; }
.section-title     { font-size: clamp(1.2rem, 2.5vw, 1.6rem); font-weight: 700; color: var(--heading); }
.section-more-link { font-size: .9rem; color: var(--text); display: flex; align-items: center; gap: .4rem; transition: color var(--transition); }
.section-more-link:hover { color: var(--primary); }

/* ── Products Grid ── */
.products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
}
@media (max-width: 991px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 575px) { .products-grid { grid-template-columns: repeat(2, 1fr); } }

/* ── Product Card ── */
.product-card {
    position: relative;
    border: 1px solid var(--stroke);
    border-radius: 16px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: var(--white);
    transition: box-shadow var(--transition), transform var(--transition);
}
.product-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }

.product-card__badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: var(--red);
    color: var(--white);
    font-size: .75rem;
    font-weight: 700;
    min-width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.product-card__img-wrap { display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: var(--radius-md); aspect-ratio: 1; }
.product-card__img { width: 100%; height: 100%; object-fit: contain; transition: transform .35s ease; }
.product-card__img-wrap:hover .product-card__img { transform: scale(1.04); }
.product-card__no-img { width: 100%; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); border-radius: var(--radius-md); color: var(--text); font-size: 2rem; }

.product-card__body { display: flex; flex-direction: column; gap: 8px; }
.product-card__name { font-size: .83rem; font-weight: 500; color: var(--heading); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0; }
.product-card__name a:hover { color: var(--primary); }

.product-card__prices { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.product-card__price { font-size: .95rem; font-weight: 700; color: var(--heading); }
.product-card__old-price { font-size: .8rem; color: var(--text); text-decoration: line-through; }

.product-card__actions { display: flex; gap: 8px; }
.product-card__add-btn { flex: 1; background: var(--white); border: 1px solid var(--stroke); color: var(--heading); font-size: .82rem; font-weight: 600; border-radius: var(--radius-md); padding: .4rem .6rem; display: flex; align-items: center; justify-content: center; gap: 5px; transition: background var(--transition), color var(--transition), border-color var(--transition); }
.product-card__add-btn:hover { background: var(--primary); color: var(--white); border-color: var(--primary); }
.product-card__wish-btn { border: 1px solid var(--stroke); color: var(--text); border-radius: var(--radius-md); padding: .4rem .55rem; transition: color var(--transition), border-color var(--transition); }
.product-card__wish-btn:hover { color: var(--red); border-color: var(--red); }

/* ── Categories Grid ── */
.categories-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem; }
@media (max-width: 991px) { .categories-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 575px) { .categories-grid { grid-template-columns: repeat(3, 1fr); } }

.category-card { position: relative; border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 1; display: block; }
.category-card__img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
.category-card:hover .category-card__img { transform: scale(1.05); }
.category-card__placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); color: var(--text); font-size: 2rem; }
.category-card__name { position: absolute; bottom: 0; right: 0; left: 0; padding: .5rem; background: rgba(0,0,0,.5); color: var(--white); font-size: .85rem; font-weight: 600; text-align: center; }

/* ── Maintenance Grid ── */
.maintenance-grid { display: flex; flex-direction: column; gap: 1rem; }
.maintenance-grid__top { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.maintenance-card { display: block; border-radius: var(--radius-md); overflow: hidden; }
.maintenance-card img { width: 100%; height: 200px; object-fit: cover; transition: transform .35s ease; }
.maintenance-card--wide img { height: 280px; }
.maintenance-card:hover img { transform: scale(1.02); }

/* ── Features Bar ── */
.features-bar { padding-block: 2.5rem; border-top: 1px solid var(--stroke); }
.feature-item__icon-wrap { font-size: 1.8rem; color: var(--primary); flex-shrink: 0; }
.feature-item__title { font-weight: 700; font-size: .95rem; color: var(--heading); margin-bottom: .2rem; }
.feature-item__text { font-size: .82rem; color: var(--text); line-height: 1.5; }

/* ── Responsive ── */
@media (max-width: 767px) {
    .hero-slide { min-height: 420px; padding-block: 1.5rem; }
    .hero-slide__title { -webkit-line-clamp: 2; }
    .hero-slide__subtitle { -webkit-line-clamp: 3; }
    .hero-slide__img { max-height: 220px; }
    .hero-carousel-btn { width: 32px !important; height: 32px !important; }
    .maintenance-grid__top { grid-template-columns: 1fr; }
    .maintenance-card img { height: 160px; }
    .maintenance-card--wide img { height: 200px; }
    .features-bar .col-6 { margin-bottom: .5rem; }
}
---$_COOKIEتأكد إن الـ compact() فيه:

$ScrollingOffers — من ScrollingOffer::where('scrollingoffer_active', '1')->get()
$PartnerCompanies — من MaintenanceCompany::where('maintenancecompany_active', '1')->whereNotNull('maintenancecompany_image')->get()
$ThemostsellingEcommerceproducts — مع ->with('product')
$Categories — من Category::where('category_appearonhomepage', '1')->take(6)->get()
$Productswithoffersanddiscounts — مع ->paginate(4)
$Offersfromtheowners — من Offersfromtheowner::where('offerfromtheowner_active', '1')->take(3)->get()
----
// app/Models/EcommerceProduct.php
public function product()
{
    return $this->belongsTo(Product::class, 'product_id', 'product_id');
}