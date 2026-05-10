{{-- resources/views/EcommerceKnowAboutUs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'اعرف عنا — ' . ($branchName ?? ''))

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">معلومات عنا</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item active">اعرف عنا</li>
            </ol>
        </nav>
    </div>
</div>

{{-- About Content --}}
<section class="section-padding">
    <div class="container">
        @if($aboutUs)
        <div class="row g-5">

            {{-- من نحن --}}
            <div class="col-lg-6">
                <div class="about-card">
                    <h2 class="about-card__title">من نحن</h2>
                    <p class="about-card__text">{{ $aboutUs->whoweare }}</p>
                </div>
            </div>

            {{-- رسالتنا --}}
            <div class="col-lg-6">
                <div class="about-card">
                    <h2 class="about-card__title">رسالتنا</h2>
                    <p class="about-card__text">{{ $aboutUs->ourmission }}</p>
                </div>
            </div>

            {{-- رؤيتنا --}}
            <div class="col-lg-6">
                <div class="about-card">
                    <h2 class="about-card__title">رؤيتنا</h2>
                    <p class="about-card__text">{{ $aboutUs->ourvision }}</p>
                </div>
            </div>

            {{-- خدماتنا --}}
            <div class="col-lg-6">
                <div class="about-card">
                    <h2 class="about-card__title">خدماتنا</h2>
                    <p class="about-card__text">{{ $aboutUs->ourservices }}</p>
                </div>
            </div>

        </div>
        @endif

        {{-- فريق العمل --}}
        @if(isset($Ourservices) && $Ourservices->count())
        <div class="mt-5">
            <h2 class="section-title text-end mb-4">فريق شركة المتخصص</h2>
            <div class="row g-4 justify-content-center">
                @foreach($Ourservices as $service)
                <div class="col-6 col-md-4 col-lg-3 text-center">
                    <div class="team-card">
                        <img src="{{ asset('images/ourservices/' . $service->ourservice_image) }}"
                             alt="{{ $service->ourservice_name }}"
                             class="team-card__img"
                             loading="lazy">
                        <div class="team-card__name">{{ $service->ourservice_name }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

@endsection


<!--  -->
// app/Http/Controllers/Ecommerce/EcommerceKnowAboutUs/EcommerceKnowAboutUsController.php

use App\Services\SharedDataService;
use App\Models\AboutUs;
use App\Models\Ourservice;

public function EcommerceKnowAboutUs()
{
    $data = SharedDataService::get();
    $data['aboutUs']    = AboutUs::first();
    $data['Ourservices'] = Ourservice::where('ourservice_displaystatus', 1)->get();
    $data['pageTitle']  = 'اعرف عنا';
    return view('EcommerceKnowAboutUs.index', $data);
}

<!--  -->


{{-- resources/views/EcommerceContactUs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'تواصل معنا — ' . ($branchName ?? ''))

@section('content')

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">تواصل معنا</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">تواصل معنا</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">

            {{-- الخريطة --}}
            <div class="col-lg-7">
                @if(isset($SocialMediaContact) && $SocialMediaContact?->location)
                <div class="contact-map">
                    <iframe
                        src="{{ $SocialMediaContact->location }}"
                        width="100%"
                        height="400"
                        style="border:0; border-radius: var(--radius-md);"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
                @endif
            </div>

            {{-- معلومات التواصل + الفورم --}}
            <div class="col-lg-5">
                <div class="contact-info mb-4">
                    <h2 class="section-title mb-3">معلومات التواصل</h2>
                    @if(isset($Branch) && $Branch)
                    <p>
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        {{ $Branch->branch_place }}
                    </p>
                    <p>
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        {{ $Branch->branch_place }}
                    </p>
                    <p>
                        <a href="tel:+2{{ $Branch->branch_phone }}">
                            <i class="fas fa-phone-alt text-primary me-2"></i>
                            {{ $Branch->branch_phone }}
                        </a>
                    </p>
                    @if($Branch->branch_phone2)
                    <p>
                        <a href="tel:+2{{ $Branch->branch_phone2 }}">
                            <i class="fas fa-phone-alt text-primary me-2"></i>
                            {{ $Branch->branch_phone2 }}
                        </a>
                    </p>
                    @endif
                    @endif
                </div>

                {{-- Contact Form --}}
                <form method="POST" action="{{ route('CustomerContactUsMessages') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="contactusmessage_customername"
                               class="form-control" placeholder="الاسم" required>
                    </div>
                    <div class="mb-3">
                        <input type="tel" name="contactusmessage_customerphone"
                               class="form-control" placeholder="رقم التليفون" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="contactusmessage_customeremail"
                               class="form-control" placeholder="البريد الإلكتروني">
                    </div>
                    <div class="mb-3">
                        <textarea name="contactusmessage_customermessage"
                                  class="form-control" rows="4"
                                  placeholder="رسالتك" required></textarea>
                    </div>
                    <button type="submit" class="btn hero__btn w-100">إرسال</button>
                </form>
            </div>

        </div>
    </div>
</section>

@endsection


<!--  -->

// Controller
use App\Services\SharedDataService;

public function EcommerceContactUs()
{
    $data = SharedDataService::get();
    $data['pageTitle'] = 'تواصل معنا';
    return view('EcommerceContactUs.index', $data);
}



<!--  -->

{{-- resources/views/EcommerceOffers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'عروض الصيانة — ' . ($branchName ?? ''))

@section('content')

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">عروض الصيانة</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">عروض الصيانة</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="offers-grid">
            @forelse($Offersfromtheowners as $offer)
            <a href="{{ $offer->offerfromtheowner_url ?? '#' }}"
               class="offer-banner"
               target="{{ $offer->offerfromtheowner_url ? '_blank' : '_self' }}"
               rel="noopener">
                <img src="{{ asset('images/Offersfromtheowner/' . $offer->offerfromtheowner_image) }}"
                     alt="{{ $offer->offerfromtheowner_headline }}"
                     loading="lazy">
            </a>
            @empty
            <p class="text-center text-muted py-5">لا توجد عروض حالياً</p>
            @endforelse
        </div>
    </div>
</section>

@endsection

<!--  -->

// Controller
use App\Services\SharedDataService;
use App\Models\Offersfromtheowner;

public function EcommerceOffers()
{
    $data = SharedDataService::get();
    $data['Offersfromtheowners'] = Offersfromtheowner::where('offerfromtheowner_active', 1)
        ->whereNotNull('offerfromtheowner_image')
        ->orderBy('updated_at', 'desc')
        ->get();
    $data['pageTitle'] = 'عروض الصيانة';
    return view('EcommerceOffers.index', $data);
}


<!--  -->

{{-- resources/views/PrivacyPolicy/index.blade.php --}}
@extends('layouts.app')

@section('title', 'سياسة الخصوصية — ' . ($branchName ?? ''))

@section('content')

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">سياسة الخصوصية</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">سياسة الخصوصية</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="policy-content">
            <h2>جمع المعلومات</h2>
            <p>نحن نجمع المعلومات التي تقدمها لنا مباشرة عند استخدام خدماتنا...</p>

            <h2>استخدام المعلومات</h2>
            <p>نستخدم المعلومات التي نجمعها لتقديم وتحسين خدماتنا...</p>

            <h2>مشاركة المعلومات</h2>
            <p>لا نبيع أو نشارك معلوماتك الشخصية مع أطراف ثالثة...</p>

            <h2>سياسة المسترجعات</h2>
            <p>نضمن حق الاسترجاع خلال 14 يوم من تاريخ الاستلام...</p>

            <h2>التغييرات</h2>
            <p>نحتفظ بالحق في تعديل هذه السياسة في أي وقت...</p>
        </div>
    </div>
</section>

@endsection

<!--  -->

// PrivacyPolicyController
use App\Services\SharedDataService;

public function PrivacyPolicy()
{
    $data = SharedDataService::get();
    $data['pageTitle'] = 'سياسة الخصوصية';
    return view('PrivacyPolicy.index', $data);
}

public function TermsAndConditions()
{
    $data = SharedDataService::get();
    $data['pageTitle'] = 'الشروط والأحكام';
    return view('PrivacyPolicy.terms', $data);
}

<!--  -->

/* أضف في resources/css/home.css */

/* ── Page Header ── */
.page-header {
    background: var(--bg-secondary);
    padding-block: 1.5rem;
    border-bottom: 1px solid var(--stroke);
    margin-bottom: 0;
}

.page-header__title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: .25rem;
}

.breadcrumb-item a { color: var(--primary); }
.breadcrumb-item.active { color: var(--text); }

/* ── About Cards ── */
.about-card {
    background: var(--white);
    border: 1px solid var(--stroke);
    border-radius: var(--radius-lg);
    padding: 2rem;
    height: 100%;
}

.about-card__title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--heading);
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 2px solid var(--primary);
}

.about-card__text {
    color: var(--text);
    line-height: 1.8;
    font-size: .95rem;
}

/* ── Team Card ── */
.team-card { text-align: center; }

.team-card__img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary);
    margin-bottom: .75rem;
}

