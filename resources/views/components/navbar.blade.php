{{-- resources/views/components/navbar.blade.php --}}
@props([
    'staticLinks' => [],
    'navData'     => [],
    'branchName'  => '',
    'phone'       => '',
    'logo'        => 'images/primaryLogo.png',
])

{{-- TOP BAR --}}
<div class="top-bar">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center h-100">
            <a href="tel:{{ $phone }}"
               class="top-bar__link d-flex align-items-center gap-2"
               aria-label="اتصل بنا">
                <i class="bi bi-telephone-fill"></i>
                <span>{{ $phone }}</span>
            </a>
            <span>English</span>
        </div>
    </div>
</div>

{{-- MAIN NAVBAR --}}
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top"
     role="navigation"
     aria-label="القائمة الرئيسية">
    <div class="container-fluid px-4">

        <a class="navbar-brand max-h-[80px] max-w-[280px] d-flex justify-content-center align-items-center" href="{{ route('home') }}">
            <img src="{{ asset($logo) }}" alt="المتخصص" height="48" loading="eager">
        </a>

        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNav"
                aria-controls="mainNav"
                aria-expanded="false"
                aria-label="فتح القائمة">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-1">

                @foreach($staticLinks as $link)
                    @php
                        /*
                         | كل link بيبص على db_key الخاص بيه بس
                         | ده اللي بيمنع التكرار
                         | لو has_db = false → children فاضية → مفيش dropdown
                        */
                        $children = [];
                        if (!empty($link['has_db']) && !empty($link['db_key'])) {
                            $children = $navData[$link['db_key']] ?? [];
                        }
                        $hasChildren = count($children) > 0;
                    @endphp

                    <li class="nav-item {{ $hasChildren ? 'dropdown' : '' }}">

                        @if($hasChildren)
                            <a class="nav-link dropdown-toggle fw-medium"
                               href="#"
                               data-bs-toggle="dropdown"
                               data-bs-auto-close="outside"
                               aria-expanded="false">
                                {{ $link['name'] }}
                            </a>

                            <ul class="dropdown-menu border-0 shadow-sm">

                                {{-- عرض الكل --}}
                                <li>
                                    <a class="dropdown-item fw-semibold text-primary border-bottom pb-2 mb-1"
                                       href="{{ route($link['route']) }}">
                                        <i class="bi bi-arrow-right ms-1"></i>
                                        عرض الكل
                                    </a>
                                </li>

                                @foreach($children as $cat)
                                    @if(count($cat['children']) > 0)
                                        {{--
                                            Mega submenu:
                                            - data-bs-auto-close="outside" مهم
                                              عشان ميقفلش لما تدوس جوه الـ submenu
                                            - dropend بيفتح السهم على اليسار في RTL
                                        --}}
                                        <li class="dropend">
                                            <a class="dropdown-item dropdown-toggle"
                                               href="{{ route('category.show', $cat['slug']) }}"
                                               data-bs-toggle="dropdown"
                                               data-bs-auto-close="outside"
                                               aria-expanded="false">
                                                {{ $cat['name'] }}
                                            </a>
                                            <ul class="dropdown-menu border-0 shadow-sm">
                                                @foreach($cat['children'] as $sub)
                                                    <li>
                                                        {{--
                                                            URL: /category/{slug_english}
                                                            لكن في الـ breadcrumb أو الـ title
                                                            بنعرض الاسم العربي
                                                        --}}
                                                        <a class="dropdown-item"
                                                           href="{{ route('category.show', $sub['slug']) }}">
                                                            {{ $sub['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('category.show', $cat['slug']) }}">
                                                {{ $cat['name'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach

                            </ul>

                        @else
                            <a class="nav-link fw-medium"
                               href="{{ route($link['route']) }}"
                               @if(request()->routeIs($link['route'])) aria-current="page" @endif>
                                {{ $link['name'] }}
                            </a>
                        @endif

                    </li>
                @endforeach

            </ul>

            {{-- الأيقونات --}}
            <div class="navbar-icons d-flex align-items-center gap-3 ms-lg-3 mt-3 mt-lg-0">
                <button class="btn btn-link p-0 text-dark position-relative" aria-label="المفضلة">
                    <i class="bi bi-heart fs-5"></i>
                    <span class="navbar-badge">0</span>
                </button>
                <a href="{{ route('cart') }}" class="btn btn-link p-0 text-dark position-relative" aria-label="السلة">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="navbar-badge">{{ session('cart_count', 0) }}</span>
                </a>
                <a href="{{ route('account') }}" class="btn btn-link p-0 text-dark" aria-label="حسابي">
                    <i class="bi bi-person fs-5"></i>
                </a>
                <button class="btn btn-link p-0 text-dark" aria-label="بحث">
                    <i class="bi bi-search fs-5"></i>
                </button>
            </div>
        </div>
    </div>
</nav>