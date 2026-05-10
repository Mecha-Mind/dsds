{{-- @include('ecommerce.layouts.MainProductsRowMobile') --}}


<div class="row pt-2 pb-2">
    <div class="col-12 p-3 m-auto">
        <div class="row g-3 p-2 m-auto">
            @if ($AllEcommerceProducts)
                @foreach ($AllEcommerceProducts as $AllEcommerceProduct)
                    @php
                        $Product = $Products->firstWhere('product_id', $AllEcommerceProduct->product_id);
                        if (!$Product || !$Product->product_image) {
                            continue;
                        }
                    @endphp
                    @if ($Product->product_image)
                        <div class="col-12 m-auto pb-2">
                            <div
                                class="product-card-mobile h-100 d-flex flex-column border rounded-3 overflow-hidden shadow-sm p-2">
                                <!-- Product Image -->
                                <div class="product-image-mobile ratio ratio-1x1 mb-2">
                                    <a href="{{ route('ProductDetails', $AllEcommerceProduct->product_id) }}"
                                        class="d-block h-100 w-100">
                                        <img class="object-fit-cover w-100 h-100"
                                            src="{{ url('/images/productsimages/' . $Product->product_image) }}"
                                            alt="{{ $Product->product_name }}" loading="lazy">
                                    </a>
                                </div>

                                <!-- Product Info -->
                                <div class="product-info-mobile flex-grow-1 d-flex flex-column">
                                    <!-- Description -->
                                    <div class="product-description-mobile text-end text-truncate-2 mb-1"
                                        style="direction: rtl">
                                        {{ $Product->product_name }}
                                    </div>

                                    <!-- Price -->
                                    <div class="price-mobile d-flex align-items-center justify-content-start gap-2"
                                        style="direction: rtl">
                                        @if ($AllEcommerceProduct->ecommerceproduct_appearinthelistofoffers == '1')
                                            <span class="text-danger fw-bold">
                                                {{ $Product->product_offerprice }} جنية
                                            </span>
                                            <del class="text-muted small">
                                                {{ $Product->product_sellprice }} جنية
                                            </del>
                                        @else
                                            <span class="fw-bold">
                                                {{ $Product->product_sellprice }} جنية
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Add to Cart Button -->
                                <div class="add-to-cart-mobile mt-2">
                                    @if (session('customer_name') === null)
                                        <a href="{{ route('CustomerLogin') }}"
                                            class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                            <i class="fas fa-dolly-flatbed"></i>
                                            <span>إضافة للسلة</span>
                                        </a>
                                    @else
                                        <form method="post"
                                            action="{{ route('ProductDetailsPost', $AllEcommerceProduct->product_id) }}"
                                            class="w-100">
                                            @csrf
                                            @method('post')
                                            <button type="submit"
                                                class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-1">
                                                <span
                                                    class="btn apponwer_systemprimarybtn d-flex align-items-center justify-content-center p-0">
                                                    <i class="fas fa-dolly-flatbed fs-6"></i>
                                                </span>
                                                <span class="fs-6">إضافة للسلة</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="add-to-cart-mobile mt-2">

                                    @php
                                        $mainphone = '2' . $ecommerceSharedData['branch']->branch_phone;
                                        $Message =
                                            "مرحباً 👋\n\n" .
                                            "هل يمكنني الحصول على المزيد من المعلومات عن المنتج:\n" .
                                            "🔹 {$Product->product_name}\n\n" .
                                            'هذا الطلب مقدم من المتجر الإلكتروني الخاص بكم ✅';

                                        $encodedMessage = urlencode($Message);
                                    @endphp
                                    <a href="https://wa.me/{{ $mainphone }}?text={{ $encodedMessage }}"
                                        class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-1">
                                        <i class="fab fa-whatsapp"></i>
                                        <span> اطلب عبر الواتساب</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

        </div>
        <div class="d-flex justify-content-center mt-4" style="height:36px; overflow:hidden;">
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between w-100">
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-2 m-auto">
                        <div class="hidden sm:hidden">
                            <a href="{{ $AllEcommerceProducts->previousPageUrl() }}"
                                class="pagination-button decorationnone">
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-chevron-left"></i></button>
                            </a>
                        </div>
                    </div>
                    <div class="col-2 m-auto">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <a href="{{ $AllEcommerceProducts->nextPageUrl() }}"
                                class="pagination-button decorationnone">
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-chevron-right"></i></button>
                            </a>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>
            </nav>
        </div>
    </div>
</div>
