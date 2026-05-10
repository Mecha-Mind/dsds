@if (!$ScrollingOffers->isEmpty())
    <div id="desktop_content_scrollingoffer">
        <div id="customCarouselDesktop" class="my-carousel-container h-100">
            <div class="my-carousel-inner pt-2 pb-2">
                @php $snScrollingOffers = 0; @endphp

                @foreach ($ScrollingOffers as $ScrollingOffer)
                    @if ($ScrollingOffer->scrollingoffer_image != null)
                        <a href="{{ $ScrollingOffer->scrollingoffer_url }}" target="_blank">
                            <div class="my-carousel-item {{ $snScrollingOffers == 0 ? 'my-active' : '' }}"
                                style="background-image: url('{{ url('/images/ScrollingOffers/' . $ScrollingOffer->scrollingoffer_image) }}');">
                                <div class="container">
                                    <div class="d-block h-100 w-100 text-decoration-none">
                                        <div class="row scrollingoffer align-items-center h-100">
                                            <div class="col-md-6 d-flex justify-content-center"></div>
                                            <div class="col-md-6 d-flex flex-column justify-content-between h-100">
                                                {{-- <div>
                                                    <h5
                                                        class="fw-bold text-end scrollingofferBoldheadlinefont Headlinecolor mb-3">
                                                        {{ $ScrollingOffer->scrollingoffer_headline }}
                                                    </h5>
                                                    <p class="text-end scrollingofferdescriptionfont">
                                                        {{ $ScrollingOffer->scrollingoffer_description }}
                                                    </p>
                                                </div>
                                                <div class="text-end mt-auto">
                                                    <span class="btn btn-secondary Primarybackground">تسويق الآن</span>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @php $snScrollingOffers++; @endphp
                    @endif
                @endforeach
            </div>

            <button class="my-carousel-control-prev" type="button">‹</button>
            <button class="my-carousel-control-next" type="button">›</button>
            <style>
                .my-carousel-container {
                    position: relative;
                    overflow: hidden;
                }

                .my-carousel-inner {
                    position: relative;
                    width: 100%;
                    height: 400px;
                }

                .my-carousel-item {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-size: cover;
                    background-position: center;
                    opacity: 0;
                    transition: opacity 1s ease;
                    pointer-events: none;
                }

                .my-carousel-item.my-active {
                    opacity: 1;
                    pointer-events: auto;
                    z-index: 2;
                }

                .my-carousel-control-prev,
                .my-carousel-control-next {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    border: none;
                    background: transparent;
                    color: white;
                    font-size: 2rem;
                    width: 40px;
                    height: 40px;
                    cursor: pointer;
                    z-index: 10;
                }

                .my-carousel-control-prev {
                    left: 10px;
                }

                .my-carousel-control-next {
                    right: 10px;
                }
            </style>


            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const carousel = document.querySelector('#customCarouselDesktop');
                    if (!carousel) return;

                    const items = carousel.querySelectorAll('.my-carousel-item');
                    const prevBtn = carousel.querySelector('.my-carousel-control-prev');
                    const nextBtn = carousel.querySelector('.my-carousel-control-next');
                    let currentIndex = 0;

                    function showItem(index) {
                        items.forEach((item, i) => {
                            item.classList.remove('my-active');
                        });

                        setTimeout(() => {
                            items[index].classList.add('my-active');
                        }, 20); // slight delay to allow transition
                    }

                    prevBtn.addEventListener('click', function() {
                        currentIndex = (currentIndex - 1 + items.length) % items.length;
                        showItem(currentIndex);
                    });

                    nextBtn.addEventListener('click', function() {
                        currentIndex = (currentIndex + 1) % items.length;
                        showItem(currentIndex);
                    });

                    // Auto play every 5 seconds
                    setInterval(() => {
                        currentIndex = (currentIndex + 1) % items.length;
                        showItem(currentIndex);
                    }, 5000);
                });
            </script>


        </div>


    </div>

    <div id="mobile_content_scrollingoffer">
        <div id="customCarouselMobile" class="my-carousel-container w-100">
            <div class="my-carousel-inner">
                @php $snScrollingOffers = 0; @endphp

                @foreach ($ScrollingOffers as $ScrollingOffer)
                    @if ($ScrollingOffer->scrollingoffer_imagemobile != null)
                        <a href="{{ $ScrollingOffer->scrollingoffer_url }}" target="_blank">
                            <div class="my-carousel-item {{ $snScrollingOffers == 0 ? 'my-active' : '' }}">
                                <div class="carousel-bg"
                                    style="background-image: url('{{ url('/images/ScrollingOffers/' . $ScrollingOffer->scrollingoffer_imagemobile) }}');">
                                </div>

                                <!-- النص خارج الصورة -->
                                <div class="carousel-caption-container bg-light text-dark text-end p-3">
                                    <div class="container">
                                        <h5 class="fw-bold scrollingofferBoldheadlinefont Headlinecolor mb-2">
                                            {{ $ScrollingOffer->scrollingoffer_headline }}
                                        </h5>
                                        <p class="scrollingofferdescriptionfont mb-3">
                                            {{ $ScrollingOffer->scrollingoffer_description }}
                                        </p>
                                        <div class="btn btn-secondary Primarybackground">
                                            تسويق الآن
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @php $snScrollingOffers++; @endphp
                    @endif
                @endforeach
            </div>

            <button class="my-carousel-control-prev" type="button">‹</button>
            <button class="my-carousel-control-next" type="button">›</button>

            <style>
                .my-carousel-container {
                    position: relative;
                    overflow: hidden;
                }

                .my-carousel-inner {
                    position: relative;
                    width: 100%;
                }

                /* الصورة بنسبة 4:1 */
                .carousel-bg {
                    width: 100%;
                    aspect-ratio: 4 / 1;
                    background-size: cover;
                    background-position: center;
                    border-radius: 0.5rem;
                }

                /* النص أسفل الصورة */
                .carousel-caption-container {
                    border-radius: 0 0 0.5rem 0.5rem;
                }

                .my-carousel-item {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    opacity: 0;
                    transition: opacity 1s ease;
                    pointer-events: none;
                }

                .my-carousel-item.my-active {
                    opacity: 1;
                    pointer-events: auto;
                    z-index: 2;
                }

                /* أزرار التنقل */
                .my-carousel-control-prev,
                .my-carousel-control-next {
                    position: absolute;
                    top: 40%;
                    transform: translateY(-50%);
                    border: none;
                    background: rgba(0, 0, 0, 0.15);
                    /* خفيفة أكتر */
                    color: white;
                    font-size: 2rem;
                    width: 40px;
                    height: 40px;
                    cursor: pointer;
                    z-index: 10;
                    border-radius: 50%;
                    backdrop-filter: blur(4px);
                    /* لمسة شفافية ناعمة */
                    transition: background 0.3s ease, transform 0.2s ease;
                }

                .my-carousel-control-prev:hover,
                .my-carousel-control-next:hover {
                    background: rgba(0, 0, 0, 0.35);
                    /* تظهر أكتر عند الـ hover */
                    transform: translateY(-50%) scale(1.1);
                }

                .my-carousel-control-prev {
                    left: 10px;
                }

                .my-carousel-control-next {
                    right: 10px;
                }


                /* للموبايل */
                @media (max-width: 768px) {
                    .carousel-bg {
                        aspect-ratio: 4 / 2;
                    }

                    .scrollingofferBoldheadlinefont {
                        font-size: 1rem;
                    }

                    .scrollingofferdescriptionfont {
                        font-size: 0.9rem;
                    }

                    .carousel-caption-container {
                        padding: 1rem;
                    }
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const carousel = document.querySelector('#customCarouselMobile');
                    if (!carousel) return;

                    const items = carousel.querySelectorAll('.my-carousel-item');
                    const prevBtn = carousel.querySelector('.my-carousel-control-prev');
                    const nextBtn = carousel.querySelector('.my-carousel-control-next');
                    let currentIndex = 0;

                    function showItem(index) {
                        items.forEach(item => item.classList.remove('my-active'));
                        items[index].classList.add('my-active');
                    }

                    prevBtn.addEventListener('click', () => {
                        currentIndex = (currentIndex - 1 + items.length) % items.length;
                        showItem(currentIndex);
                    });

                    nextBtn.addEventListener('click', () => {
                        currentIndex = (currentIndex + 1) % items.length;
                        showItem(currentIndex);
                    });

                    setInterval(() => {
                        currentIndex = (currentIndex + 1) % items.length;
                        showItem(currentIndex);
                    }, 5000);
                });
            </script>
        </div>


    </div>
@endif
