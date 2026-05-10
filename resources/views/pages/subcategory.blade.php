@extends('layouts.app')

@section('title', $subcategoryName . ' - ' . $categoryName . ' - المتخصص')
@section('description', 'تسوق ' . $subcategoryName . ' من فئة ' . $categoryName)

@section('content')
<div class="container py-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('categories') }}">التصنيفات</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('category.show', $subcategory->category->category_slug) }}">
                    {{ $categoryName }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $subcategoryName }}
            </li>
        </ol>
    </nav>

    <h1>{{ $subcategoryName }}</h1>
    <p class="text-muted">{{ $categoryName }}</p>

    {{-- قائمة المنتجات --}}
    <div class="row mt-4">
        <div class="col-12">
            <p class="text-center text-muted">لا توجد منتجات حالياً في هذه الفئة</p>
        </div>
    </div>
</div>
@endsection
