@php
    $Page_title = $ecommerceSharedData['branch']->branch_name . ' - سياسة الخصوصية';
    $description =
        'تعرف على سياسة الخصوصية الخاصة بـ ' .
        $ecommerceSharedData['branch']->branch_name .
        '، وكيفية جمع واستخدام وحماية بيانات المستخدمين.';
    $keywords = 'سياسة الخصوصية, خصوصية, بيانات المستخدم, ' . $ecommerceSharedData['branch']->branch_name;
    $og_title = $ecommerceSharedData['branch']->branch_name . ' - سياسة الخصوصية';
    $og_description =
        'تعرف على سياسة الخصوصية الخاصة بـ ' .
        $ecommerceSharedData['branch']->branch_name .
        ' وكيف نحمي معلوماتك الشخصية.';
    $og_image = url('/images/brancheslogo/' . $ecommerceSharedData['branch']->branch_image);
    $og_type = 'website';

@endphp

@extends('layouts.app')


@section('title', $Page_title)
@section('description', $description)

@section('content')

    <x-page-header title="سياسة الخصوصية" :breadcrumbs="[
        
    ]" />

    <div class="container py-5">
        <div class="policy-content">

            <div class="policy-section">
                <h2>جمع المعلومات</h2>
                <ol>
                    <li>نقوم بجمع بيانات شخصية مثل رقم الهاتف والاسم وعنوان البريد الإلكتروني أثناء تسجيل الحساب أو الشراء.
                    </li>
                    <li>يتم استخدام ملفات تعريف الارتباط لتحسين تجربة المستخدم.</li>
                </ol>
            </div>

            <div class="policy-section">
                <h2>استخدام المعلومات</h2>
                <ol>
                    <li>نستخدم البيانات الشخصية لمعالجة الطلبات وتقديم خدمة العملاء.</li>
                    <li>قد نستخدم بيانات الاتصال لإرسال إشعارات الطلبات والتحديثات ومكن للمستخدم إلغاء الاشتراك في أي وقت.
                    </li>
                </ol>
            </div>

            <div class="policy-section">
                <h2>حماية المعلومات</h2>
                <p>تلتزم حماية بياناتك من الوصول غير المصرح به باستخدام تقنيات حديثة لضمان الخصوصية.</p>
            </div>

            <div class="policy-section">
                <h2>مشاركة المعلومات</h2>
                <ol>
                    <li>لا نقوم ببيع أو تأجير بيانات المستخدمين لطرف ثالث.</li>
                    <li>قد نشارك البيانات مع مزودي الخدمات المرتبطين لتلبية الطلبات فقط.</li>
                </ol>
            </div>

            <div class="policy-section">
                <h2>التعديلات</h2>
                <p>نحتفظ بالحق في تعديل سياسة الخصوصية عند الضرورة وسنقوم بتحديث المستخدمين بأي تغيرات.</p>
            </div>

        </div>
    </div>
@endsection
