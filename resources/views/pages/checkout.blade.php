@extends('layouts.app')

@section('title', 'الدفع - المتخصص')
@section('description', 'إتمام عملية الشراء')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('cart') }}">السلة</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                الدفع
            </li>
        </ol>
    </nav>

    <h1>الدفع والشحن</h1>

    <div class="row mt-4">
        <div class="col-md-6">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h3>تفاصيل الشحن</h3>
                <form>
                    <div class="mb-3">
                        <label style="display: block; margin-bottom: 5px;">الاسم الكامل</label>
                        <input type="text" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div class="mb-3">
                        <label style="display: block; margin-bottom: 5px;">العنوان</label>
                        <input type="text" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div class="mb-3">
                        <label style="display: block; margin-bottom: 5px;">رقم الهاتف</label>
                        <input type="tel" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h3>ملخص الطلب</h3>
                <div style="border-bottom: 1px solid #ddd; padding: 10px 0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>الإجمالي:</span>
                        <span>0 ج.م</span>
                    </div>
                </div>
                <button style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif; margin-top: 20px;">
                    إكمال الشراء
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
