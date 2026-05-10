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



            <!-- {{-- في صفحة الـ category --}}
@section('title', $pageTitle)

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
        {{-- الاسم العربي في الـ breadcrumb --}}
        <li class="breadcrumb-item active">{{ $category->category_name }}</li>
    </ol>
</nav>  -->