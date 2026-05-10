@extends('layouts.app')

@section('title', 'طلباتي - المتخصص')
@section('description', 'عرض جميع طلباتك السابقة')

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('account') }}">حسابي</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                طلباتي
            </li>
        </ol>
    </nav>

    <h1>طلباتي</h1>

    <div class="table-responsive mt-4">
        <table class="table table-hover">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th>رقم الطلب</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>الإجمالي</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 40px 0;">
                        لا توجد طلبات حالياً
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
