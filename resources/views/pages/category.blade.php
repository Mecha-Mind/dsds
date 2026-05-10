@extends('layouts.app')

@section('title', 'التصنيفات - المتخصص')
@section('description', 'تصفح جميع التصنيفات')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                التصنيفات
            </li>
        </ol>
    </nav>

    <h1 style="margin-bottom: 30px; text-align: center;">جميع التصنيفات</h1>

    {{-- شبكة التصنيفات --}}
    <div class="row" style="min-height: 400px;">
        <div class="col-12">
            <div style="background: #f8f9fa; padding: 60px 20px; border-radius: 10px; text-align: center;">
                <i class="bi bi-grid-3x3" style="font-size: 4rem; color: #ccc; display: block; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem; color: #666; margin-bottom: 10px;">لا توجد تصنيفات متاحة حالياً</p>
                <p style="color: #999; margin-bottom: 20px;">سيتم إضافة التصنيفات قريباً</p>
                <a href="{{ route('home') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    العودة للرئيسية
                </a>
            </div>
        </div>
    </div>

</div>
@endsection