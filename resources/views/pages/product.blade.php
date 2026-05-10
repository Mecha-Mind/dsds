@extends('layouts.app')

@section('title', 'تفاصيل المنتج - المتخصص')
@section('description', 'معلومات تفصيلية عن المنتج')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('products') }}">المنتجات</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                تفاصيل المنتج
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <div style="background: #f0f0f0; aspect-ratio: 1; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <p class="text-muted">صورة المنتج</p>
            </div>
        </div>
        <div class="col-md-6">
            <h1>اسم المنتج</h1>
            <p class="text-muted">SKU: {{ $slug }}</p>
            <div style="font-size: 1.5rem; margin: 20px 0;">
                <span style="font-weight: 700; color: #667eea;">0 ج.م</span>
            </div>
            <button style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif; width: 100%;">
                أضف إلى السلة
            </button>
        </div>
    </div>
</div>
@endsection
