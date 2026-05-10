@extends('layouts.app')

@section('title', 'جميع المنتجات - المتخصص')
@section('description', 'تصفح جميع المنتجات الموجودة')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                المنتجات
            </li>
        </ol>
    </nav>

    <h1 style="margin-bottom: 30px;">جميع المنتجات</h1>

    <div class="row" style="min-height: 400px;">
        <div class="col-12">
            <div style="background: #f8f9fa; padding: 40px; border-radius: 10px; text-align: center;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666;">لا توجد منتجات متاحة حالياً</p>
                <p style="color: #999; margin-bottom: 20px;">سيتم إضافة المنتجات قريباً</p>
                <a href="{{ route('home') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    العودة للرئيسية
                </a>
            </div>
        </div>
    </div>

</div>
@endsection