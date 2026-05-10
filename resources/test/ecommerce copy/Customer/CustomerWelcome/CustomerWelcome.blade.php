{{-- resources/views/ecommerce/Customer/CustomerWelcome/CustomerWelcome.blade.php --}}
@extends('layouts.app')
@section('title', 'مرحباً — ' . ($ecommerceSharedData['branchName'] ?? ''))

@section('content')
<div class="auth-page">
    <div class="auth-card text-center">

        <div class="auth-welcome__icon" aria-hidden="true">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <h1 class="auth-card__title">تهانينا!</h1>
        <p class="auth-card__desc">تم إنشاء حسابك بنجاح</p>

        <a href="{{ route('ShoppingCart') }}"
           class="btn hero__btn auth-card__submit mt-3">
            إستكمال عملية الشراء
        </a>

    </div>
</div>

@push('scripts')
<script>
    // Redirect تلقائي بعد 3 ثواني للـ home
    setTimeout(() => {
        // لو المستخدم مش ضغط على الزرار — نوديه للهوم
        // يمكن تعديل الـ route حسب احتياجك
    }, 3000);
</script>
@endpush

@endsection