@php
$links = [
    ['route' => 'home', 'label' => 'الرئيسية'],

    [
        'label' => 'المنتجات',
        'dropdown' => true,
        'children' => [
            ['route' => 'EcommerceAllProducts', 'label' => 'كل المنتجات'],
            ['route' => 'EcommerceOffers', 'label' => 'العروض'],
            ['route' => 'EcommerceAllCategories', 'label' => 'التصنيفات'],
        ]
    ],

    ['route' => 'UserMaintenance', 'label' => 'الصيانة'],
    ['route' => 'EcommerceContactUs', 'label' => 'تواصل معنا'],
    ['route' => 'EcommerceContactUs', 'label' => 'test'],
];
@endphp

<header>

    {{-- Top Navbar --}}
    <div class="d-none d-md-block top-navbar py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-phone text-white "></i>
                <p class="mb-0">{{ $ecommerceSharedData['branch']->branch_phone }}</p>
            </div>
            <p class="mb-0">{{ $ecommerceSharedData['branch']->branch_name }}</p>
        </div>
    </div>

    {{-- Main Navbar --}}
    <nav class="navbar-expand-lg main-navbar">
        <div class="container d-flex justify-content-center align-items-center md:justify-content-between flex-row-reverse flex-md-row-reverse">

            {{-- Logo --}}
            <a class="logo" href="{{ route('home') }}">
                <img src="{{ url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image) }}"
                     alt="Logo"
                     class="img-fluid"
                     style="max-height: 50px;">
            </a>

            {{-- Toggler (Mobile) --}}
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Links + Icons --}}
            <div class="collapse navbar-collapse py-2 flex-row-reverse flex-md-row-reverse" id="mainNavbar">

                <ul class="links d-flex justify-content-center align-items-center text-center flex-md-row-reverse">

                    @foreach($links as $link)

                        @if(isset($link['dropdown']) && $link['dropdown'])

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle"
                                href="#"
                                data-bs-toggle="dropdown">
                                    {{ $link['label'] }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    @foreach($link['children'] as $child)
                                        <li>
                                            <a class="dropdown-item"
                                            href="{{ route($child['route']) }}">
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                        @else

                            <li class="nav-item">
                                <a href="{{ route($link['route']) }}"
                                class="nav-link {{ request()->routeIs($link['route']) ? 'active' : '' }}">
                                    {{ $link['label'] }}
                                </a>
                            </li>

                        @endif

                    @endforeach

                </ul>

                {{-- Icons --}}
                <div class="icons d-flex align-items-center gap-3">

                    @if (session('customer_phone'))
                        <a href="{{ route('ShoppingCart') }}">
                            <i class="fa-regular fa-shopping-cart"></i>
                        </a>
                    @endif

                    @if (session('customer_name') === null)
                        <a href="{{ route('CustomerLogin') }}">
                            <i class="fa-regular fa-user"></i>
                        </a>
                    @else
                        <a href="{{ route('UserPersonalPage') }}">
                            <i class="fa-regular fa-user-circle"></i>
                        </a>
                    @endif

                    <a href="{{ route('EcommerceAllProducts') }}">
                        <i class="fas fa-search"></i>
                    </a>

                    <a href="{{ route('EcommerceAllProducts') }}">
                        <i class="fa-regular fa-heart"></i>
                    </a>

                </div>

            </div>
        </div>
    </nav>

    {{-- Alerts --}}
    {{-- <div class="container mt-2 text-center">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div> --}}

</header>