@extends('layouts.app')

@section('title', 'المنتجات الأكثر شهرة - المتخصص')
@section('description', 'تصفح أكثر المنتجات طلباً')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                المنتجات الشهيرة
            </li>
        </ol>
    </nav>

    <h1 style="margin-bottom: 30px;">المنتجات الأكثر شهرة</h1>

    {{-- شبكة المنتجات --}}
    <div class="row" style="min-height: 400px;">
        <div class="col-12">
            <div style="background: #f8f9fa; padding: 60px 20px; border-radius: 10px; text-align: center;">
                <i class="bi bi-star" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666; margin-bottom: 10px;">لا توجد منتجات شهيرة متاحة حالياً</p>
                <p style="color: #999; margin-bottom: 20px;">سيتم تحديث المنتجات الشهيرة قريباً</p>
                <a href="{{ route('home') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    العودة للرئيسية
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@section('content')

<h1>Most Popular Games</h1>

    @foreach($products as $index => $product)

        <section class="featured-card {{ $index % 2 ? 'reverse' : '' }}">
            <div class="featured-image">
                <img src="{{ asset('images/'.$product->image) }}" alt="">
            </div>

            <div class="featured-content">
                <h2>{{ $product->name }}</h2>
                <p>{{ $product->description }}</p>

                <a href="#" class="btn">
                    Play Now
                </a>
            </div>
        </section>

    @endforeach

@endsection