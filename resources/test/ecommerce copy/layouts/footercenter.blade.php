<div id="desktop_content_footercenter">
    <div class="row Secondarybgbackground">
        <div class="container pt-5 pb-5">
            <div class="row">
                <div class="col-4">
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-end footerfirstheadline">
                            تابعنا
                        </div>
                        <div class="social-icons-container">
                            @if ($SocialMediaContact)
                                @php
                                    $socialPlatforms = [
                                        'gmail' => ['gmail.png', 'Gmail'],
                                        'telegrambot' => ['telegrambot.png', 'Telegram Bot'],
                                        'whatsappbot' => ['whatsappbot.png', 'WhatsApp Bot'],
                                        'whatsappgroup' => ['whatsappgroup.png', 'WhatsApp Group'],
                                        'facebook' => ['facebook.png', 'Facebook'],
                                        'twitter' => ['twitter.png', 'Twitter'],
                                        'instagram' => ['instagram.png', 'Instagram'],
                                        'linkedin' => ['linkedin.png', 'LinkedIn'],
                                        'youtube' => ['youtube.png', 'YouTube'],
                                        'tiktok' => ['tiktok.png', 'TikTok'],
                                        'snapchat' => ['snapchat.png', 'Snapchat'],
                                        'whatsapp' => ['whatsapp.png', 'WhatsApp'],
                                        'telegram' => ['telegram.png', 'Telegram'],
                                        'pinterest' => ['pinterest.png', 'Pinterest'],
                                        'reddit' => ['reddit.png', 'Reddit'],
                                        'discord' => ['discord.png', 'Discord'],
                                    ];
                                @endphp

                                @foreach ($socialPlatforms as $platform => $details)
                                    @if (!empty($SocialMediaContact->$platform))
                                        <a href="{{ $SocialMediaContact->$platform }}" class="social-icon-link"
                                            target="_blank" aria-label="{{ $details[1] }}"
                                            title="{{ $details[1] }}">
                                            <img src="{{ url('/images/socialmediacontacts/' . $details[0]) }}"
                                                alt="{{ $details[1] }} Logo" loading="lazy" class="social-icon">
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-end footerfirstheadline">
                            المعلومات
                        </div>
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="{{ route('EcommerceKnowAboutUs') }}"
                                class="footerfirstdescription decorationnone w-100">

                                اعرف عنا
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="{{ route('EcommerceContactUs') }}"
                                class="footerfirstdescription decorationnone w-100">
                                تواصل معانا
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="{{ route('UserPersonalPage') }}"
                                class="footerfirstdescription decorationnone w-100">
                                حسابي
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="{{ route('PrivacyPolicy') }}" class="footerfirstdescription decorationnone w-100">
                                سياسات الخصوصية
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="{{ route('TermsAndConditions') }}"
                                class="footerfirstdescription decorationnone w-100">
                                الاحكام و الشروط
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-end footerfirstheadline">
                            التصنيفات
                        </div>
                        @if ($FooterCategories)
                            @foreach ($FooterCategories as $FooterCategory)
                                <div class="col-12 pb-2 text-end footerfirstdescription">
                                    <a href="{{ route('CategoryProduct', $FooterCategory->category_id) }}"
                                        class="footerfirstdescription decorationnone w-100">
                                        {{ $FooterCategory->category_name }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div class="col-6">
                        </div>
                        <div
                            class="col-6 pb-2 d-flex align-items-center justify-content-end text-right footerfirstdescription">
                            <img class="homepagelogoimage" style="height:48px;"
                                src="{{ url('/images/brancheslogo/' . $Branch->branch_image) }}" alt="Logo">
                        </div>
                        @php
                            if (
                                preg_match('/!2d([0-9\.\-]+)!3d([0-9\.\-]+)/', $SocialMediaContact->location, $matches)
                            ) {
                                $longitude = $matches[1];
                                $latitude = $matches[2];
                                $direct_url = "https://www.google.com/maps?q={$latitude},{$longitude}";
                            }
                        @endphp
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="{{ $direct_url }}" target="_blank">
                                {{ $Branch->branch_place }}
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-map-marker-alt"></i>
                                </button>
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="tel:+2{{ $Branch->branch_phone }}" target="_blank">
                                {{ $Branch->branch_phone }}
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-phone-alt"></i>
                                </button>
                            </a>
                        </div>
                        @if ($Branch->branch_phone2)
                            <div class="col-12 pb-2 text-end footerfirstdescription">
                                <a href="tel:+2{{ $Branch->branch_phone2 }}" target="_blank">
                                    {{ $Branch->branch_phone2 }}
                                    <button class="btn apponwer_systemprimarybtn">
                                        <i class="fas fa-phone-alt"></i>
                                    </button>
                                </a>
                            </div>
                        @endif
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="https://wa.me/+2{{ $Branch->branch_phone }}" target="_blank">
                                {{ $Branch->branch_phone }}
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="mobile_content_footercenter">
    <div class="row Secondarybgbackground pt-3">
        <div class="col-12 p-3 m-auto">
            <div class="row">
                <div class="col-12 p-5">
                    <div class="row">
                        <div
                            class="col-12 pb-2 d-flex align-items-center justify-content-center text-right footerfirstdescription">
                            <img class="homepagelogoimage" style="height:48px;"
                                src="{{ url('/images/brancheslogo/' . $Branch->branch_image) }}" alt="Logo">
                        </div>
                        @php
                            if (
                                preg_match('/!2d([0-9\.\-]+)!3d([0-9\.\-]+)/', $SocialMediaContact->location, $matches)
                            ) {
                                $longitude = $matches[1];
                                $latitude = $matches[2];
                                $direct_url = "https://www.google.com/maps?q={$latitude},{$longitude}";
                            }
                        @endphp


                        <div class="col-12 footerfirstdescription text-end">
                            <a href="{{ $direct_url }}" target="_blank" class="footer-contact">
                                <span>{{ $Branch->branch_place }}</span>
                                <div class="icon-btn">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </a>
                        </div>

                        <div class="col-12 footerfirstdescription text-end">
                            <a href="tel:+2{{ $Branch->branch_phone }}" target="_blank" class="footer-contact">
                                <span>{{ $Branch->branch_phone }}</span>
                                <div class="icon-btn">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                            </a>
                        </div>

                        @if ($Branch->branch_phone2)
                            <div class="col-12 footerfirstdescription text-end">
                                <a href="tel:+2{{ $Branch->branch_phone2 }}" target="_blank" class="footer-contact">
                                    <span>{{ $Branch->branch_phone2 }}</span>
                                    <div class="icon-btn">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <div class="col-12 footerfirstdescription text-end">
                            <a href="https://wa.me/+2{{ $Branch->branch_phone }}" target="_blank"
                                class="footer-contact">
                                <span>{{ $Branch->branch_phone }}</span>
                                <div class="icon-btn">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
                <hr class="w-80 footerfirstdescription m-auto overflowhidden">
                <div class="col-12 p-5">
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-center footerfirstheadline">
                            تابعنا
                        </div>
                        <div class="social-icons-container">
                            @if ($SocialMediaContact)
                                @php
                                    $socialPlatforms = [
                                        'gmail' => ['gmail.png', 'Gmail'],
                                        'telegrambot' => ['telegrambot.png', 'Telegram Bot'],
                                        'whatsappbot' => ['whatsappbot.png', 'WhatsApp Bot'],
                                        'whatsappgroup' => ['whatsappgroup.png', 'WhatsApp Group'],
                                        'facebook' => ['facebook.png', 'Facebook'],
                                        'twitter' => ['twitter.png', 'Twitter'],
                                        'instagram' => ['instagram.png', 'Instagram'],
                                        'linkedin' => ['linkedin.png', 'LinkedIn'],
                                        'youtube' => ['youtube.png', 'YouTube'],
                                        'tiktok' => ['tiktok.png', 'TikTok'],
                                        'snapchat' => ['snapchat.png', 'Snapchat'],
                                        'whatsapp' => ['whatsapp.png', 'WhatsApp'],
                                        'telegram' => ['telegram.png', 'Telegram'],
                                        'pinterest' => ['pinterest.png', 'Pinterest'],
                                        'reddit' => ['reddit.png', 'Reddit'],
                                        'discord' => ['discord.png', 'Discord'],
                                    ];
                                @endphp

                                @foreach ($socialPlatforms as $platform => $details)
                                    @if (!empty($SocialMediaContact->$platform))
                                        <a href="{{ $SocialMediaContact->$platform }}" class="social-icon-link"
                                            target="_blank" aria-label="{{ $details[1] }}"
                                            title="{{ $details[1] }}">
                                            <img src="{{ url('/images/socialmediacontacts/' . $details[0]) }}"
                                                alt="{{ $details[1] }} Logo" loading="lazy" class="social-icon">
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <hr class="footerfirstdescription m-auto overflowhidden">
                <div class="col-12 p-5">
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-center footerfirstheadline">
                            التصنيفات
                        </div>
                        @if ($FooterCategories)
                            @foreach ($FooterCategories as $FooterCategory)
                                <div class="col-12 pb-2 text-center footerfirstdescription">
                                    <a href="{{ route('CategoryProduct', $FooterCategory->category_id) }}"
                                        class="footerfirstdescription decorationnone w-100">
                                        {{ $FooterCategory->category_name }}
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <hr class="footerfirstdescription m-auto overflowhidden">
                <div class="col-12 p-5">
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-center footerfirstheadline">
                            المعلومات
                        </div>
                        <div class="col-12 pb-2 text-center footerfirstdescription">
                            <a href="{{ route('EcommerceKnowAboutUs') }}"
                                class="footerfirstdescription decorationnone w-100">
                                اعرف عنا
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-center footerfirstdescription">
                            <a href="{{ route('EcommerceContactUs') }}"
                                class="footerfirstdescription decorationnone w-100">
                                تواصل معانا
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-center footerfirstdescription">
                            <a href="{{ route('UserPersonalPage') }}"
                                class="footerfirstdescription decorationnone w-100">
                                حسابي
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-center footerfirstdescription">
                            <a href="{{ route('PrivacyPolicy') }}"
                                class="footerfirstdescription decorationnone w-100">
                                سياسات الخصوصية
                            </a>
                        </div>
                        <div class="col-12 pb-2 text-center footerfirstdescription">
                            <a href="{{ route('TermsAndConditions') }}"
                                class="footerfirstdescription decorationnone w-100">
                                الاحكام و الشروط
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
