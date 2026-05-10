@extends('layouts.app')

@section('title', 'من نحن - المتخصص')
@section('description', 'تعرف على قصة متجر المتخصص وقيمنا')

@section('content')
<main id="about-page">
    {{-- ══════════════════════ البطل ══════════════════════ --}}
    <section class="about-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 20px;">
        <div class="container text-center">
            <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 20px;">من نحن</h1>
            <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                متجر متخصص في توفير أحدث التكنولوجيا والإلكترونيات بأفضل الأسعار والجودة العالية
            </p>
        </div>
    </section>

    {{-- ══════════════════════ قصتنا ══════════════════════ --}}
    <section class="about-story py-5">
        <div class="container">
            <div class="row align-items-center gap-5">
                <div class="col-lg-6">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 3px; border-radius: 15px;">
                        <img src="{{ asset('images/hero.png') }}" alt="قصتنا" style="width: 100%; border-radius: 12px; display: block;">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px;">قصتنا</h2>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #666; margin-bottom: 20px;">
                        أسسنا متجرنا برؤية واضحة: توفير تجربة تسوق استثنائية للعملاء في مصر والشرق الأوسط.
                        مع سنوات من الخبرة في مجال التكنولوجيا، نسعى جاهدين لتقديم أفضل المنتجات
                        والخدمات بأسعار تنافسية.
                    </p>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #666; margin-bottom: 30px;">
                        كل منتج في متجرنا تم اختياره بعناية فائقة لضمان جودته والتزامنا برضا عملائنا.
                        نحن لا نبيع فقط منتجات، بل نقدم حلاً شاملاً لكل احتياجاتك التكنولوجية.
                    </p>
                    <div style="display: flex; gap: 30px;">
                        <div>
                            <h3 style="font-size: 2rem; font-weight: 700; color: #667eea;">+50K</h3>
                            <p style="color: #666; margin: 5px 0 0 0;">عميل سعيد</p>
                        </div>
                        <div>
                            <h3 style="font-size: 2rem; font-weight: 700; color: #667eea;">+1000</h3>
                            <p style="color: #666; margin: 5px 0 0 0;">منتج متنوع</p>
                        </div>
                        <div>
                            <h3 style="font-size: 2rem; font-weight: 700; color: #667eea;">24/7</h3>
                            <p style="color: #666; margin: 5px 0 0 0;">دعم فني</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ القيم ══════════════════════ --}}
    <section class="about-values py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 700; margin-bottom: 50px;">قيمنا الأساسية</h2>
            <div class="row gap-4">
                <div class="col-md-6 col-lg-3">
                    <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <div style="font-size: 3rem; margin-bottom: 15px; color: #667eea;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">الجودة</h3>
                        <p style="color: #666; line-height: 1.6;">
                            نضمن أعلى معايير الجودة لكل منتج يتم بيعه
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <div style="font-size: 3rem; margin-bottom: 15px; color: #667eea;">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">رضا العميل</h3>
                        <p style="color: #666; line-height: 1.6;">
                            رضاكم هو أولويتنا الأولى والأخيرة
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <div style="font-size: 3rem; margin-bottom: 15px; color: #667eea;">
                            <i class="bi bi-lightning-fill"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">السرعة</h3>
                        <p style="color: #666; line-height: 1.6;">
                            توصيل سريع وآمن إلى كل مكان
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <div style="font-size: 3rem; margin-bottom: 15px; color: #667eea;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 10px;">الأمان</h3>
                        <p style="color: #666; line-height: 1.6;">
                            عمليات دفع آمنة وحماية بيانات قصوى
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ الفريق ══════════════════════ --}}
    <section class="about-team py-5">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 700; margin-bottom: 50px;">فريقنا المتميز</h2>
            <div class="row gap-4">
                <div class="col-md-6 col-lg-4" style="text-align: center;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 style="font-weight: 600; margin-bottom: 5px;">أحمد محمد</h3>
                    <p style="color: #667eea; font-weight: 600; margin-bottom: 10px;">المدير التنفيذي</p>
                    <p style="color: #666; font-size: 0.95rem;">مؤسس المتجر مع خبرة تزيد عن 10 سنوات في مجال التكنولوجيا</p>
                </div>
                <div class="col-md-6 col-lg-4" style="text-align: center;">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 style="font-weight: 600; margin-bottom: 5px;">فاطمة علي</h3>
                    <p style="color: #f5576c; font-weight: 600; margin-bottom: 10px;">مسؤولة العمليات</p>
                    <p style="color: #666; font-size: 0.95rem;">متخصصة في إدارة المخزون والعمليات اللوجستية</p>
                </div>
                <div class="col-md-6 col-lg-4" style="text-align: center;">
                    <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 style="font-weight: 600; margin-bottom: 5px;">محمود حسن</h3>
                    <p style="color: #00f2fe; font-weight: 600; margin-bottom: 10px;">قائد فريق الدعم</p>
                    <p style="color: #666; font-size: 0.95rem;">مسؤول عن خدمة العملاء والدعم الفني</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ CTA ══════════════════════ --}}
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 20px; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.2rem; font-weight: 700; margin-bottom: 20px;">استعد للتسوق الذكي</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                انضم إلى آلاف العملاء الراضين واستمتع بتجربة تسوق فريدة
            </p>
            <a href="{{ route('products') }}" style="display: inline-block; background: white; color: #667eea; padding: 12px 35px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                تسوق الآن
            </a>
        </div>
    </section>
</main>
@endsection