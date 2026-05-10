@extends('layouts.app')

@section('title', 'حسابي - المتخصص')
@section('description', 'إدارة حسابك الشخصي')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                حسابي
            </li>
        </ol>
    </nav>

    <h1 style="margin-bottom: 30px;">حسابي</h1>

    <div class="row">
        <div class="col-md-3">
            {{-- القائمة الجانبية --}}
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="{{ route('account') }}" style="color: #667eea; text-decoration: none; font-weight: 600;">
                            <i class="bi bi-person"></i> بياناتي
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="{{ route('account.orders') }}" style="color: #666; text-decoration: none;">
                            <i class="bi bi-box"></i> طلباتي
                        </a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#" style="color: #666; text-decoration: none;">
                            <i class="bi bi-heart"></i> المفضلة
                        </a>
                    </li>
                    <li>
                        <a href="#" style="color: #666; text-decoration: none;">
                            <i class="bi bi-box-arrow-left"></i> تسجيل الخروج
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-9">
            {{-- المحتوى الرئيسي --}}
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h2 style="margin-bottom: 25px;">بيانات الحساب</h2>

                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">الاسم الكامل</label>
                            <input type="text" placeholder="أدخل اسمك الكامل" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">البريد الإلكتروني</label>
                            <input type="email" placeholder="بريدك الإلكتروني" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">رقم الهاتف</label>
                            <input type="tel" placeholder="رقم الهاتف" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">العنوان</label>
                            <input type="text" placeholder="عنوانك" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>

                    <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 40px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                        حفظ التغييرات
                    </button>
                </form>

                <hr style="margin: 40px 0;">

                <h3 style="margin-bottom: 20px;">تغيير كلمة المرور</h3>
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">كلمة المرور الحالية</label>
                            <input type="password" placeholder="كلمة المرور الحالية" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">كلمة المرور الجديدة</label>
                            <input type="password" placeholder="كلمة المرور الجديدة" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">تأكيد كلمة المرور</label>
                            <input type="password" placeholder="تأكيد كلمة المرور" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>

                    <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 40px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                        تحديث كلمة المرور
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
