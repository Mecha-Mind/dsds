{{-- resources/views/ecommerce/Customer/CustomerLogin/CustomerLogin.blade.php --}}
@extends('layouts.app')
@section('title', 'تسجيل الدخول — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-card__logo">
            <img src="{{ asset('images/brancheslogo/' . ($ecommerceSharedData['branchImage'] ?? '')) }}"
                 alt="{{ $ecommerceSharedData['branchName'] ?? '' }}"
                 height="48" loading="eager">
        </div>

        <h1 class="auth-card__title">أهلا بك!</h1>
        <p class="auth-card__desc">أستخدم رقم موبايلك لتسجيل الدخول أو إنشاء حساب.</p>

        {{-- Errors --}}
        @if($errors->any())
        <div class="auth-card__errors" role="alert">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- Form --}}
        <form method="POST"
              action="{{ route('CustomerLoginPost') }}"
              id="loginForm"
              novalidate>
            @csrf
            @method('post')
            <input type="hidden" name="fingerprint" id="fingerprint">
            <input type="hidden" name="client_token" id="client_token">

            <div class="auth-card__field">
                <label for="customer_phone" class="auth-card__label">رقم الموبايل</label>
                <input type="tel"
                       id="customer_phone"
                       name="customer_phone"
                       class="auth-card__input @error('customer_phone') is-invalid @enderror"
                       placeholder="{{ $ecommerceSharedData['phone'] ?? '01xxxxxxxxx' }}"
                       pattern="01[0-9]{9}"
                       maxlength="11"
                       required
                       autocomplete="tel"
                       inputmode="numeric"
                       aria-describedby="phoneHelp">
                @error('customer_phone')
                <span class="auth-card__error-msg" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn hero__btn auth-card__submit">
                الدخول بكود رسالة قصيرة
            </button>
        </form>

        <p class="auth-card__footer">
            مستخدم جديد؟
            <a href="{{ route('CustomerLogin') }}" class="auth-card__link">إنشاء حساب</a>
        </p>

        <p class="auth-card__terms">
            بالمتابعة، فأنت موافق على
            <a href="{{ route('TermsAndConditions') }}" class="auth-card__link">الشروط والأحكام</a>
        </p>

    </div>
</div>
@endsection