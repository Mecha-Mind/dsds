@extends('layouts.app')

@section('title', $pageTitle . ' — المتخصص')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb بالعربي --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('categories') }}">التصنيفات</a>
            </li>
            {{-- الاسم العربي في الـ breadcrumb، الـ URL بالـ ID --}}
            <li class="breadcrumb-item active" aria-current="page">
                {{ $categoryName }}
            </li>
        </ol>
    </nav>

    <h1>{{ $categoryName }}</h1>

</div>
@endsection