@extends('layouts.app')

@section('title', 'تفاصيل الطلب - المتخصص')
@section('description', 'معلومات تفصيلية عن طلبك')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('account.orders') }}">طلباتي</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                تفاصيل الطلب
            </li>
        </ol>
    </nav>

    <h1>تفاصيل الطلب #{{ $orderId }}</h1>

    <div class="row mt-4">
        <div class="col-md-8">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3>معلومات الطلب</h3>
                <p><strong>رقم الطلب:</strong> #{{ $orderId }}</p>
                <p><strong>التاريخ:</strong> <span>{{ date('d/m/Y') }}</span></p>
                <p><strong>الحالة:</strong> <span style="background: #ffc107; color: white; padding: 5px 10px; border-radius: 5px;">قيد المعالجة</span></p>
            </div>

            <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd;">
                    <h3>المنتجات</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding: 40px 0;">
                                لا توجد منتجات
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h3>عنوان الشحن</h3>
                <p>لم يتم تحديد عنوان بعد</p>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <h3>ملخص الطلب</h3>
                <div style="border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>الإجمالي:</span>
                        <span>0 ج.م</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
