{{-- resources/views/components/footer.blade.php --}}
@props(['footer' => [], 'categories' => []])
    <!-- <x-footer
        :footer="$footer ?? []"
        :categories="$categories ?? []"
    /> -->

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="row g-4 g-lg-5">

            {{-- 1. تابعنا --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">تابعنا</h3>
                <div class="footer__social">
                    @foreach($footer['social'] as $s)
                    <a href="{{ $s['url'] }}"
                       class="footer__social-link"
                       aria-label="{{ $s['label'] }}"
                       target="_blank"
                       rel="noopener noreferrer">
                        <i class="bi {{ $s['icon'] }}" aria-hidden="true"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- 2. المعلومات --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">المعلومات</h3>
                <ul class="footer__links">
                    @foreach($footer['info_links'] as $link)
                    <li>
                        <a href="{{ route($link['route']) }}">{{ $link['name'] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3. التصنيفات --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">التصنيفات</h3>
                <ul class="footer__links">
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('category.show', $cat['slug']) }}">
                            {{ $cat['name'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 4. اللوجو والعنوان --}}
            <div class="col-6 col-lg-3">
                <a href="{{ route('home') }}" class="footer__logo-wrap">
                    <img src="{{ asset($footer['logo']) }}"
                         alt="شعار المتجر"
                         height="48"
                         loading="lazy">
                </a>
                <address class="footer__address">
                    <p>
                        <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                        {{ $footer['address'] }}
                    </p>
                    <p>
                        <a href="tel:{{ $footer['phone'] }}">
                            <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                            {{ $footer['phone'] }}
                        </a>
                    </p>
                    <p>
                        <a href="mailto:{{ $footer['email'] }}">
                            <i class="bi bi-envelope-fill" aria-hidden="true"></i>
                            {{ $footer['email'] }}
                        </a>
                    </p>
                </address>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="footer__bottom">
            <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} — المتخصص</p>
        </div>
    </div>
</footer>