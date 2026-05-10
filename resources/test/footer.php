{{-- resources/views/components/footer.blade.php --}}
@props([
    'FooterCategories'   => collect(),
    'Branch'             => null,
    'SocialMediaContact' => null,
    'mapUrl'             => '#',
])

{{-- Features Bar --}}
<section class="features-bar">
    <div class="container">
        <div class="row g-4 text-end">
            @foreach([
                ['icon'=>'fas fa-lock',    'title'=>'نظام الدفع آمن',   'text'=>'نضمن الدفع الآمن مع PVE'],
                ['icon'=>'fas fa-undo',    'title'=>'سياسة الاسترجاع', 'text'=>'وفقاً لقانون حماية المستهلك'],
                ['icon'=>'fas fa-headset', 'title'=>'خدمة العملاء',    'text'=>'24 ساعة 7 أيام في الأسبوع'],
                ['icon'=>'fab fa-cc-visa', 'title'=>'وسائل الدفع',     'text'=>'فيزا، فودافون كاش، إنستا باي'],
            ] as $feature)
            <div class="col-6 col-md-3">
                <div class="feature-item d-flex align-items-start gap-3">
                    <div class="feature-item__icon-wrap">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="feature-item__title">{{ $feature['title'] }}</div>
                        <div class="feature-item__text">{{ $feature['text'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="row g-4 g-lg-5">

            {{-- 1. اللوجو والعنوان --}}
            <div class="col-6 col-lg-3">
                @if($Branch)
                <a href="{{ route('home') }}" class="d-inline-block mb-3">
                    <img src="{{ url('/images/brancheslogo/' . $Branch->branch_image) }}"
                         alt="{{ $Branch->branch_name }}"
                         height="48" loading="lazy">
                </a>
                <address class="footer__address">
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $Branch->branch_place }}
                    </a>
                    <a href="tel:+2{{ $Branch->branch_phone }}">
                        <i class="fas fa-phone-alt"></i>
                        {{ $Branch->branch_phone }}
                    </a>
                    @if($Branch->branch_phone2)
                    <a href="tel:+2{{ $Branch->branch_phone2 }}">
                        <i class="fas fa-phone-alt"></i>
                        {{ $Branch->branch_phone2 }}
                    </a>
                    @endif
                    @if($SocialMediaContact?->whatsapp)
                    <a href="{{ $SocialMediaContact->whatsapp }}" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        {{ $Branch->branch_phone }}
                    </a>
                    @endif
                </address>
                @endif
            </div>

            {{-- 2. التصنيفات --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">التصنيفات</h3>
                <ul class="footer__links">
                    @foreach($FooterCategories as $cat)
                    <li>
                        <a href="{{ route('CategoryProduct', $cat->category_id) }}">
                            {{ $cat->category_name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3. المعلومات --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">المعلومات</h3>
                <ul class="footer__links">
                    <li><a href="{{ route('EcommerceKnowAboutUs') }}">اعرف عنا</a></li>
                    <li><a href="{{ route('EcommerceContactUs') }}">تواصل معنا</a></li>
                    <li><a href="{{ route('UserPersonalPage') }}">حسابي</a></li>
                    <li><a href="{{ route('PrivacyPolicy') }}">سياسة الخصوصية</a></li>
                    <li><a href="{{ route('TermsAndConditions') }}">الشروط والأحكام</a></li>
                </ul>
            </div>

            {{-- 4. تابعنا --}}
            <div class="col-6 col-lg-3">
                <h3 class="footer__heading">تابعنا</h3>
                @if($SocialMediaContact)
                <div class="footer__social">
                    @php
                        $platforms = [
                            'facebook'    => ['facebook.png',    'Facebook'],
                            'instagram'   => ['instagram.png',   'Instagram'],
                            'tiktok'      => ['tiktok.png',      'TikTok'],
                            'whatsapp'    => ['whatsapp.png',    'WhatsApp'],
                            'youtube'     => ['youtube.png',     'YouTube'],
                            'telegram'    => ['telegram.png',    'Telegram'],
                            'telegrambot' => ['telegrambot.png', 'Telegram Bot'],
                            'linkedin'    => ['linkedin.png',    'LinkedIn'],
                        ];
                    @endphp
                    @foreach($platforms as $platform => $details)
                        @if(!empty($SocialMediaContact->$platform))
                        <a href="{{ $SocialMediaContact->$platform }}"
                           class="footer__social-link"
                           target="_blank" rel="noopener noreferrer"
                           aria-label="{{ $details[1] }}">
                            <img src="{{ url('/images/socialmediacontacts/' . $details[0]) }}"
                                 alt="{{ $details[1] }}"
                                 loading="lazy"
                                 class="footer__social-img">
                        </a>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

        </div>

        <div class="footer__bottom">
            <p>جميع حقوق النشر محفوظة لـ {{ $Branch?->branch_name ?? '' }} &copy; {{ date('Y') }}</p>
            <div class="footer__payment">
                <img src="{{ url('/images/socialmediacontacts/backing.png') }}"
                     alt="وسائل الدفع" loading="lazy" style="max-height:28px;">
            </div>
        </div>
    </div>
</footer>