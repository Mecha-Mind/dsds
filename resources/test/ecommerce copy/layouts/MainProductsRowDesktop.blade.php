{{-- @include('ecommerce.layouts.MainProductsRowDesktop') --}}


<div class="row pt-2 pb-5">
    <div class="container">
        <div class="row g-3">
            @if ($AllEcommerceProducts)
                @foreach ($AllEcommerceProducts as $AllEcommerceProduct)
                    @php
                        $Product = $Products->firstWhere('product_id', $AllEcommerceProduct->product_id);
                        if (!$Product || !$Product->product_image) {
                            continue;
                        }
                    @endphp
                    @if ($Product->product_image)
                        <div class="col-12">
                            <div class="card shadow-sm p-3 h-100">
                                <div class="row g-3 align-items-center">
                                    <!-- Product Info -->
                                    <div class="col-9 d-flex flex-column h-100">
                                        <!-- Description at the top -->
                                        <div class="mb-2 text-end apponwer_descrptiontext" style="direction: rtl;">
                                            {{ $Product->product_name }}
                                            <br>
                                            {{ $Product->product_description }}
                                        </div>

                                        <!-- Spacer to push content down -->
                                        <div class="flex-grow-1"></div>

                                        <!-- Price and Add to Cart at the bottom -->
                                        <div>
                                            <!-- Price -->
                                            <div class="d-flex align-items-center gap-2 text-end mb-3 apponwer_descrptiontext"
                                                style="direction: rtl;">
                                                @if ($AllEcommerceProduct->ecommerceproduct_appearinthelistofoffers == '1')
                                                    <span class="text-danger fw-bold">
                                                        {{ $Product->product_offerprice }} جنية
                                                    </span>
                                                    <del class="text-muted">
                                                        {{ $Product->product_sellprice }} جنية
                                                    </del>
                                                @else
                                                    <span class="fw-bold">
                                                        {{ $Product->product_sellprice }} جنية
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Add to Cart -->
                                            <div class="pb-3">
                                                @if (session('customer_name') === null)
                                                    <a href="{{ route('CustomerLogin') }}"
                                                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                                        <i class="fas fa-dolly-flatbed"></i>
                                                        <span>إضافة للسلة</span>
                                                    </a>
                                                @else
                                                    <form method="POST"
                                                        action="{{ route('ProductDetailsPost', $AllEcommerceProduct->product_id) }}"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        @method('post')
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

                                            <div class="p-3 pt-0">
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
                                                    class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2"
                                                    target="_blank" rel="noopener">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span> اطلب عبر الواتساب</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Image -->
                                    <div class="col-3 d-flex justify-content-end align-items-end">
                                        <a href="{{ route('ProductDetails', $AllEcommerceProduct->product_id) }}"
                                            class="d-flex w-100 h-100 justify-content-end align-items-end">
                                            <img src="{{ url('/images/productsimages/' . $Product->product_image) }}"
                                                class="img-fluid rounded imagerowdesktop" alt="Product Image">
                                        </a>
                                    </div>
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
                    <div class="col-5"></div>
                    <div class="col-1 m-auto">
                        <div class="hidden sm:hidden">
                            <a href="{{ $AllEcommerceProducts->previousPageUrl() }}"
                                class="pagination-button decorationnone">
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-chevron-left"></i></button>
                            </a>
                        </div>
                    </div>
                    <div class="col-1 m-auto">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <a href="{{ $AllEcommerceProducts->nextPageUrl() }}"
                                class="pagination-button decorationnone">
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-chevron-right"></i></button>
                            </a>
                        </div>
                    </div>
                    <div class="col-5"></div>
                </div>
            </nav>
        </div>
    </div>
</div>
