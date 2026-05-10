@if (!$Offersfromtheowners->isEmpty())
    <div id="desktop_content_Offersfromtheowner">
        <div class="row pt-5">
            <div class="container">
                <div class="row categoryheadlinetext d-flex align-items-center justify-content-end text-right">
                    <div class="col-6 d-flex align-items-center justify-content-start text-right">
                        <a href="{{ route('EcommerceOffers') }}" class="Mediumfont decorationnone">
                            <span class="btn btn-secondary Primarybackground">
                                عرض المزيد
                            </span>
                        </a>
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end text-right Primarycolor">
                        العروض
                    </div>
                </div>

                <div class="row g-3 align-items-stretch mt-3">
                    @php $SN = 1; @endphp
                    @foreach ($Offersfromtheowners as $Offersfromtheowner)
                        @if ($Offersfromtheowner->offerfromtheowner_image != null)
                            @if ($SN == 3)
                                <!-- الصورة الكبيرة -->
                                <div class="col-12">
                                    <div class="offer-card">
                                        <div class="offer-bg"
                                            style="background-image: url('{{ url('/images/Offersfromtheowners/' . $Offersfromtheowner->offerfromtheowner_image) }}');">
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- الصورتين الصغيرتين -->
                                <div class="col-6">
                                    <div class="offer-card">
                                        <div class="offer-bg"
                                            style="background-image: url('{{ url('/images/Offersfromtheowners/' . $Offersfromtheowner->offerfromtheowner_image) }}');">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @php $SN++; @endphp
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <style>
            /* الكارت العام */
            .offer-card {
                width: 100%;
                overflow: hidden;
                border-radius: 0.5rem;
            }

            /* الصورة كخلفية بنسبة 4:1 */
            .offer-bg {
                width: 100%;
                aspect-ratio: 4 / 1;
                background-size: cover;
                background-position: center;
                border-radius: 0.5rem;
                transition: transform 0.4s ease;
            }

            /* تأثير لطيف عند hover */
            .offer-bg:hover {
                transform: scale(1.03);
            }

            /* في الموبايل تكون 4:2 */
            @media (max-width: 768px) {
                .offer-bg {
                    aspect-ratio: 4 / 2;
                }
            }
        </style>

    </div>

    <div id="mobile_content_Offersfromtheowner">
        <div class="row pt-5">
            <div class="col-12 p-3 m-auto">
                <div class="row categorytitlehomepagemobile d-flex align-items-center justify-content-end text-right">
                    <div class="col-6 d-flex align-items-center justify-content-start text-right">
                        <a href="{{ route('EcommerceOffers') }}" class="Mediumfont decorationnone">
                            عرض المزيد
                        </a>
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end text-right">
                        العروض
                    </div>
                </div>

                <div class="row">
                    @foreach ($Offersfromtheowners as $Offersfromtheowner)
                        @if ($Offersfromtheowner->offerfromtheowner_image != null)
                            <div class="col-12 pt-4">
                                <div class="offerfromowner-card position-relative">
                                    <div class="offerfromowner-bg"
                                        style="background-image: url('{{ url('/images/Offersfromtheowners/' . $Offersfromtheowner->offerfromtheowner_image) }}');">
                                    </div>

                                    <!-- لو حبيت تضيف نص فوق الصورة -->
                                    @if ($Offersfromtheowner->offerfromtheowner_title ?? false)
                                        <div class="offerfromowner-caption text-end text-light p-3">
                                            <h5 class="fw-bold mb-1">{{ $Offersfromtheowner->offerfromtheowner_title }}
                                            </h5>
                                            <p class="mb-0">{{ $Offersfromtheowner->offerfromtheowner_description }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <style>
            /* نفس مظهر carousel-bg */
            .offerfromowner-card {
                position: relative;
                overflow: hidden;
                border-radius: 0.5rem;
            }

            .offerfromowner-bg {
                width: 100%;
                aspect-ratio: 4 / 1;
                background-size: cover;
                background-position: center;
                border-radius: 0.5rem;
            }

            /* النص فوق الصورة (اختياري) */
            .offerfromowner-caption {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                background: rgba(0, 0, 0, 0.4);
                border-radius: 0 0 0.5rem 0.5rem;
            }

            /* للموبايل */
            @media (max-width: 768px) {
                .offerfromowner-bg {
                    aspect-ratio: 4 / 2;
                }

                .offerfromowner-caption h5 {
                    font-size: 1rem;
                }

                .offerfromowner-caption p {
                    font-size: 0.9rem;
                }
            }
        </style>

    </div>
@endif
