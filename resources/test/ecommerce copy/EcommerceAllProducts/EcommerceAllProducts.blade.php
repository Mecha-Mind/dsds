@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - جميع المنتجات';
    $description =
        'تصفح جميع المنتجات المتوفرة في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        ' بأسعار مميزة وجودة عالية.';
    $keywords = 'منتجات, ' . $ecommerceSharedData['branch']->branch_name . ', تسوق, عروض, أسعار, منتجات أصلية';
    $og_title = $ecommerceSharedData['branch']->branch_name . ' - جميع المنتجات';
    $og_description =
        'اكتشف أفضل المنتجات والعروض في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        '، جودة وأسعار تنافسية بانتظارك!';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';

@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)

@section('title', 'المنتجات — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

    {{-- Page Header --}}
    <x-page-header title="المنتجات" :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'المنتجات', 'url' => route('EcommerceAllProducts')],
    ]" />


    {{-- Search Result Indicator --}}
    @if (request()->filled('search'))
        <div class="search-indicator container mt-3">
            <div class="search-indicator__wrap">
                <span class="search-indicator__text">
                    <i class="bi bi-search me-1"></i>
                    نتائج البحث عن:
                    <strong>"{{ request('search') }}"</strong>
                    <span class="search-indicator__count">
                        ({{ $Products->total() }} نتيجة)
                    </span>
                </span>
                <a href="{{ route('EcommerceAllProducts') }}" class="search-indicator__clear" aria-label="Clear search">
                    <i class="bi bi-x-circle me-1"></i>
                    مسح البحث
                </a>
            </div>
        </div>
    @endif

    {{-- Brands Bar --}}
    @include('components.brand-logos')


    <div class="container py-4">
        <div class="row g-4">

            {{-- ════════════════════════════════
             Sidebar — الفلترة
        ════════════════════════════════ --}}
            <aside class="col-lg-3 d-none d-lg-block" aria-label="فلاتر المنتجات">
                <form method="GET" action="{{ route('EcommerceAllProducts') }}" id="filtersForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    {{-- ── متاح في الفروع ── --}}
                    @if ($branches->count())
                        <div class="filter-card mb-3">
                            <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                                data-bs-target="#filterBranchs" aria-expanded="false" aria-controls="filterBranchs">
                                <span>متاح في الفروع</span>
                                <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                            </button>
                            <div class="collapse filter-card__body" id="filterBranchs">
                                @foreach ($branches as $b)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="branchs[]"
                                            value="{{ $b->branch_id }}" id="branch_{{ $b->branch_id }}"
                                            {{ in_array($b->branch_id, request('branchs', [])) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <label class="form-check-label" for="branch_{{ $b->branch_id }}">
                                            {{ $b->branch_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── الماركة ── --}}
                    @if ($FilterBrands->count())
                        <div class="filter-card mb-3">
                            <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                                data-bs-target="#filterBrand" aria-expanded="false" aria-controls="filterBrand">
                                <span>الماركة</span>
                                <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                            </button>
                            <div class="collapse filter-card__body" id="filterBrand">
                                @foreach ($FilterBrands as $b)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="maintenancecompanies[]"
                                            value="{{ $b->maintenancecompany_id }}"
                                            id="brand_{{ $b->maintenancecompany_id }}"
                                            {{ in_array($b->maintenancecompany_id, request('maintenancecompanies', [])) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <label class="form-check-label" for="brand_{{ $b->maintenancecompany_id }}">
                                            {{ $b->maintenancecompany_title }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── السعر ── --}}
                    <div class="filter-card mb-3">
                        <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterPrice" aria-expanded="true" aria-controls="filterPrice">
                            <span>السعر</span>
                            <i class="bi bi-chevron-up filter-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse show filter-card__body" id="filterPrice">
                            <div class="d-flex justify-content-between mb-2 small fw-semibold">
                                <span id="priceValMin">{{ number_format($minPrice) }} ج.م</span>
                                <span id="priceValMax">{{ number_format(request('max_price', $maxPrice)) }} ج.م</span>
                            </div>
                            <input type="range" class="price-range w-100" name="max_price" min="{{ $minPrice }}"
                                max="{{ $maxPrice }}" value="{{ request('max_price', $maxPrice) }}"
                                aria-label="الحد الأقصى للسعر"
                                oninput="document.getElementById('priceValMax').textContent = Number(this.value).toLocaleString('ar-EG') + ' ج.م'"
                                onchange="this.form.submit()">
                        </div>
                    </div>

                    {{-- ── اللون ──
                         لو $usingColorPlaceholder = true → ألوان placeholder (swatches)
                         لو false → ألوان حقيقية من الـ DB (checkboxes)
                         ← لما تتضاف قيم في product_color هيتحول تلقائياً للـ checkboxes
                    --}}
                    <div class="filter-card mb-3">
                        <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterColor" aria-expanded="false" aria-controls="filterColor">
                            <span>الألوان</span>
                            <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse filter-card__body" id="filterColor">
                            @if ($usingColorPlaceholder)
                                {{--
                                Placeholder Swatches — بيظهروا لحد ما تتضاف ألوان في الـ DB
                                ← لما تتضاف في product_color هيختفوا تلقائياً
                            --}}
                                <div class="color-swatches" role="group" aria-label="اختر لون">
                                    <input type="hidden" name="color" id="colorInput"
                                        value="{{ request('color', '') }}">
                                    @foreach ($availableColors as $c)
                                        <button type="button"
                                            class="color-swatch {{ request('color') === $c['val'] ? 'is-active' : '' }}"
                                            style="background: {{ $c['hex'] }}" title="{{ $c['val'] }}"
                                            aria-label="{{ $c['val'] }}"
                                            aria-pressed="{{ request('color') === $c['val'] ? 'true' : 'false' }}"
                                            onclick="selectColor('{{ $c['val'] }}')">
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                {{-- ألوان حقيقية من الـ DB --}}
                                @foreach ($availableColors as $color)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="color[]"
                                            value="{{ $color }}" id="color_{{ $loop->index }}"
                                            {{ in_array($color, request('color', [])) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <label class="form-check-label" for="color_{{ $loop->index }}">
                                            {{ $color }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- ── المساحة الداخلية ──
                         ← لما يتضاف product_storage في الـ DB:
                           1. في الـ Controller: أزل تعليق $availableStorage
                           2. نفس اللحظة $storagePlaceholders هيتجاهل لأن $availableStorage مش هيكون فاضي
                    --}}
                    <div class="filter-card mb-3">
                        <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterStorage" aria-expanded="false" aria-controls="filterStorage">
                            <span>المساحة الداخلية</span>
                            <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse filter-card__body" id="filterStorage">
                            @php $storageItems = !empty($availableStorage) ? $availableStorage : $storagePlaceholders; @endphp
                            @foreach ($storageItems as $s)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="storage[]"
                                        value="{{ $s }}" id="s_{{ $loop->index }}"
                                        {{ in_array($s, request('storage', [])) ? 'checked' : '' }}
                                        @if (empty($availableStorage)) disabled @endif onchange="this.form.submit()">
                                    <label class="form-check-label {{ empty($availableStorage) ? 'text-muted' : '' }}"
                                        for="s_{{ $loop->index }}">
                                        {{ $s }}
                                    </label>
                                </div>
                            @endforeach
                            @if (empty($availableStorage))
                                <small class="text-muted d-block mt-1" style="font-size: .72rem">
                                    قريباً
                                </small>
                            @endif
                        </div>
                    </div>

                    {{-- ── سعة الرامات ──
                         ← لما يتضاف product_ram في الـ DB:
                           1. في الـ Controller: أزل تعليق $availableRam
                           2. أزل تعليق if ($request->filled('ram'))
                    --}}
                    <div class="filter-card mb-3">
                        <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterRam" aria-expanded="false" aria-controls="filterRam">
                            <span>سعة الرامات</span>
                            <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse filter-card__body" id="filterRam">
                            @php $ramItems = !empty($availableRam) ? $availableRam : $ramPlaceholders; @endphp
                            @foreach ($ramItems as $r)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ram[]"
                                        value="{{ $r }}" id="r_{{ $loop->index }}"
                                        {{ in_array($r, request('ram', [])) ? 'checked' : '' }}
                                        @if (empty($availableRam)) disabled @endif onchange="this.form.submit()">
                                    <label class="form-check-label {{ empty($availableRam) ? 'text-muted' : '' }}"
                                        for="r_{{ $loop->index }}">
                                        {{ $r }}
                                    </label>
                                </div>
                            @endforeach
                            @if (empty($availableRam))
                                <small class="text-muted d-block mt-1" style="font-size: .72rem">قريباً</small>
                            @endif
                        </div>
                    </div>

                    {{-- ── البروسيسور ──
                         ← لما يتضاف product_cpu في الـ DB
                    --}}
                    <div class="filter-card mb-3">
                        <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterCPU" aria-expanded="false" aria-controls="filterCPU">
                            <span>البروسيسور (CPU)</span>
                            <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse filter-card__body" id="filterCPU">
                            @php $cpuItems = !empty($availableCpu) ? $availableCpu : $cpuPlaceholders; @endphp
                            @foreach ($cpuItems as $c)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cpu[]"
                                        value="{{ $c }}" id="cpu_{{ $loop->index }}"
                                        {{ in_array($c, request('cpu', [])) ? 'checked' : '' }}
                                        @if (empty($availableCpu)) disabled @endif onchange="this.form.submit()">
                                    <label class="form-check-label {{ empty($availableCpu) ? 'text-muted' : '' }}"
                                        for="cpu_{{ $loop->index }}">
                                        {{ $c }}
                                    </label>
                                </div>
                            @endforeach
                            @if (empty($availableCpu))
                                <small class="text-muted d-block mt-1" style="font-size: .72rem">قريباً</small>
                            @endif
                        </div>
                    </div>

                    {{-- ── الشاحن ──
                         ← لما يتضاف product_charger في الـ DB
                    --}}
                    <div class="filter-card mb-3">
                        <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                            data-bs-target="#filterCharger" aria-expanded="false" aria-controls="filterCharger">
                            <span>الشاحن</span>
                            <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse filter-card__body" id="filterCharger">
                            @php $chargerItems = !empty($availableCharger) ? $availableCharger : $chargerPlaceholders; @endphp
                            @foreach ($chargerItems as $ch)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="charger[]"
                                        value="{{ $ch }}" id="ch_{{ $loop->index }}"
                                        {{ in_array($ch, request('charger', [])) ? 'checked' : '' }}
                                        @if (empty($availableCharger)) disabled @endif onchange="this.form.submit()">
                                    <label class="form-check-label {{ empty($availableCharger) ? 'text-muted' : '' }}"
                                        for="ch_{{ $loop->index }}">
                                        {{ $ch }}
                                    </label>
                                </div>
                            @endforeach
                            @if (empty($availableCharger))
                                <small class="text-muted d-block mt-1" style="font-size: .72rem">قريباً</small>
                            @endif
                        </div>
                    </div>

                    {{-- ── التصنيف الفرعي ── --}}
                    @if ($FilterSubcategories->count())
                        <div class="filter-card mb-3">
                            <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                                data-bs-target="#filterSubcat" aria-expanded="false" aria-controls="filterSubcat">
                                <span>التصنيف</span>
                                <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                            </button>
                            <div class="collapse filter-card__body" id="filterSubcat">
                                @foreach ($FilterSubcategories as $sub)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subcategory[]"
                                            value="{{ $sub->subcategory_id }}" id="subcat_{{ $sub->subcategory_id }}"
                                            {{ in_array($sub->subcategory_id, request('subcategory', [])) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <label class="form-check-label" for="subcat_{{ $sub->subcategory_id }}">
                                            {{ $sub->subcategory_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── الموديل ── --}}
                    @if ($FilterModels->count())
                        <div class="filter-card mb-3">
                            <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                                data-bs-target="#filterModel" aria-expanded="false" aria-controls="filterModel">
                                <span>الموديل</span>
                                <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                            </button>
                            <div class="collapse filter-card__body" id="filterModel">
                                @foreach ($FilterModels as $m)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="model[]"
                                            value="{{ $m->maintenancemodel_id }}"
                                            id="model_{{ $m->maintenancemodel_id }}"
                                            {{ in_array($m->maintenancemodel_id, request('model', [])) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <label class="form-check-label" for="model_{{ $m->maintenancemodel_id }}">
                                            {{--
                                        ← تأكد من اسم الـ column في maintenancemodels
                                        لو الاسم مختلف غيّره هنا
                                    --}}
                                            {{ $m->maintenancemodel_title ?? ($m->maintenancemodel_name ?? '—') }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── نوع الجهاز ── --}}
                    @if ($FilterMaintenanceCategories->count())
                        <div class="filter-card mb-3">
                            <button type="button" class="filter-card__header w-100" data-bs-toggle="collapse"
                                data-bs-target="#filterMCat" aria-expanded="false" aria-controls="filterMCat">
                                <span>نوع الجهاز</span>
                                <i class="bi bi-chevron-down filter-chevron" aria-hidden="true"></i>
                            </button>
                            <div class="collapse filter-card__body" id="filterMCat">
                                @foreach ($FilterMaintenanceCategories as $mc)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="maintenancecategory[]"
                                            value="{{ $mc->maintenancecategory_id }}"
                                            id="mcat_{{ $mc->maintenancecategory_id }}"
                                            {{ in_array($mc->maintenancecategory_id, request('maintenancecategory', [])) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <label class="form-check-label" for="mcat_{{ $mc->maintenancecategory_id }}">
                                            {{--
                                        ← تأكد من اسم الـ column في maintenancecategories
                                    --}}
                                            {{ $mc->maintenancecategory_title ?? '—' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ── إعادة الضبط ── --}}
                    @if (request()->hasAny([
                            'search',
                            'category',
                            'maintenancecompanies',
                            'max_price',
                            'color',
                            'sort',
                            'branchs',
                            'subcategory',
                            'model',
                            'maintenancecategory',
                            'ram',
                            'storage',
                            'cpu',
                            'charger',
                        ]))
                        <a href="{{ route('EcommerceAllProducts') }}" class="btn btn-outline-danger btn-sm w-100 mb-3">
                            <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                            إعادة ضبط الفلترة
                        </a>
                    @endif

                </form>
            </aside>

            {{-- ════════════════════════════════
             Main Content — المنتجات
        ════════════════════════════════ --}}
            <div class="col-lg-9 col-12 products-section">

                {{-- Toolbar --}}
                <div class="products-toolbar d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                    {{-- اليسار: الترتيب --}}
                    <div class="d-flex align-items-center gap-2 filter-select">
                        <select class="form-select form-select-sm toolbar-sort" name="sort" form="filtersForm"
                            aria-label="ترتيب المنتجات" onchange="document.getElementById('filtersForm').submit()">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>الأكثر مبيعا
                            </option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>السعر:
                                من
                                الأقل</option>
                            <option value="price_desc"{{ request('sort') === 'price_desc' ? 'selected' : '' }}>السعر:
                                من
                                الأعلى</option>
                        </select>
                    </div>

                    {{-- اليمين: العدد + View Toggle + فلتر موبايل --}}
                    <div class="d-flex align-items-center gap-2">


                        {{-- Desktop + Tablet --}}
                        <div class="view-toggle d-none d-md-flex">
                            <button class="view-btn" id="gridViewBtn" title="شبكة">
                                <i class="bi bi-grid"></i>
                            </button>
                            <button class="view-btn" id="listViewBtn" title="قائمة">
                                <i class="bi bi-list-ul"></i>
                            </button>
                        </div>

                        {{-- Mobile فقط --}}
                        <div class="view-toggle d-flex d-md-none">
                            <button class="view-btn" id="gridView2Btn" title="عمودين">
                                <i class="bi bi-grid"></i>
                            </button>
                            <button class="view-btn" id="gridView1Btn" title="عمود واحد">
                                <i class="bi bi-list-ul"></i>
                            </button>
                        </div>



                    </div>

                    {{-- فلتر موبايل --}}
                    <div class="d-flex align-items-center gap-2 filter-btn">


                        {{-- فلتر موبايل --}}
                        <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#mobileFilters">
                            <i class="bi bi-funnel"></i>
                            فلترة
                        </button>

                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="products-grid" id="productsGrid">

                    @forelse($Products as $ep)
                        <x-product-card :id="$ep->ecommerceproduct_id" :name="$ep->product?->product_name ?? ''" :price="$ep->product?->product_sellprice ?? 0" :offerPrice="$ep->product?->product_offerprice ?? null"
                            :image="$ep->product?->product_image ?? ''" route="ProductDetails" :hasOffer="($ep->product?->product_offerprice ?? 0) > 0 &&
                                $ep->product?->product_offerprice < $ep->product?->product_sellprice" />
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-search" style="font-size: 3rem; color: var(--text)"></i>
                            <p class="mt-3 text-muted">لا توجد منتجات</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($Products->hasPages())
                    <div class="products-pagination mt-4 d-flex justify-content-center align-items-center gap-2 flex-wrap">

                        @if ($Products->onFirstPage())
                            <button class="pagination-btn" disabled>السابق</button>
                        @else
                            <a href="{{ $Products->previousPageUrl() }}" class="pagination-btn"
                                id="prevPage">السابق</a>
                        @endif

                        @foreach ($Products->getUrlRange(1, $Products->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                                class="pagination-btn {{ $page === $Products->currentPage() ? 'active' : '' }}"
                                id="activePage">
                                {{ $page }}
                            </a>
                        @endforeach

                        @if ($Products->hasMorePages())
                            <a href="{{ $Products->nextPageUrl() }}" class="pagination-btn" id="nextPage">التالي</a>
                        @else
                            <button class="pagination-btn" disabled>التالي</button>
                        @endif

                    </div>
                @endif

            </div>
        </div>
    </div>


    {{-- Mobile Offcanvas --}}
    <div class="offcanvas offcanvas-end" id="mobileFilters" tabindex="-1">
        <div class="offcanvas-header border-bottom">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            <h5 class="offcanvas-title mb-0">الفلترة</h5>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ route('EcommerceAllProducts') }}">
                <div class="filter-card mb-3">
                    <div class="filter-card__header">التصنيفات</div>
                    <div class="filter-card__body">
                        @foreach ($FilterCategories as $cat)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="category[]"
                                    value="{{ $cat->category_id }}"
                                    {{ in_array($cat->category_id, request('category', [])) ? 'checked' : '' }}>
                                <label class="form-check-label d-flex justify-content-between">
                                    <span>{{ $cat->category_name }}</span>
                                    <span class="filter-count">{{ $cat->ecommerce_products_count }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="filter-card mb-3">
                    <div class="filter-card__header">الشركة</div>
                    <div class="filter-card__body">
                        @foreach ($FilterBrands as $brand)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="brand[]"
                                    value="{{ $brand->maintenancecompany_id }}"
                                    {{ in_array($brand->maintenancecompany_id, request('brand', [])) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $brand->maintenancecompany_title }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="btn hero__btn w-100 mt-3" aria-label="filter">
                    <i class="bi bi-funnel me-1"></i>
                    تطبيق الفلترة
                </button>
                @if (request()->hasAny(['category', 'brand', 'max_price', 'color', 'warranty', 'search']))
                    <a href="{{ route('EcommerceAllProducts') }}" class="btn btn-outline-danger w-100 mt-2"
                        id="filterReset">
                        إعادة الضبط
                    </a>
                @endif
            </form>
        </div>
    </div>


@endsection


@push('scripts')
    <script>
        (function() {
            'use strict';

            const grid = document.getElementById('productsGrid');
            if (!grid) return;

            // ══════════════════════════════════════
            // View Toggle
            // ══════════════════════════════════════
            const VIEWS = {
                GRID: '',
                LIST: 'products-grid--list',
                SINGLE: 'products-grid--single',
            };

            function setView(key) {
                Object.values(VIEWS).forEach(cls => cls && grid.classList.remove(cls));
                if (VIEWS[key]) grid.classList.add(VIEWS[key]);

                const activeMap = {
                    GRID: ['gridViewBtn', 'gridView2Btn'],
                    LIST: ['listViewBtn'],
                    SINGLE: ['gridView1Btn']
                };
                ['gridViewBtn', 'listViewBtn', 'gridView2Btn', 'gridView1Btn'].forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-pressed', 'false');
                    }
                });
                (activeMap[key] || []).forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) {
                        btn.classList.add('active');
                        btn.setAttribute('aria-pressed', 'true');
                    }
                });

                localStorage.setItem('productView', key);
            }

            setView(localStorage.getItem('productView') || 'GRID');

            const btnMap = {
                gridViewBtn: 'GRID',
                listViewBtn: 'LIST',
                gridView2Btn: 'GRID',
                gridView1Btn: 'SINGLE'
            };
            Object.entries(btnMap).forEach(([id, key]) => {
                const btn = document.getElementById(id);
                if (btn) btn.addEventListener('click', () => setView(key));
            });

            // ══════════════════════════════════════
            // Collapse Chevron — إصلاح الـ Glitch
            // السبب: Bootstrap بيشتغل بـ CSS transition وبيكون في conflict
            // الحل: نستخدم shown.bs.collapse و hidden.bs.collapse بدل show و hide
            // ══════════════════════════════════════
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
                const targetId = btn.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);
                const chevron = btn.querySelector('.filter-chevron');
                if (!target || !chevron) return;

                /*
                 | shown.bs.collapse  = بيشتغل بعد ما الـ collapse خلص فتح
                 | hidden.bs.collapse = بيشتغل بعد ما الـ collapse خلص قفل
                 | ده بيحل مشكلة الـ glitch لأننا بنغير الـ icon بعد الـ animation
                */
                target.addEventListener('shown.bs.collapse', () => {
                    chevron.classList.replace('bi-chevron-down', 'bi-chevron-up');
                    btn.setAttribute('aria-expanded', 'true');
                });
                target.addEventListener('hidden.bs.collapse', () => {
                    chevron.classList.replace('bi-chevron-up', 'bi-chevron-down');
                    btn.setAttribute('aria-expanded', 'false');
                });
            });

            // ══════════════════════════════════════
            // Color Swatch Selection
            // ══════════════════════════════════════
            window.selectColor = function(val) {
                const input = document.getElementById('colorInput');
                if (!input) return;

                const currentVal = input.value;
                const isDeselect = currentVal === val;
                input.value = isDeselect ? '' : val;

                document.querySelectorAll('.color-swatch').forEach(btn => {
                    const isThis = btn.getAttribute('title') === val;
                    btn.classList.toggle('is-active', isThis && !isDeselect);
                    btn.setAttribute('aria-pressed', (isThis && !isDeselect).toString());
                });

                document.getElementById('filtersForm').submit();
            };

        })();
    </script>
@endpush
