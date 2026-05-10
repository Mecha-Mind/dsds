{{-- resources/views/Products/All/index.blade.php --}}
<?php $data = array_merge($data, [
  'title' => $metaTitle,
  'description' => $metaDescription,
  ]); ?>
@extends('layouts.app')
<?php
    // ← SEO
    $metaTitle = 'المنتجات';
    $metaDescription = 'تصفح جميع منتجاتنا من موبايلات وإكسسوارات وأجهزة لوحية وغيرها.';
    $company = $data['FilterBrands']->firstWhere('maintenancecompany_id', request('brand')) ?? null;
?>
@section('title', 'المنتجات — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<!-- EcommerceAllProducts -->
{{-- Page Header --}}
<x-page-header
    title="المنتجات"
    :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'المنتجات', 'url' => route('allproducts')], 
    ]"
/>

{{-- Brands Bar --}}
<div class="brands-bar">
    <div class="container">
        <div class="brands-bar__track">
            <div class="brands-bar__item">
                <img src="{{ asset('images/partnercompany/' . $company->maintenancecompany_image) }}"
                     alt="{{ $company->maintenancecompany_title }}"
                     loading="lazy" height="30">
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        {{-- ════════════════════════════════
             Sidebar — الفلترة
        ════════════════════════════════ --}}
        <aside class="col-lg-3 d-none d-lg-block" id="filtersSidebar">
            <form method="GET" action="{{ route('EcommerceAllProducts') }}" id="filtersForm">

                {{-- ماذا في اللوب --}}
                <div class="filter-card mb-3">
                    <div class="filter-card__header">
                        <span>ماذا في اللوب</span>
                        <i class="bi bi-chevron-up"></i>
                    </div>
                    <div class="filter-card__body">
                        {{--
                            بتجيب عدد المنتجات في كل category من الـ DB
                            وبتعرضها هنا كـ checkboxes
                            مثال: Category::withCount('ecommerceProducts')->get()
                        --}}
                        @foreach($FilterCategories ?? [] as $cat)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="category[]" value="{{ $cat->category_id }}"
                                   id="cat_{{ $cat->category_id }}"
                                   {{ in_array($cat->category_id, request('category', [])) ? 'checked' : '' }}
                                   onchange="document.getElementById('filtersForm').submit()">
                            <label class="form-check-label d-flex justify-content-between"
                                   for="cat_{{ $cat->category_id }}">
                                <span>{{ $cat->category_name }}</span>
                                <span class="filter-count">{{ $cat->products_count ?? 0 }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- الشركة --}}
                <div class="filter-card mb-3">
                    <div class="filter-card__header" data-bs-toggle="collapse" data-bs-target="#filterBrand">
                        <span>الشركة</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="collapse show filter-card__body" id="filterBrand">
                        @foreach($FilterBrands ?? [] as $brand)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="brand[]" value="{{ $brand->maintenancecompany_id }}"
                                   id="brand_{{ $brand->maintenancecompany_id }}"
                                   {{ in_array($brand->maintenancecompany_id, request('brand', [])) ? 'checked' : '' }}
                                   onchange="document.getElementById('filtersForm').submit()">
                            <label class="form-check-label" for="brand_{{ $brand->maintenancecompany_id }}">
                                {{ $brand->maintenancecompany_title }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- السعر --}}
                <div class="filter-card mb-3">
                    <div class="filter-card__header">
                        <span>السعر</span>
                        <i class="bi bi-chevron-up"></i>
                    </div>
                    <div class="filter-card__body">
                        {{--
                            ← Range slider بسيط
                            : اجيب min وmax price من الـ DB
                        --}}
                        <input type="range" class="price-range"
                               name="max_price"
                               min="{{ $minPrice ?? 0 }}"
                               max="{{ $maxPrice ?? 100000 }}"
                               value="{{ request('max_price', $maxPrice ?? 100000) }}"
                               oninput="document.getElementById('priceDisplay').textContent = this.value">
                        <div class="d-flex justify-content-between mt-2">
                            <span>{{ $minPrice ?? 0 }} ج.م</span>
                            <span id="priceDisplay">{{ request('max_price', $maxPrice ?? 100000) }} ج.م</span>
                        </div>
                    </div>
                </div>

                {{-- اللون --}}
                <div class="filter-card mb-3">
                    <div class="filter-card__header">
                        <span>اللون</span>
                        <i class="bi bi-chevron-up"></i>
                    </div>
                    <div class="filter-card__body">
                        {{--
                            ← : اجيب الألوان المتاحة من الـ DB
                            حسب الـ products الموجودة
                        --}}
                        <div class="color-swatches">
                            @foreach($FilterColors ?? [] as $color)
                            <button type="button"
                                    class="color-swatch {{ request('color') === $color['value'] ? 'active' : '' }}"
                                    style="background: {{ $color['hex'] }}"
                                    onclick="document.querySelector('[name=color]').value='{{ $color['value'] }}'; document.getElementById('filtersForm').submit()"
                                    title="{{ $color['name'] }}">
                            </button>
                            @endforeach
                            {{-- Default colors لو مفيش من الـ DB --}}
                            <button type="button" class="color-swatch" style="background:#000" title="أسود"></button>
                            <button type="button" class="color-swatch" style="background:#fff; border: 1px solid #ddd" title="أبيض"></button>
                            <button type="button" class="color-swatch" style="background:#2563eb" title="أزرق"></button>
                            <button type="button" class="color-swatch" style="background:#ef4444" title="أحمر"></button>
                        </div>
                        <input type="hidden" name="color" value="{{ request('color') }}">
                    </div>
                </div>

                {{-- الضمان --}}
                <div class="filter-card mb-3">
                    <div class="filter-card__header" data-bs-toggle="collapse" data-bs-target="#filterWarranty">
                        <span>الضمان</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="collapse show filter-card__body" id="filterWarranty">
                        @foreach(['سنة', 'سنتين', '3 سنوات'] as $warranty)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="warranty[]" value="{{ $warranty }}"
                                   id="w_{{ $loop->index }}"
                                   {{ in_array($warranty, request('warranty', [])) ? 'checked' : '' }}
                                   onchange="document.getElementById('filtersForm').submit()">
                            <label class="form-check-label" for="w_{{ $loop->index }}">{{ $warranty }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

            </form>
        </aside>

        {{-- ════════════════════════════════
             Main Content — المنتجات
        ════════════════════════════════ --}}
        <div class="col-lg-9 col-12">

            {{-- Toolbar --}}
            <div class="products-toolbar d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

                {{-- اليسار: الترتيب --}}
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm toolbar-sort"
                            name="sort" form="filtersForm"
                            onchange="document.getElementById('filtersForm').submit()">
                        <option value="latest"   {{ request('sort') === 'latest'   ? 'selected' : '' }}>الأحدث</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>السعر: من الأقل</option>
                        <option value="price_desc"{{ request('sort') === 'price_desc'? 'selected' : '' }}>السعر: من الأعلى</option>
                    </select>
                </div>

                {{-- اليمين: العدد + View Toggle + فلتر موبايل --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="products-count text-muted">
                        {{-- ← : $Products->total() --}}
                        {{ $Products->total() ?? 0 }} منتج
                    </span>

                    {{-- Toggle Grid/List --}}
                    <div class="view-toggle d-none d-lg-flex">
                        <button class="view-btn active" id="gridViewBtn" title="شبكة">
                            <i class="bi bi-grid"></i>
                        </button>
                        <button class="view-btn" id="listViewBtn" title="قائمة">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>

                    {{-- Toggle موبايل: 2 في الصف / 1 في الصف --}}
                    <div class="view-toggle d-flex d-lg-none">
                        <button class="view-btn active" id="gridView2Btn" title="عمودين">
                            <i class="bi bi-grid"></i>
                        </button>
                        <button class="view-btn" id="gridView1Btn" title="عمود واحد">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>

                    {{-- فلتر موبايل --}}
                    <button class="btn btn-outline-secondary btn-sm d-lg-none"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#mobileFilters">
                        <i class="bi bi-funnel"></i>
                        فلترة
                    </button>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="products-grid" id="productsGrid">
                @forelse($Products as $ep)
                <x-product-card
                    :id="$ep->ecommerceproduct_id"
                    :name="$ep->product?->product_name ?? ''"
                    :price="$ep->product?->product_sellprice ?? 0"
                    :offer-price="$ep->product?->product_offerprice ?? null"
                    :image="$ep->product?->product_image ?? ''"
                    route="ProductDetails"
                    :has-offer="($ep->product?->product_offerprice ?? 0) > 0 && ($ep->product?->product_offerprice < $ep->product?->product_sellprice)"
                />
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search" style="font-size: 3rem; color: var(--text)"></i>
                    <p class="mt-3 text-muted">لا توجد منتجات</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($Products->hasPages())
            <div class="products-pagination mt-4 d-flex justify-content-center align-items-center gap-2 flex-wrap">
                {{-- السابق --}}
                @if($Products->onFirstPage())
                <button class="pagination-btn" disabled>السابق</button>
                @else
                <a href="{{ $Products->previousPageUrl() }}" class="pagination-btn">السابق</a>
                @endif

                {{-- الأرقام --}}
                @foreach($Products->getUrlRange(1, $Products->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="pagination-btn {{ $page === $Products->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
                @endforeach

                {{-- التالي --}}
                @if($Products->hasMorePages())
                <a href="{{ $Products->nextPageUrl() }}" class="pagination-btn">التالي</a>
                @else
                <button class="pagination-btn" disabled>التالي</button>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Mobile Filters Offcanvas --}}
<div class="offcanvas offcanvas-start" id="mobileFilters" tabindex="-1">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">الفلترة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        {{-- نفس محتوى الـ sidebar بالظبط --}}
        <form method="GET" action="{{ route('EcommerceAllProducts') }}">
            <div class="filter-card mb-3">
                <div class="filter-card__header">التصنيفات</div>
                <div class="filter-card__body">
                    @foreach($FilterCategories ?? [] as $cat)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="category[]" value="{{ $cat->category_id }}"
                               {{ in_array($cat->category_id, request('category', [])) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $cat->category_name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn hero__btn w-100 mt-3">تطبيق الفلترة</button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ── View Toggle Desktop ──
const gridViewBtn = document.getElementById('gridViewBtn');
const listViewBtn = document.getElementById('listViewBtn');
const grid        = document.getElementById('productsGrid');

gridViewBtn?.addEventListener('click', () => {
    grid.classList.remove('products-grid--list');
    gridViewBtn.classList.add('active');
    listViewBtn.classList.remove('active');
    localStorage.setItem('productView', 'grid');
});

listViewBtn?.addEventListener('click', () => {
    grid.classList.add('products-grid--list');
    listViewBtn.classList.add('active');
    gridViewBtn.classList.remove('active');
    localStorage.setItem('productView', 'list');
});

// ── View Toggle Mobile ──
const grid2Btn = document.getElementById('gridView2Btn');
const grid1Btn = document.getElementById('gridView1Btn');

grid2Btn?.addEventListener('click', () => {
    grid.classList.remove('products-grid--single');
    grid2Btn.classList.add('active');
    grid1Btn.classList.remove('active');
});

grid1Btn?.addEventListener('click', () => {
    grid.classList.add('products-grid--single');
    grid1Btn.classList.add('active');
    grid2Btn.classList.remove('active');
});

// ── Restore saved view ──
if(localStorage.getItem('productView') === 'list') {
    grid?.classList.add('products-grid--list');
    listViewBtn?.classList.add('active');
    gridViewBtn?.classList.remove('active');
}
</script>
@endsection