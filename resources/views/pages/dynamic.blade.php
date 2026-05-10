@extends('layouts.app')

@section('title', $page['meta_title'] ?? $page['title'])
@section('description', $page['meta_description'] ?? $page['description'] ?? '')

@section('content')
<main id="dynamic-page">
    {{-- البطل (Hero Section) --}}
    <section class="page-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 20px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 20px;">{{ $page['title'] }}</h1>
                    @if($page['description'])
                    <p style="font-size: 1.2rem; opacity: 0.95; line-height: 1.8;">{{ $page['description'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- المحتوى الرئيسي --}}
    @if($page['content'])
    <section class="page-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div style="line-height: 1.8; font-size: 1.1rem; color: #666;">
                        {!! $page['content'] !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- الأقسام الديناميكية --}}
    @forelse($page['sections'] ?? [] as $section)
    
    @if($section['type'] === 'hero')
    <section class="section-hero" style="background: linear-gradient(135deg, {{ $section['data']['color_1'] ?? '#667eea' }} 0%, {{ $section['data']['color_2'] ?? '#764ba2' }} 100%); color: white; padding: 60px 20px;">
        <div class="container text-center">
            <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px;">{{ $section['title'] }}</h2>
            @if($section['description'])
            <p style="font-size: 1.1rem; opacity: 0.9; max-width: 700px; margin: 0 auto;">{{ $section['description'] }}</p>
            @endif
        </div>
    </section>

    @elseif($section['type'] === 'content')
    <section class="section-content py-5 {{ $loop->even ? 'bg-light' : '' }}">
        <div class="container">
            <div class="row gap-5 align-items-center">
                @if($section['image'])
                <div class="col-lg-6">
                    <img src="{{ asset($section['image']) }}" alt="{{ $section['title'] }}" style="width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                </div>
                @endif
                <div class="col-lg-{{ $section['image'] ? '6' : '12' }}">
                    @if($section['title'])
                    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 20px;">{{ $section['title'] }}</h2>
                    @endif
                    @if($section['description'])
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #666;">{{ $section['description'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @elseif($section['type'] === 'features')
    <section class="section-features py-5">
        <div class="container">
            @if($section['title'])
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 700; margin-bottom: 50px;">{{ $section['title'] }}</h2>
            @endif
            <div class="row g-4">
                @foreach($section['data']['items'] ?? [] as $item)
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s;">
                        @if($item['icon'] ?? null)
                        <div style="font-size: 3rem; margin-bottom: 15px; color: #667eea;">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </div>
                        @endif
                        <h3 style="font-weight: 600; margin-bottom: 10px;">{{ $item['title'] ?? '' }}</h3>
                        <p style="color: #666; font-size: 0.95rem; line-height: 1.6;">{{ $item['description'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @elseif($section['type'] === 'stats')
    <section class="section-stats py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row text-center">
                @foreach($section['data']['stats'] ?? [] as $stat)
                <div class="col-md-6 col-lg-3">
                    <div style="padding: 30px;">
                        <h3 style="font-size: 2.5rem; font-weight: 700; color: #667eea; margin-bottom: 10px;">{{ $stat['value'] ?? '' }}</h3>
                        <p style="color: #666; font-weight: 600;">{{ $stat['label'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @elseif($section['type'] === 'gallery')
    <section class="section-gallery py-5">
        <div class="container">
            @if($section['title'])
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 700; margin-bottom: 50px;">{{ $section['title'] }}</h2>
            @endif
            <div class="row g-4">
                @foreach($section['data']['images'] ?? [] as $image)
                <div class="col-md-6 col-lg-4">
                    <img src="{{ asset($image) }}" alt="Gallery Image" style="width: 100%; height: 300px; object-fit: cover; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @elseif($section['type'] === 'cta')
    <section class="section-cta py-5" style="background: linear-gradient(135deg, {{ $section['data']['color_1'] ?? '#667eea' }} 0%, {{ $section['data']['color_2'] ?? '#764ba2' }} 100%); color: white; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.2rem; font-weight: 700; margin-bottom: 20px;">{{ $section['title'] }}</h2>
            @if($section['description'])
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px;">{{ $section['description'] }}</p>
            @endif
            @if($section['data']['button_text'] ?? null)
            <a href="{{ $section['data']['button_link'] ?? '#' }}" style="display: inline-block; background: white; color: {{ $section['data']['color_1'] ?? '#667eea' }}; padding: 14px 40px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s;">
                {{ $section['data']['button_text'] }}
            </a>
            @endif
        </div>
    </section>
    @endif

    @empty
    {{-- لا توجد أقسام إضافية --}}
    @endforelse
</main>

<style>
    .section-content {
        transition: all 0.3s ease;
    }

    .section-features [class*="col"] > div {
        transition: all 0.3s;
    }

    .section-features [class*="col"] > div:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection
