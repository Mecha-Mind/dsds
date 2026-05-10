@extends('layouts.app')

@section('title', 'أحدث العروض - المتخصص')
@section('description', 'احصل على أحدث العروض والخصومات الحصرية')

@section('content')
<main id="offers-page">
    {{-- ══════════════════════ البطل ══════════════════════ --}}
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 20px; text-align: center;">
        <div class="container">
            <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 20px;">أحدث العروض</h1>
            <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                اكتشف أفضل العروض والخصومات على جميع المنتجات
            </p>
        </div>
    </section>

    {{-- ══════════════════════ المحتوى ══════════════════════ --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- عرض 1 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s;">
                        <div style="position: relative; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <div style="padding: 25px;">
                            <span style="background: #ff6b6b; color: white; padding: 5px 12px; border-radius: 50px; font-size: 0.9rem; font-weight: 700;">-30%</span>
                            <h3 style="font-size: 1.3rem; font-weight: 700; margin: 15px 0 10px;">موبايل ايفون 13</h3>
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">أسعار مميزة على أحدث الطرازات</p>
                            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
                                <span style="text-decoration: line-through; color: #999;">45000 ج.م</span>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #667eea;">31500 ج.م</span>
                            </div>
                            <button style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                                عرض التفاصيل
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 2 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s;">
                        <div style="position: relative; height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></div>
                        <div style="padding: 25px;">
                            <span style="background: #ff6b6b; color: white; padding: 5px 12px; border-radius: 50px; font-size: 0.9rem; font-weight: 700;">-25%</span>
                            <h3 style="font-size: 1.3rem; font-weight: 700; margin: 15px 0 10px;">سماعات بلوتوث</h3>
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">جودة صوت عالية وتصميم عصري</p>
                            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
                                <span style="text-decoration: line-through; color: #999;">2000 ج.م</span>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #f5576c;">1500 ج.م</span>
                            </div>
                            <button style="width: 100%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                                عرض التفاصيل
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 3 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s;">
                        <div style="position: relative; height: 200px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);"></div>
                        <div style="padding: 25px;">
                            <span style="background: #ff6b6b; color: white; padding: 5px 12px; border-radius: 50px; font-size: 0.9rem; font-weight: 700;">-40%</span>
                            <h3 style="font-size: 1.3rem; font-weight: 700; margin: 15px 0 10px;">أكسسوارات موبايل</h3>
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">شواحن وأغطية أصلية</p>
                            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
                                <span style="text-decoration: line-through; color: #999;">500 ج.م</span>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #00f2fe;">300 ج.م</span>
                            </div>
                            <button style="width: 100%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                                عرض التفاصيل
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ CTA ══════════════════════ --}}
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 20px; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px;">لا تفوت العروض المحدودة</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 40px;">كل يوم عروض جديدة</p>
            <a href="{{ route('products') }}" style="display: inline-block; background: white; color: #667eea; padding: 14px 50px; border-radius: 50px; font-weight: 700; text-decoration: none; font-size: 1.1rem;">
                تسوق الآن
            </a>
        </div>
    </section>
</main>
@endsection
