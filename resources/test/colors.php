<?php
// اختبار الألوان - ملف قديم غير مستخدم
// يمكن حذفه لاحقاً
    <style>
        :root {
            --primary:    {{ $AppColors->ecommerceprimary_color   ?? '#4066AC' }};
            --secondary:  {{ $AppColors->ecommercesecondary_color ?? '#f99e0a' }};
            --text:       {{ $AppColors->ecommercetext_color      ?? '#4B5563' }};
            --heading:    {{ $AppColors->dark_color               ?? '#0E001A' }};
            --bg-heading: {{ $AppColors->dark_color               ?? '#0E001A' }};
        }
    </style>
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

// في CategoryProductController
public function CategoryProduct($id)
{
    $data = SharedDataService::get();

    $category = Category::find($id);
    if (!$category) abort(404);

    $data['category']     = $category;
    $data['pageTitle']    = $category->category_name . ' — ' . ($data['branchName']);

    return view('pages.category-product', $data);
}

{{-- في صفحة الـ category --}}
@section('title', $pageTitle)

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
        {{-- الاسم العربي في الـ breadcrumb --}}
        <li class="breadcrumb-item active">{{ $category->category_name }}</li>
    </ol>
</nav>


// how to use
<?php
// مثال — EcommerceKnowAboutUsController

namespace App\Http\Controllers\Ecommerce\EcommerceKnowAboutUs;

use App\Http\Controllers\Controller;
use App\Services\SharedDataService;
use App\Models\AboutUs;

class EcommerceKnowAboutUsController extends Controller
{
    public function EcommerceKnowAboutUs()
    {
        // ── سطرين بس ──
        $data = SharedDataService::get();

        // ── بيانات الصفحة الخاصة ──
        $data['aboutUs'] = AboutUs::first();
        $data['pageTitle'] = 'من نحن — ' . ($data['branchName']);

        return view('pages.about', $data);
    }
}