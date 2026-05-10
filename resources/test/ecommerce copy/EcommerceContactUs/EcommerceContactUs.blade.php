@extends('layouts.app')
@php
    $Page_title = $ecommerceSharedData['pageTitle'] ?? 'تواصل معنا';
    $description = 'تواصل مع فرع ' . $ecommerceSharedData['branchName'] ?? 'فرعنا' . ' لأي استفسارات أو دعم.';
         '، عروض حصرية على المنتجات والخدمات لفترة محدودة.';
    $keywords = 'عروض, خصومات, ' . $ecommerceSharedData['branch']->branch_name . ', منتجات, خدمات, توفير, أسعار خاصة';
    $og_title = 'عروض فرع ' . $ecommerceSharedData['branch']->branch_name;
    $og_description =
        'استفيد من أفضل العروض والخصومات المتوفرة الآن في فرع ' . $ecommerceSharedData['branch']->branch_name . ' – لا تفوت الفرصة!';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';
@endphp


@section('title', $Page_title)
@section('description', $description)
@section('title', 'تواصل معنا — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')

    <x-page-header title="تواصل معنا" :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'تواصل معنا', 'url' => route('EcommerceContactUs')],
    ]" />

    <div class="container py-5">
        <div class="row g-4">

            {{-- ── الخريطة ── --}}
            @if (!empty($ecommerceSharedData['social']?->location))
                <div class="col-12">
                    <div class="contact-map-wrap">
                        <iframe src="{{ $ecommerceSharedData['social']->location }}" width="100%" height="320"
                            style="border:0; border-radius:var(--radius-md); display:block;" allowfullscreen loading="lazy"
                            title="موقعنا على الخريطة">
                        </iframe>
                    </div>
                </div>
            @endif


            {{-- ── معلومات التواصل ── --}}
            <div class="col-md-6">
                <div class="contact-info-card">
                    <h2 class="contact-info-card__title">معلومات التواصل</h2>
                    <p class="contact-info-card__desc">
                        يسعدنا أن نسمع منك بشأن خدمة العملاء أو المنتجات أو الموقع الإلكتروني
                        أو أي موضوعات ترغب في مشاركتها معنا. سنكون مستعدين لتلبية توقعاتك وإدهاشك.
                    </p>

                    @php
                        $branch = $ecommerceSharedData['branch'];
                        $mapUrl = $ecommerceSharedData['mapUrl'] ?? '#';
                    @endphp

                    <ul class="contact-info-list">
                        @if ($branch?->branch_place)
                            <li class="contact-info-list__item">
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener">
                                    {{ $branch->branch_place }}
                                </a>
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            </li>
                        @endif

                        @if ($branch?->branch_phone)
                            <li class="contact-info-list__item">
                                <a href="tel:+2{{ $branch->branch_phone }}">
                                    {{ $branch->branch_phone }}
                                </a>
                                <i class="bi bi-telephone" aria-hidden="true"></i>
                            </li>
                        @endif

                        @if ($branch?->branch_phone2)
                            <li class="contact-info-list__item">
                                <a href="tel:+2{{ $branch->branch_phone2 }}">
                                    {{ $branch->branch_phone2 }}
                                </a>
                                <i class="bi bi-telephone" aria-hidden="true"></i>
                            </li>
                        @endif

                        @php
                            $openTime = '10 صباحاً إلى 11 مساءً';
                        @endphp
                        <li class="contact-info-list__item">
                            <span>من {{ $openTime }}</span>
                            <i class="bi bi-clock" aria-hidden="true"></i>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ── الفورم ── --}}
            <div class="col-md-6">
                <div class="contact-form-card">
                    <h2 class="contact-form-card__title">معلومات التواصل</h2>

                    @if (session('success'))
                        <div class="alert alert-success text-end" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger text-end" role="alert">
                            @foreach ($errors->all() as $e)
                                <div>{{ $e }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('CustomerContactUsMessages') }}" id="contactForm" novalidate>
                        @csrf

                        {{-- الاسم --}}
                        <div class="contact-form-card__field">
                            <label for="contact_name" class="form-label">
                                الاسم <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="contact_name" name="contactusmessage_customername"
                                class="form-control @error('contactusmessage_customername') is-invalid @enderror"
                                value="{{ old('contactusmessage_customername', session('customer_name') ?? '') }}"
                                 required autocomplete="off">
                            @error('contactusmessage_customername')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="contact-form-card__field">
                            <label for="contact_email" class="form-label">
                                البريد الإلكتروني <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="contact_email" name="contactusmessage_customeremail"
                                class="form-control @error('contactusmessage_customeremail') is-invalid @enderror"
                                value="{{ old('contactusmessage_customeremail') }}"
                                required autocomplete="off">
                            @error('contactusmessage_customeremail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- الرسالة --}}
                        <div class="contact-form-card__field">
                            <label for="contact_message" class="form-label">
                                الرسالة <span class="text-danger">*</span>
                            </label>
                            <textarea id="contact_message" name="contactusmessage_customermessage"
                                class="form-control @error('contactusmessage_customermessage') is-invalid @enderror" rows="5"
                                placeholder="اكتب رسالتك هنا..." required maxlength="2000">{{ old('contactusmessage_customermessage') }}</textarea>
                            @error('contactusmessage_customermessage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn hero__btn w-100">
                            إرسال
                        </button>
                    </form>
                </div>
            </div>


        </div>
    </div>

@endsection
