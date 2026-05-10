@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - تواصل معانا';
    $description =
        'تواصل مع فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        ' الآن للاستفسارات والدعم الفني والمبيعات. نحن هنا لخدمتك بكل احترافية.';
    $keywords =
        'تواصل, ' . $ecommerceSharedData['branch']->branch_name . ', دعم فني, خدمة عملاء, استفسارات, فروع, اتصل بنا';
    $og_title = $Page_title;
    $og_description = $description;
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';

@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header flex-column text-center w-100">
                    <h5 class="modal-title CustomerLoginHeadline w-100" id="errorModalLabel">
                        شكرا {{ session('customer_name') }} لرسال رسالتك
                    </h5>

                    <p class="CustomerLoginDescription w-100 mt-2" id="errorDescription">
                        سوف يتم الرد علي رسالتك في اقرب وقت </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to trigger the modal -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>

    <div id="desktop_content_EcommerceAllProductsDivofchangingpages">
        <div class="row inactiveBtnbackground Divofchangingpages">
            <div class="col-12">
                <div class="container h-100">
                    <div class="row h-100">
                        <div class="col-6 m-auto">
                            <div class="row">
                                <div class="col-12 m-auto Divofchangingpagesheadtitle">
                                    تواصل معنا
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 m-auto Divofchangingpagesnavlink">
                                    <a href="{{ route('home') }}" class="Divofchangingpagesnavlink decorationnone">
                                        الرئيسية
                                    </a>
                                    <button class="btn apponwer_systemprimarybtn">
                                        <i class="fas fa-chevron-left p-2"></i>
                                    </button>
                                    <a href="{{ route('EcommerceContactUs') }}"
                                        class="Divofchangingpagesnavlink decorationnone">
                                        تواصل معنا
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="mobile_content_EcommerceAllProductsDivofchangingpages">
        <div class="row inactiveBtnbackground Divofchangingpages">
            <div class="col-12">
                <div class="container h-100">
                    <div class="row h-100">
                        <div class="col-12 m-auto">
                            <div class="row">
                                <div class="col-12 m-auto Divofchangingpagesheadtitle">
                                    تواصل معنا
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="desktop_content_EcommerceKnowAboutUs">

        <div class="container mt-5">
            @if ($ecommerceSharedData['SocialMediaContact'])
                @if (!empty($ecommerceSharedData['SocialMediaContact']->location))
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <iframe src="{{ $ecommerceSharedData['SocialMediaContact']->location }}" width="100%"
                                height="300" style="border:0;" allowfullscreen="" loading="lazy">
                            </iframe>
                        </div>
                    </div>
                @endif
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h4 class="ContactUsHeadline">ارسال رسالة</h4>
                    @if (session('customer_name') === null)
                        <a href="{{ route('CustomerLogin') }}">
                            <div class="mb-3">
                                <textarea class="form-control text-end" id="message" name="message" rows="10" style="resize: none; width: 100%;"
                                    placeholder="اكتب رسالتك هنا"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">إرسال</button>
                        </a>
                    @else
                        <form method="post" action="{{ route('CustomerContactUsMessages') }}"
                            tyle="direction: rtl; text-align: right;" enctype="multipart/form-data">
                            @csrf
                            @method('post')

                            <textarea class="form-control text-end" id="message" name="message" rows="10" style="resize: none; width: 100%;"
                                placeholder="اكتب رسالتك هنا" required></textarea>
                            <button type="submit" class="btn btn-primary w-100">إرسال</button>
                        </form>
                    @endif
                </div>
                <div class="col-md-6 mb-4">
                    <h4 class="ContactUsHeadline">معلومات التواصل</h4>
                    <p class="ContactUsdescription">يسعدنا دعمك على مدار الساعة لأي استفسارات أو مشكلات تواجهك</p>
                    @php
                        if (
                            preg_match(
                                '/!2d([0-9\.\-]+)!3d([0-9\.\-]+)/',
                                $ecommerceSharedData['SocialMediaContact']->location,
                                $matches,
                            )
                        ) {
                            $longitude = $matches[1];
                            $latitude = $matches[2];
                            $direct_url = "https://www.google.com/maps?q={$latitude},{$longitude}";
                        }
                    @endphp
                    <div class="col-12 pb-2 text-end footerfirstdescription">
                        <a href="{{ $direct_url }}" target="_blank">
                            {{ $ecommerceSharedData['branch']->branch_place }}
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                        </a>
                    </div>
                    <div class="col-12 pb-2 text-end footerfirstdescription">
                        <a href="tel:+2{{ $ecommerceSharedData['branch']->branch_phone }}" target="_blank">
                            {{ $ecommerceSharedData['branch']->branch_phone }}
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fas fa-phone-alt"></i>
                            </button>
                        </a>
                    </div>
                    @if ($ecommerceSharedData['branch']->branch_phone2)
                        <div class="col-12 pb-2 text-end footerfirstdescription">
                            <a href="tel:+2{{ $ecommerceSharedData['branch']->branch_phone2 }}" target="_blank">
                                {{ $ecommerceSharedData['branch']->branch_phone2 }}
                                <button class="btn apponwer_systemprimarybtn">
                                    <i class="fas fa-phone-alt"></i>
                                </button>
                            </a>
                        </div>
                    @endif
                    <div class="col-12 pb-2 text-end footerfirstdescription">
                        <a href="https://wa.me/+2{{ $ecommerceSharedData['branch']->branch_phone }}" target="_blank">
                            {{ $ecommerceSharedData['branch']->branch_phone }}
                            <button class="btn apponwer_systemprimarybtn">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        </a>
                    </div>
                    <div class="row m-auto">
                        <div class="col-12 pb-2 text-end footerfirstheadline">
                            تابعنا
                        </div>
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->gmail))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->gmail }}" target="_blank">
                                        <img class="w-100 h-100" src="{{ url('/images/socialmediacontacts/gmail.png') }}"
                                            alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->telegrambot))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->telegrambot }}"
                                        target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/telegrambot.png') }}"
                                            alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->whatsappbot))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->whatsappbot }}"
                                        target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/whatsappbot.png') }}"
                                            alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->whatsappgroup))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->whatsappgroup }}"
                                        target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/whatsappgroup.png') }}"
                                            alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->facebook))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->facebook }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/facebook.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->twitter))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->twitter }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/twitter.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->instagram))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->instagram }}"
                                        target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/instagram.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->linkedin))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->linkedin }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/linkedin.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->youtube))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->youtube }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/youtube.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->tiktok))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->tiktok }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/tiktok.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->snapchat))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->snapchat }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/snapchat.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->whatsapp))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->whatsapp }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/whatsapp.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->telegram))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->telegram }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/telegram.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->pinterest))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->pinterest }}"
                                        target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/pinterest.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->reddit))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->reddit }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/reddit.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                        @if ($ecommerceSharedData['SocialMediaContact'])
                            @if (!empty($ecommerceSharedData['SocialMediaContact']->discord))
                                <div class="col-2 m-auto p-2">
                                    <a href="{{ $ecommerceSharedData['SocialMediaContact']->discord }}" target="_blank">
                                        <img class="w-100 h-100"
                                            src="{{ url('/images/socialmediacontacts/discord.png') }}" alt="Logo">
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    </div>

    <div id="mobile_content_EcommerceKnowAboutUs">

        <div class="mt-4 contact-container">

            @if ($ecommerceSharedData['SocialMediaContact'])
                @if (!empty($ecommerceSharedData['SocialMediaContact']->location))
                    <div class="mb-3">
                        <iframe src="{{ $ecommerceSharedData['SocialMediaContact']->location }}" width="100%"
                            height="250" style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                @endif
            @endif

            <!-- معلومات التواصل -->
            <div class="card p-3 mb-3">
                <h5 class="mb-2 ContactUsHeadline">معلومات التواصل</h5>
                <p class="ContactUsdescription">يسعدنا دعمك بشأن خدمة العملاء أو المنتجات أو الموقع الإلكتروني، أو لأي
                    معلومات أخرى.</p>
                <div class="row">
                    <div class="col-12 p-5">
                        <div class="row">
                            @php
                                if (
                                    preg_match(
                                        '/!2d([0-9\.\-]+)!3d([0-9\.\-]+)/',
                                        $ecommerceSharedData['SocialMediaContact']->location,
                                        $matches,
                                    )
                                ) {
                                    $longitude = $matches[1];
                                    $latitude = $matches[2];
                                    $direct_url = "https://www.google.com/maps?q={$latitude},{$longitude}";
                                }
                            @endphp

                            <div class="col-12 footerfirstdescription text-end">
                                <a href="{{ $direct_url }}" target="_blank" class="footer-contact">
                                    <span>{{ $ecommerceSharedData['branch']->branch_place }}</span>
                                    <div class="icon-btn">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </a>
                            </div>

                            <div class="col-12 footerfirstdescription text-end">
                                <a href="tel:+2{{ $ecommerceSharedData['branch']->branch_phone }}" target="_blank"
                                    class="footer-contact">
                                    <span>{{ $ecommerceSharedData['branch']->branch_phone }}</span>
                                    <div class="icon-btn">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                </a>
                            </div>

                            @if ($ecommerceSharedData['branch']->branch_phone2)
                                <div class="col-12 footerfirstdescription text-end">
                                    <a href="tel:+2{{ $ecommerceSharedData['branch']->branch_phone2 }}" target="_blank"
                                        class="footer-contact">
                                        <span>{{ $ecommerceSharedData['branch']->branch_phone2 }}</span>
                                        <div class="icon-btn">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <div class="col-12 footerfirstdescription text-end">
                                <a href="https://wa.me/+2{{ $ecommerceSharedData['branch']->branch_phone }}"
                                    target="_blank" class="footer-contact">
                                    <span>{{ $ecommerceSharedData['branch']->branch_phone }}</span>
                                    <div class="icon-btn">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 p-5">
                        <div class="row m-auto">
                            <div class="col-12 pb-2 text-center footerfirstheadline">
                                تابعنا
                            </div>
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->gmail))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->gmail }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/gmail.png') }}" alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->telegrambot))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->telegrambot }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/telegrambot.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->whatsappbot))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->whatsappbot }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/whatsappbot.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->whatsappgroup))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->whatsappgroup }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/whatsappgroup.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->facebook))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->facebook }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/facebook.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->twitter))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->twitter }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/twitter.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->instagram))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->instagram }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/instagram.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->linkedin))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->linkedin }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/linkedin.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->youtube))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->youtube }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/youtube.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->tiktok))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->tiktok }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/tiktok.png') }}" alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->snapchat))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->snapchat }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/snapchat.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->whatsapp))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->whatsapp }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/whatsapp.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->telegram))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->telegram }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/telegram.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->pinterest))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->pinterest }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/pinterest.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->reddit))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->reddit }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/reddit.png') }}" alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                            @if ($ecommerceSharedData['SocialMediaContact'])
                                @if (!empty($ecommerceSharedData['SocialMediaContact']->discord))
                                    <div class="col-2 m-auto p-2">
                                        <a href="{{ $ecommerceSharedData['SocialMediaContact']->discord }}"
                                            target="_blank">
                                            <img class="w-100 h-100"
                                                src="{{ url('/images/socialmediacontacts/discord.png') }}"
                                                alt="Logo">
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- نموذج التواصل -->
            <div class="card p-3">
                <h5 class="mb-2 ContactUsHeadline">ارسال رسالة</h5>
                @if (session('customer_name') === null)
                    <a href="{{ route('CustomerLogin') }}">
                        <div class="mb-3">
                            <textarea class="form-control text-end" id="message" name="message" rows="10"
                                style="resize: none; width: 100%;" placeholder="اكتب رسالتك هنا"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">إرسال</button>
                    </a>
                @else
                    <form method="post" action="{{ route('CustomerContactUsMessages') }}"
                        tyle="direction: rtl; text-align: right;" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="mb-3">
                            <textarea class="form-control text-end" id="message" name="message" rows="10"
                                style="resize: none; width: 100%;" placeholder="اكتب رسالتك هنا" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">إرسال</button>
                    </form>
                @endif

            </div>

        </div>

    </div>

@endsection
