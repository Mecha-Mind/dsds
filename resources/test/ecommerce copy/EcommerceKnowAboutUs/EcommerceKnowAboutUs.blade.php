@php
    /*
     | AboutUs model:
     | ← تأكد من اسم الـ columns في الـ DB
     | الأسماء المتوقعة: whoweare, ourmission, ourvision, ourservices
    */
    try {
        $aboutData = \App\Models\AboutUs::first();
    } catch (\Exception $e) {
        $aboutData = null;
    }

    try {
        $ourServices = \App\Models\Ourservice::where('ourservice_displaystatus', 1)
            ->orderBy('updated_at', 'desc')
            ->get();
    } catch (\Exception $e) {
        $ourServices = collect();
    }

    /*
     | Placeholder لو مفيش بيانات في الـ DB
    */
    $aboutPlaceholder = [
        'whoweare' =>
            'نحن فريق متخصص في مجال الإلكترونيات وخدمات الصيانة، نسعى دائماً لتقديم أفضل الخدمات والمنتجات لعملائنا.',
        'ourmission' => 'رسالتنا تقديم منتجات أصيلة بأسعار تنافسية مع خدمة عملاء متميزة على مدار الساعة.',
        'ourvision' => 'رؤيتنا أن نكون الخيار الأول للمستهلك في مجال الإلكترونيات والصيانة في المنطقة.',
        'ourservices' => 'نقدم خدمات بيع الأجهزة الإلكترونية، صيانة الموبايلات، وتوفير جميع الإكسسوارات.',
    ];
@endphp

@extends('layouts.app')

@section('title', 'اعرف عنا — ' . ($ecommerceSharedData['branchName'] ?? ''))
@section('description', 'تعرف على ' . ($ecommerceSharedData['branchName'] ?? '') . ' وخدماتنا المتميزة.')

@section('content')

    <x-page-header title="معلومات عنا" :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'معلومات عنا', 'url' => route('EcommerceKnowAboutUs')],
    ]" />

    <div class="container py-5">

        {{-- ══ الكاردات الأربعة ══ --}}
        <div class="row g-4 mb-5">

            @php
                $sections = [
                    ['key' => 'whoweare', 'title' => 'من نحن', 'icon' => 'bi-people'],
                    ['key' => 'ourmission', 'title' => 'رسالتنا', 'icon' => 'bi-bullseye'],
                    ['key' => 'ourvision', 'title' => 'رؤيتنا', 'icon' => 'bi-eye'],
                    ['key' => 'ourservices', 'title' => 'خدماتنا', 'icon' => 'bi-stars'],
                ];
            @endphp

            @foreach ($sections as $section)
                <div class="col-md-6">
                    <div class="about-card h-100">
                        <div class="about-card__icon">
                            <i class="bi {{ $section['icon'] }}" aria-hidden="true"></i>
                        </div>
                        <h2 class="about-card__title">{{ $section['title'] }}</h2>
                        <p class="about-card__text">
                            {{ $aboutData?->{$section['key']} ?? $aboutPlaceholder[$section['key']] }}
                        </p>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- ══ فريق العمل ══ --}}
        <div class="about-team">
            <h2 class="section-title mb-4">
                فريق {{ $ecommerceSharedData['branchName'] ?? 'شركتنا' }}
            </h2>

            @if ($ourServices->count())
                <div class="row g-4 justify-content-center">
                    @foreach ($ourServices as $member)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="team-card">
                                <div class="team-card__img-wrap">
                                    <img src="{{ asset('images/ourserviceimages/' . $member->ourservice_image) }}"
                                        alt="{{ $member->ourservice_name }}" width="292" height="320" loading="lazy">
                                </div>
                                <p class="team-card__name">{{ $member->ourservice_name }}</p>
                                @if ($member->ourservice_jobtitle ?? null)
                                    <p class="team-card__role">{{ $member->ourservice_jobtitle }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Placeholder لو مفيش بيانات ──
             ← لما تتضافوا Ourservice records في الـ DB هيظهروا تلقائياً
        --}}
                <div class="row g-4 justify-content-center">
                    @foreach ([['name' => 'أحمد محمد', 'role' => 'مدير عام', 'img' => null], ['name' => 'محمد محمد', 'role' => 'مدير مبيعات', 'img' => null], ['name' => 'أحمد محمد', 'role' => 'مدير مبيعات', 'img' => null], ['name' => 'أحمد محمد', 'role' => 'موظف صيانة', 'img' => null]] as $member)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="team-card">
                                <div class="team-card__img-wrap team-card__img-wrap--placeholder">
                                    <i class="bi bi-person-fill" aria-hidden="true"></i>
                                </div>
                                <p class="team-card__name">{{ $member['name'] }}</p>
                                <p class="team-card__role">{{ $member['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- ══ بيانات الفرع ══ --}}
        <div class="about-branch mt-5">
            <div class="about-branch__inner">
                <img src="{{ asset('images/brancheslogo/' . ($ecommerceSharedData['branchImage'] ?? '')) }}"
                    alt="{{ $ecommerceSharedData['branchName'] ?? '' }}" class="about-branch__logo" loading="lazy"
                    width="120" height="48">

                <div class="about-branch__info">
                    <p>
                        <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                        {{ $ecommerceSharedData['branch']?->branch_place ?? '' }}
                    </p>
                    <p>
                        <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                        {{ $ecommerceSharedData['phone'] ?? '' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

@endsection
