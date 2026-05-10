{{-- resources/views/EcommerceContactUs/index.blade.php --}}
@extends('layouts.app')

@section('title', $ecommerceSharedData['branch']->branch_name . ' - تواصل معانا')

@section('content')

    {{-- Page Header --}}
    <div class="row inactiveBtnbackground Divofchangingpages">
        <div class="col-12">
            <div class="container">
                <div class="Divofchangingpagesheadtitle">تواصل معنا</div>
                <div class="Divofchangingpagesnavlink">
                    <a href="{{ route('home') }}" class="decorationnone">الرئيسية</a>
                    <i class="fas fa-chevron-left p-2"></i>
                    <span>تواصل معنا</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="row g-4">

            {{-- الخريطة --}}
            @if (!empty($ecommerceSharedData['social']?->location))
                <div class="col-12">
                    <iframe src="{{ $ecommerceSharedData['social']->location }}" width="100%" height="300"
                        style="border:0; border-radius: var(--radius-md);" allowfullscreen loading="lazy">
                    </iframe>
                </div>
            @endif

            {{-- الفورم --}}
            <div class="col-md-6">
                <h4 class="ContactUsHeadline">ارسال رسالة</h4>
                @if (session('customer_name') === null)
                    <a href="{{ route('CustomerLogin') }}" class="btn hero__btn w-100">
                        سجل دخول لإرسال رسالة
                    </a>
                @else
                    <form method="POST" action="{{ route('CustomerContactUsMessages') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control text-end" name="message" rows="10" style="resize: none;"
                                placeholder="اكتب رسالتك هنا" required></textarea>
                        </div>
                        <button type="submit" class="btn hero__btn w-100">إرسال</button>
                    </form>
                @endif
            </div>

            {{-- معلومات التواصل --}}
            <div class="col-md-6">
                <h4 class="ContactUsHeadline">معلومات التواصل</h4>
                <p class="ContactUsdescription">يسعدنا دعمك على مدار الساعة</p>

                @php
                    $mapUrl = $ecommerceSharedData['mapUrl'];
                    $branch = $ecommerceSharedData['branch'];
                    $social = $ecommerceSharedData['social'];
                @endphp

                <div class="d-flex flex-column gap-2 mb-4">
                    <a href="{{ $mapUrl }}" target="_blank" class="footer-contact">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        {{ $branch->branch_place }}
                    </a>
                    <a href="tel:+2{{ $branch->branch_phone }}" class="footer-contact">
                        <i class="fas fa-phone-alt text-primary me-2"></i>
                        {{ $branch->branch_phone }}
                    </a>
                    @if ($branch->branch_phone2)
                        <a href="tel:+2{{ $branch->branch_phone2 }}" class="footer-contact">
                            <i class="fas fa-phone-alt text-primary me-2"></i>
                            {{ $branch->branch_phone2 }}
                        </a>
                    @endif
                    @if ($social?->whatsapp)
                        <a href="{{ $social->whatsapp }}" target="_blank" class="footer-contact">
                            <i class="fab fa-whatsapp text-success me-2"></i>
                            {{ $branch->branch_phone }}
                        </a>
                    @endif
                </div>

                {{-- Social Media Icons --}}
                @if ($social)
                    <h5 class="footerfirstheadline mb-3">تابعنا</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $platforms = [
                                'gmail' => 'gmail.png',
                                'telegrambot' => 'telegrambot.png',
                                'whatsappbot' => 'whatsappbot.png',
                                'whatsappgroup' => 'whatsappgroup.png',
                                'facebook' => 'facebook.png',
                                'twitter' => 'twitter.png',
                                'instagram' => 'instagram.png',
                                'linkedin' => 'linkedin.png',
                                'youtube' => 'youtube.png',
                                'tiktok' => 'tiktok.png',
                                'snapchat' => 'snapchat.png',
                                'whatsapp' => 'whatsapp.png',
                                'telegram' => 'telegram.png',
                                'pinterest' => 'pinterest.png',
                                'reddit' => 'reddit.png',
                                'discord' => 'discord.png',
                            ];
                        @endphp
                        @foreach ($platforms as $platform => $img)
                            @if (!empty($social->$platform))
                                <a href="{{ $social->$platform }}" target="_blank" rel="noopener">
                                    <img src="{{ url('/images/socialmediacontacts/' . $img) }}" alt="{{ $platform }}"
                                        width="36" height="36" loading="lazy">
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Success Modal --}}
    @if (session('success'))
        <div class="modal fade show d-block" id="successModal" tabindex="-1" style="background:rgba(0,0,0,.4)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <h5 class="CustomerLoginHeadline">شكراً {{ session('customer_name') }} لإرسال رسالتك</h5>
                    <p class="CustomerLoginDescription mt-2">سيتم الرد عليك في أقرب وقت</p>
                    <button onclick="document.getElementById('successModal').remove()"
                        class="btn hero__btn mt-3">حسناً</button>
                </div>
            </div>
        </div>
    @endif

@endsection
