{{-- resources/views/ecommerce/Customer/CustomerCode/CustomerCode.blade.php --}}
@extends('layouts.app')
@section('title', 'تأكيد رقم الموبايل — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-card__title">
            {{-- لو CustomerCode → "تأكيد رقم الموبايل" --}}
            {{-- لو CustomerLogin → "الدخول بكود رسالة قصيرة" --}}
            تأكيد رقم الموبايل
        </h1>
        <p class="auth-card__desc">أدخل الكود من الرسالة المرسلة على رقم</p>
        <p class="auth-card__phone">{{ session('_pending_phone') ?? '' }}</p>

        @if($errors->any())
        <div class="auth-card__errors" role="alert">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST"
              action="{{ route('CustomerCodePost') }}"
              id="codeForm"
              novalidate>
            @csrf
            @method('post')

            {{-- البيانات المشفرة --}}
            <input type="hidden" name="customer_name"           value="{{ $encryptedcustomer_name ?? '' }}">
            <input type="hidden" name="customer_telegramchatid" value="{{ $encryptedcustomer_telegramchatid ?? '' }}">
            <input type="hidden" name="customer_email"          value="{{ $encryptedcustomer_email ?? '' }}">
            <input type="hidden" name="customer_phone"          value="{{ $encryptedcustomer_phone ?? '' }}">
            <input type="hidden" name="customer_systemcode"     value="{{ $encryptednewCustomerCode ?? '' }}">

            {{-- OTP Inputs — 6 خانات --}}
            <div class="otp-inputs" role="group" aria-label="كود التحقق">
                @for($i = 0; $i < 6; $i++)
                <input type="text"
                       class="otp-input"
                       maxlength="1"
                       inputmode="numeric"
                       pattern="[0-9]"
                       aria-label="الرقم {{ $i + 1 }}"
                       autocomplete="one-time-code">
                @endfor
                {{-- input مخفي بيجمع الكود الكامل --}}
                <input type="hidden" name="customer_code" id="otpFinal">
            </div>

            <p class="auth-card__resend">
                <span id="resendTimer">إعادة إرسال الكود خلال <strong id="countdown">30</strong> ثانية</span>
                <button type="button"
                        id="resendBtn"
                        class="auth-card__link d-none"
                        aria-label="إعادة إرسال الكود">
                    إعادة الإرسال
                </button>
            </p>

            <button type="submit" class="btn hero__btn auth-card__submit" id="continueBtn" disabled>
                متابعة
            </button>
        </form>

        <p class="auth-card__terms">
            بالمتابعة، فأنت موافق على
            <a href="{{ route('TermsAndConditions') }}" class="auth-card__link">الشروط والأحكام</a>
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /*
     | OTP Logic:
     | 1. 6 inputs — كل input يستقبل رقم واحد
     | 2. لما يكتب رقم بينتقل للـ input الجاي تلقائياً
     | 3. لما يحذف بيرجع للـ input السابق
     | 4. لما يكمل الـ 6 أرقام بيفعّل زرار المتابعة
    */

    const inputs      = document.querySelectorAll('.otp-input');
    const finalInput  = document.getElementById('otpFinal');
    const continueBtn = document.getElementById('continueBtn');
    const form        = document.getElementById('codeForm');

    inputs.forEach((input, idx) => {
        input.addEventListener('input', function () {
            // السماح بالأرقام فقط
            this.value = this.value.replace(/\D/g, '').slice(-1);

            if (this.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }

            _syncOTP();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                inputs[idx - 1].focus();
                inputs[idx - 1].value = '';
                _syncOTP();
            }
        });

        // Paste support — لو نسخ الكود كامل
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text')
                .replace(/\D/g, '')
                .slice(0, 6);

            pasted.split('').forEach((char, i) => {
                if (inputs[i]) inputs[i].value = char;
            });

            if (inputs[pasted.length - 1]) {
                inputs[pasted.length - 1].focus();
            }

            _syncOTP();
        });
    });

    function _syncOTP() {
        const code = Array.from(inputs).map(i => i.value).join('');
        finalInput.value = code;

        // تفعيل زرار المتابعة لما يكتمل الكود
        if (code.length === 6) {
            continueBtn.disabled = false;
            continueBtn.classList.add('is-ready');
        } else {
            continueBtn.disabled = true;
            continueBtn.classList.remove('is-ready');
        }
    }

    // Focus على أول input
    if (inputs[0]) inputs[0].focus();

    /*
     | Countdown Timer — إعادة الإرسال
    */
    let timeLeft = 30;
    const countdownEl = document.getElementById('countdown');
    const resendTimer = document.getElementById('resendTimer');
    const resendBtn   = document.getElementById('resendBtn');

    const timer = setInterval(() => {
        timeLeft--;
        if (countdownEl) countdownEl.textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(timer);
            if (resendTimer) resendTimer.classList.add('d-none');
            if (resendBtn)   resendBtn.classList.remove('d-none');
        }
    }, 1000);

    // إعادة الإرسال
    if (resendBtn) {
        resendBtn.addEventListener('click', function () {
            // ← هنا ممكن تضيف AJAX لإعادة إرسال الكود
            // دلوقتي بنعمل reload عادي
            window.location.reload();
        });
    }

})();
</script>
@endpush