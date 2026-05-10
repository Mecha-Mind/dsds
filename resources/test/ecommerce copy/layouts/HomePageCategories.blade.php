@if (!$Categories->isEmpty())

    <div id="desktop_content_HomePageCategories">
        <div class="row pt-5">
            <div class="container">
                <div class="row categoryheadlinetext d-flex align-items-center justify-content-end text-right">
                    <div class="col-6 d-flex align-items-center justify-content-start text-right">
                        <a href="{{ route('EcommerceAllCategories') }}" class="Mediumfont decorationnone">
                            <span class="btn btn-secondary Primarybackground">
                                عرض المزيد
                            </span>
                        </a>
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end text-right Primarycolor">
                        التصنيفات
                    </div>
                </div>
                <div class="row pt-3 g-3 g-md-4 justify-content-center">
                    @foreach ($Categories as $Category)
                        @if ($Category->category_image)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="{{ route('CategoryProduct', $Category->category_id) }}"
                                    class="text-decoration-none">
                                    <div class="category-card h-100 d-flex flex-column align-items-center p-3">
                                        <div class="category-image-container ratio ratio-1x1 mb-2 w-100">
                                            <img src="{{ url('/images/categoriesimages/' . $Category->category_image) }}"
                                                alt="{{ $Category->category_name }}"
                                                class="object-fit-contain w-100 h-100" loading="lazy">
                                        </div>
                                        <h3 class="category-title text-center mb-0 fs-6 fw-semibold">
                                            {{ $Category->category_name }}
                                        </h3>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div id="mobile_content_HomePageCategories">
        <div class="row pt-5">
            <div class="col-12 p-3 m-auto">
                <div class="row categorytitlehomepagemobile d-flex align-items-center justify-content-end text-right">
                    <div class="col-6 d-flex align-items-center justify-content-start text-right">
                        <a href="{{ route('EcommerceAllCategories') }}" class="Mediumfont decorationnone">
                            <span class="btn btn-secondary Primarybackground">
                                عرض المزيد
                            </span>
                        </a>
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end text-right Primarycolor">
                        التصنيفات
                    </div>

                </div>
                <div class="row p-3 justify-content-center">
                    @foreach ($Categories as $Category)
                        @if ($Category->category_image)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 pb-2">
                                <a href="{{ route('CategoryProduct', $Category->category_id) }}"
                                    class="text-decoration-none">
                                    <div class="category-card h-100 d-flex flex-column align-items-center p-3">
                                        <div class="category-image-container ratio ratio-1x1 mb-2 w-100">
                                            <img src="{{ url('/images/categoriesimages/' . $Category->category_image) }}"
                                                alt="{{ $Category->category_name }}"
                                                class="object-fit-contain w-100 h-100" loading="lazy">
                                        </div>
                                        <h3 class="category-title text-center mb-0 fs-6 fw-semibold">
                                            {{ $Category->category_name }}
                                        </h3>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
