{{-- resources/views/components/footer.blade.php --}}
@props(['footer' => [], 'categories' => [], 'logo' => ''])

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="row g-4 g-lg-5">

            {{-- 1. اللوجو والعنوان --}}
            <div class="col-6 col-lg-3">
                <a href="{{ route('home') }}" class="d-inline-block mb-3">
                    <img src="{{ asset($logo) }}"
                         alt="المتخصص"
                         height="56"
                         loading="lazy">
                </a>
                <address class="footer__address">
                    @if($footer['address'] ?? null)
                    <p>
                        <i class="bi bi-geo-alt-fill"></i>
                        {{ $footer['address'] }}
                    </p>
                    @endif
                    @if($footer['phone'] ?? null)
                    <p>
                        <a href="tel:{{ $footer['phone'] }}">
                            <i class="bi bi-telephone-fill"></i>
                            {{ $footer['phone'] }}
                        </a>
                    </p>
                    @endif
                    @if($footer['email'] ?? null)
                    <p>
                        <a href="mailto:{{ $footer['email'] }}">
                            <i class="bi bi-envelope"></i>
                            {{ $footer['email'] }}
                        </a>
                    </p>
                    @endif
                </address>
            </div>

            {{-- 2. التصنيفات --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">التصنيفات</h3>
                <ul class="footer__links">
                    @if(count($categories) > 0)
                        @foreach(array_slice($categories, 0, 6) as $cat)
                        <li>
                            <a href="{{ route('category.show', $cat['slug']) }}">
                                {{ $cat['name'] }}
                            </a>
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- 3. المعلومات --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">المعلومات</h3>
                <ul class="footer__links">
                    @if($footer['info_links'] ?? null)
                        @foreach($footer['info_links'] as $link)
                        <li>
                            <a href="{{ route($link['route']) }}">
                                {{ $link['name'] }}
                            </a>
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- 4. تابعنا --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">تابعنا</h3>
                <div class="footer__social">
                    @if($footer['social'] ?? null)
                        @foreach($footer['social'] as $s)
                        <a href="{{ $s['url'] }}"
                           class="footer__social-link"
                           aria-label="{{ $s['label'] }}"
                           target="_blank"
                           rel="noopener noreferrer">
                            <i class="bi {{ $s['icon'] }}"></i>
                        </a>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        {{-- Bottom Bar --}}
        <div class="footer__bottom">
            <p>{{ date('Y') }} جميع الحقوق محفوظة &copy;</p>
            {{-- Payment Icons --}}
            @if(!empty($footer['payment_icons']))
            <div class="footer__payment">
                @foreach($footer['payment_icons'] as $icon)
                <img src="{{ asset($icon) }}"
                     alt="وسيلة دفع"
                     height="24"
                     loading="lazy">
                @endforeach
            </div>
            @endif
        </div>
    </div>
</footer>