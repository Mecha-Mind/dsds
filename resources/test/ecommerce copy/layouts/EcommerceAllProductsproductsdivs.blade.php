<div id="desktop_content_EcommerceAllProductsproductsdivs">



    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row pt-5 pb-3">
                    <div class="col-3 m-auto">
                        <button class="btn apponwer_systemprimarybtn">
                            <i class="fas fa-vector-square pt-2 pl-2 "></i>
                        </button>

                        <a href="{{ route('EcommerceAllProductsproductineachrow') }}"
                            class="decorationnone Headlinecolor">
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-sliders-h pt-2 pl-2"></i>
                            </button>
                        </a>
                    </div>
                    <div class="col-6 m-auto">
                        <form action="{{ route('EcommerceAllProductsserachforproductPost') }}" method="post"
                            class="row align-items-center rounded">
                            @csrf
                            @method('post')
                            <button type="submit" class="btn btn-circle">
                                <div class="row">
                                    <div class="col-md-2 d-flex justify-content-center align-items-center">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-md-10 d-flex justify-content-center align-items-center text-end">
                                        <input type="search" name="search" placeholder=" .... البحث عن منتج" required
                                            class="form-control custom-search-input text-end apponwer_descrptiontext">
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                    <div class="col-3 text-end">
                        <a href="{{ route('EcommerceMostSaleProducts') }}" class="text-decoration-none">
                            <button class="btn btn-primary">
                                الاكثر مبيعا
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('ecommerce.layouts.MainProductsDesktop')

</div>


<div id="mobile_content_EcommerceAllProductsproductsdivs">

    <div class="row py-3" style="padding: 16px;">
        <div class="col-12 col-md-3 d-flex justify-content-center align-items-center mb-2 mb-md-0">
            <a href="{{ route('EcommerceAllProductsproductineachrow') }}"
                class="text-decoration-none Headlinecolor me-3">
                <button class="btn apponwer_systemprimarybtn">
                    <i class="fas fa-sliders-h fs-5"></i>
                </button>
            </a>
            <button class="btn apponwer_systemprimarybtn">
                <i class="fas fa-vector-square fs-5 "></i>
            </button>
        </div>

        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <form action="{{ route('EcommerceAllProductsserachforproductPost') }}" method="post"
                class="d-flex align-items-center border rounded p-1 w-100">
                @csrf
                @method('post')
                <button type="submit" class="btn border-0 d-flex align-items-center px-2">
                    <i class="fa fa-search"></i>
                </button>
                <input type="search" name="search" placeholder=" .... البحث " required
                    class="form-control border-0 text-end apponwer_descrptiontext">
            </form>
        </div>

        <div class="col-12 col-md-3 d-flex justify-content-center align-items-center">
            <a href="{{ route('EcommerceMostSaleProducts') }}" class="w-100">
                <button class="btn btn-primary w-100">
                    الاكثر مبيعا
                </button>
            </a>
        </div>
    </div>

    @include('ecommerce.layouts.MainProductsMobile')


</div>
