// في EcommerceWelcomeController::index()
$ScrollingOffers = ScrollingOffer::where('scrollingoffer_active', '1')
    ->orderBy('updated_at', 'desc')
    ->get();

// مش بنجيب first() بس — بنبعت الكل للـ view
// وبنجيب أول واحد كـ hero default
$hero = [
    'title'   => $ScrollingOffers->first()?->scrollingoffer_headline   ?? '',
    'subtitle'=> $ScrollingOffers->first()?->scrollingoffer_description ?? '',
    'image'   => $ScrollingOffers->first()?->scrollingoffer_image        ?? '',
    'btnLink' => $ScrollingOffers->first()?->scrollingoffer_url          ?? route('EcommerceAllProducts'),
    'btnText' => 'تسوق الآن',
];

// بنبعتهم للـ view مع بعض
return view('welcome', compact(
    'ScrollingOffers',
    'hero',
    // ... باقي الـ variables
));

----
{{-- ══════════════════════ HERO ══════════════════════ --}}
<section class="hero-section" aria-label="البانر الرئيسي">

    {{-- Bootstrap Carousel --}}
    <div id="heroCarousel"
         class="carousel slide"
         data-bs-ride="carousel"
         data-bs-interval="8000">

        {{-- Indicators --}}
        <div class="carousel-indicators">
            @foreach($ScrollingOffers as $i => $offer)
            <button type="button"
                    data-bs-target="#heroCarousel"
                    data-bs-slide-to="{{ $i }}"
                    class="{{ $i === 0 ? 'active' : '' }}"
                    aria-label="Slide {{ $i + 1 }}">
            </button>
            @endforeach
        </div>

        {{-- Slides --}}
        <div class="carousel-inner">
            @foreach($ScrollingOffers as $i => $offer)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                <div class="hero-slide">
                    <div class="container h-100">
                        <div class="row h-100 align-items-center">

                            {{-- النص --}}
                            <div class="col-lg-5 col-md-6 order-2 order-md-1 hero-slide__content">
                                <h1 class="hero-slide__title">
                                    {{ $offer->scrollingoffer_headline }}
                                </h1>
                                <p class="hero-slide__subtitle">
                                    {{ $offer->scrollingoffer_description }}
                                </p>
                                @if($offer->scrollingoffer_url)
                                <a href="{{ $offer->scrollingoffer_url }}"
                                   class="btn hero__btn"
                                   target="_blank" rel="noopener">
                                    تسوق الآن
                                </a>
                                @endif
                            </div>

                            {{-- الصورة --}}
                            <div class="col-lg-7 col-md-6 order-1 order-md-2 text-center">
                                {{-- صورة الموبايل --}}
                                @if($offer->scrollingoffer_imagemobile)
                                <img src="{{ asset('images/ScrollingOffer/' . $offer->scrollingoffer_imagemobile) }}"
                                     alt="{{ $offer->scrollingoffer_headline }}"
                                     class="hero-slide__img d-md-none"
                                     loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                     fetchpriority="{{ $i === 0 ? 'high' : 'auto' }}">
                                @endif
                                {{-- صورة الديسكتوب --}}
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
            @endforeach
        </div>

        {{-- أزرار التنقل --}}
        <button class="carousel-control-prev hero-carousel-btn hero-carousel-btn--prev"
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide="prev"
                aria-label="السابق">
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
        </button>
        <button class="carousel-control-next hero-carousel-btn hero-carousel-btn--next"
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide="next"
                aria-label="التالي">
            <i class="bi bi-chevron-left" aria-hidden="true"></i>
        </button>

    </div>
</section>
-------
/* ── Hero Section ── */
.hero-section {
    background: var(--bg-secondary);
    overflow: hidden;
}

.hero-slide {
    min-height: 560px;
    display: flex;
    align-items: center;
    padding-block: 2rem;
}

/* النص محجوم عشان ميكسرش الديزاين لو الكلام زاد */
.hero-slide__content {
    padding-block: 2rem;
}

.hero-slide__title {
    /*
     | clamp(minimum, preferred, maximum)
     | بيبدأ من 1.4rem ويكبر مع الشاشة لحد 2.5rem
     | مش هيطلع برا الـ container حتى لو الكلام طويل
    */
    font-size: clamp(1.4rem, 3.5vw, 2.5rem);
    font-weight: 800;
    color: var(--heading);
    line-height: 1.4;
    margin-bottom: 1rem;
    /* لو الكلام أطول من 3 سطور بيتقطع */
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
    /* لو الكلام أطول من 4 سطور بيتقطع */
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hero-slide__img {
    max-height: 480px;
    max-width: 100%;
    object-fit: contain;
    width: auto;
}

/* أزرار التنقل */
.hero-carousel-btn {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,.85) !important;
    border-radius: 50% !important;
    top: 50%;
    transform: translateY(-50%);
    opacity: 1 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-sm);
    transition: background var(--transition);
}

.hero-carousel-btn:hover {
    background: var(--white) !important;
}

.hero-carousel-btn i {
    color: var(--heading);
    font-size: 1rem;
}

.hero-carousel-btn--prev { right: 1rem; left: auto; }
.hero-carousel-btn--next { left: 1rem;  right: auto; }

/* Indicators */
.carousel-indicators button {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(0,0,0,.3);
    border: none;
    transition: background var(--transition), transform var(--transition);
}

.carousel-indicators button.active {
    background: var(--primary);
    transform: scale(1.3);
}

/* Responsive */
@media (max-width: 767px) {
    .hero-slide { min-height: 420px; padding-block: 1.5rem; }
    .hero-slide__title { -webkit-line-clamp: 2; }
    .hero-slide__subtitle { -webkit-line-clamp: 3; }
    .hero-slide__img { max-height: 220px; }
    .hero-carousel-btn { width: 32px; height: 32px; }
}