.team-card__name {
    font-weight: 600;
    color: var(--heading);
    font-size: .9rem;
}

/* ── Contact ── */
.contact-info p {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .75rem;
    color: var(--text);
}

.contact-info a { color: var(--text); }
.contact-info a:hover { color: var(--primary); }

/* ── Offers Grid ── */
.offers-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.offer-banner {
    display: block;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.offer-banner img {
    width: 100%;
    max-height: 280px;
    object-fit: cover;
    transition: transform .35s ease;
}

.offer-banner:hover img { transform: scale(1.02); }

/* ── Policy Content ── */
.policy-content h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--heading);
    margin-top: 2rem;
    margin-bottom: .75rem;
}

.policy-content p {
    color: var(--text);
    line-height: 1.8;
    margin-bottom: 1rem;
}

<!--  -->

// في SharedDataService::get()
use App\Models\MaintenanceCategory;

// ── Maintenance Subcategories للـ navbar ──
$maintenanceCategories = MaintenanceCategory::all()
    ->map(fn($mc) => [
        'name' => $mc->maintenancecategory_title,
        'slug' => $mc->maintenancecategory_id,
    ])->toArray();

// أضفها في الـ staticLinks
$staticLinks = [
    // ...
    [
        'name'   => 'الصيانة',
        'route'  => 'UserMaintenance',
        'has_db' => true,         // ← غيّرناها لـ true
        'db_key' => 'maintenance', // ← مفتاح جديد
    ],
    // ...
];

// وفي الـ navData
$navData = [
    'categories'  => $navCategories,
    'maintenance' => $maintenanceCategories,
];

return [
    // ...
    'navData' => $navData,
    // ...
];

<!--  -->

{{-- في الـ submenu بتاع الصيانة --}}
<a class="dropdown-item"
   href="{{ route('UserMaintenance') }}?category={{ $sub['slug'] }}">
    {{ $sub['name'] }}
</a>