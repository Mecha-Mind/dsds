{{-- resources/views/ecommerce/Customer/NewCustomer/NewCustomer.blade.php --}}
@extends('layouts.app')
@section('title', 'إنشاء حساب — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <div class="auth-card__logo">
            <img src="{{ asset('images/brancheslogo/' . ($ecommerceSharedData['branchImage'] ?? '')) }}"
                 alt="{{ $ecommerceSharedData['branchName'] ?? '' }}"
                 height="48" loading="eager">
        </div>

        <h1 class="auth-card__title">إنشاء حساب</h1>
        <p class="auth-card__desc">أستخدم رقم موبايلك لتسجيل الدخول أو إنشاء حساب.</p>

        @if($errors->any())
        <div class="auth-card__errors" role="alert">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST"
              action="{{ route('NewCustomerPost') }}"
              novalidate>
            @csrf
            @method('post')
            <input type="hidden" name="fingerprint" id="fingerprint">
            <input type="hidden" name="client_token" id="client_token">
            {{-- رقم التليفون مشفر من الخطوة السابقة --}}
            <input type="hidden"
                   name="customer_phone"
                   value="{{ $encryptedcustomer_phone ?? '' }}">

            <div class="auth-card__field">
                <label for="customer_name" class="auth-card__label">الاسم ثلاثي</label>
                <input type="text"
                       id="customer_name"
                       name="customer_name"
                       class="auth-card__input @error('customer_name') is-invalid @enderror"
                       required
                       autocomplete="name"
                       aria-label="الاسم الكامل">
                @error('customer_name')
                <span class="auth-card__error-msg" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="auth-card__field">
                <label for="customer_telegramchatid" class="auth-card__label">رقم الموبايل</label>
                <input type="tel"
                       id="customer_telegramchatid"
                       name="customer_telegramchatid"
                       class="auth-card__input"
                       placeholder="اختياري — رقم شات التليجرام"
                       inputmode="numeric">
            </div>

            <button type="submit" class="btn hero__btn auth-card__submit">
                إنشاء حساب
            </button>
        </form>

        <p class="auth-card__footer">
            عندك حساب؟
            <a href="{{ route('CustomerLogin') }}" class="auth-card__link">تسجيل الدخول</a>
        </p>

        <p class="auth-card__terms">
            بالمتابعة، فأنت موافق على
            <a href="{{ route('TermsAndConditions') }}" class="auth-card__link">الشروط والأحكام</a>
        </p>

    </div>
</div>
@endsection