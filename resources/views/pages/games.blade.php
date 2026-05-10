@extends('layouts.app')

@section('title', 'الألعاب والتطبيقات - المتخصص')
@section('description', 'تصفح أحدث الألعاب والتطبيقات')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                الألعاب والتطبيقات
            </li>
        </ol>
    </nav>

    <h1 style="margin-bottom: 30px;">الألعاب والتطبيقات</h1>

    {{-- فلاتر البحث --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
        <div class="row">
            <div class="col-md-3">
                <input type="text" placeholder="ابحث عن لعبة..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            <div class="col-md-3">
                <select style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option>الفئة</option>
                    <option>ألعاب</option>
                    <option>تطبيقات</option>
                </select>
            </div>
            <div class="col-md-3">
                <select style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option>الترتيب</option>
                    <option>الأحدث</option>
                    <option>الأكثر شهرة</option>
                    <option>السعر: من الأقل للأعلى</option>
                </select>
            </div>
            <div class="col-md-3">
                <button style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                    بحث
                </button>
            </div>
        </div>
    </div>

    {{-- شبكة الألعاب --}}
    <div class="row" style="min-height: 400px;">
        <div class="col-12">
            <div style="background: #f8f9fa; padding: 60px 20px; border-radius: 10px; text-align: center;">
                <i class="bi bi-controller" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666; margin-bottom: 10px;">لا توجد ألعاب أو تطبيقات متاحة حالياً</p>
                <p style="color: #999;">سيتم إضافة محتوى جديد قريباً</p>
            </div>
        </div>
    </div>

</div>
@endsection

@section('content')

<h1>All Games 🎮</h1>

<div class="games-grid">
    @foreach($games as $game)
        <a href="{{ route('game.show', $game->slug) }}">
            <img src="/images/{{ $game->image }}">
            <h3>{{ $game->name }}</h3>
        </a>
    @endforeach
</div>

@endsection