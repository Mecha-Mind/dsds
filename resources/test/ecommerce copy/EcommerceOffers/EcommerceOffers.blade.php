@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - العروض ';
    $description =
        'اكتشف أحدث العروض والخصومات في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        '، عروض حصرية على المنتجات والخدمات لفترة محدودة.';
    $keywords = 'عروض, خصومات, ' . $ecommerceSharedData['branch']->branch_name . ', منتجات, خدمات, توفير, أسعار خاصة';
    $og_title = 'عروض فرع ' . $ecommerceSharedData['branch']->branch_name;
    $og_description =
        'استفيد من أفضل العروض والخصومات المتوفرة الآن في فرع ' .
        $ecommerceSharedData['branch']->branch_name .
        ' – لا تفوت الفرصة!';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';

@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)
@section('content')

    <x-page-header title="عروض الصيانة" :breadcrumbs="[
        ['name' => 'الرئيسية', 'url' => route('home')],
        ['name' => 'عروض الصيانة', 'url' => route('EcommerceOffers')],
    ]" />



    @if (!$Offersfromtheowners->isEmpty())
        <div class="container">
            <div class="row">
                @foreach ($Offersfromtheowners as $offer)
                    <a href="{{ $offer->offerfromtheowner_url ?? '#' }}" class="maintenance-card mt-3"
                        target="{{ $offer->offerfromtheowner_url ? '_blank' : '_self' }}" rel="noopener">
                        <img src="{{ asset('images/Offersfromtheowners/' . $offer->offerfromtheowner_image) }}"
                            alt="{{ $offer->offerfromtheowner_headline }}" loading="lazy">
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
