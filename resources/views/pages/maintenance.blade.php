@extends('layouts.app')

@section('title', 'صيانة - المتخصص')
@section('description', 'خدمات صيانة متميزة للأجهزة الإلكترونية')

@section('content')
<main id="maintenance-page">
    {{-- ══════════════════════ البطل ══════════════════════ --}}
    <section class="maintenance-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 20px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -30%; left: -5%; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        <div class="container text-center" style="position: relative; z-index: 1;">
            <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">خدمات الصيانة</h1>
            <p style="font-size: 1.3rem; opacity: 0.95; max-width: 700px; margin: 0 auto; line-height: 1.8;">
                صيانة احترافية لجميع أجهزتك الإلكترونية بأيدي خبراء متخصصين
            </p>
        </div>
    </section>

    {{-- ══════════════════════ الخدمات ══════════════════════ --}}
    <section class="maintenance-services py-5">
        <div class="container">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.8rem; font-weight: 700; margin-bottom: 15px;">خدماتنا المتنوعة</h2>
                <p style="font-size: 1.1rem; color: #666; max-width: 600px; margin: 0 auto;">
                    نوفر حلاً شاملاً لجميع احتياجات صيانة الأجهزة الإلكترونية
                </p>
            </div>

            <div class="row g-4">
                {{-- الموبايل --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 40px 25px; text-align: center; color: white; height: 100%; transition: all 0.3s; cursor: pointer;" class="service-card">
                        <div style="font-size: 3.5rem; margin-bottom: 20px;">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة الموبايل</h3>
                        <p style="opacity: 0.95; line-height: 1.6; margin-bottom: 20px;">
                            إصلاح شاشات التشقق، بطاريات ضعيفة، مشاكل البرمجيات
                        </p>
                        <ul style="text-align: right; list-style: none; padding: 0; font-size: 0.95rem;">
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> إصلاح الشاشات</li>
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> استبدال البطارية</li>
                            <li><i class="bi bi-check-circle-fill"></i> تنظيف وصيانة</li>
                        </ul>
                    </div>
                </div>

                {{-- اللاب توب --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 15px; padding: 40px 25px; text-align: center; color: white; height: 100%; transition: all 0.3s; cursor: pointer;" class="service-card">
                        <div style="font-size: 3.5rem; margin-bottom: 20px;">
                            <i class="bi bi-laptop"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة اللاب توب</h3>
                        <p style="opacity: 0.95; line-height: 1.6; margin-bottom: 20px;">
                            إصلاح المفاتيح، المروحة، مشاكل الأداء البطيء
                        </p>
                        <ul style="text-align: right; list-style: none; padding: 0; font-size: 0.95rem;">
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> إصلاح لوحة المفاتيح</li>
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> تنظيف الأوساخ</li>
                            <li><i class="bi bi-check-circle-fill"></i> ترقية الذاكرة</li>
                        </ul>
                    </div>
                </div>

                {{-- الأجهزة اللوحية --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 15px; padding: 40px 25px; text-align: center; color: white; height: 100%; transition: all 0.3s; cursor: pointer;" class="service-card">
                        <div style="font-size: 3.5rem; margin-bottom: 20px;">
                            <i class="bi bi-tablet"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة الأجهزة اللوحية</h3>
                        <p style="opacity: 0.95; line-height: 1.6; margin-bottom: 20px;">
                            إصلاح الشاشات والأزرار ومشاكل الاتصال
                        </p>
                        <ul style="text-align: right; list-style: none; padding: 0; font-size: 0.95rem;">
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> إصلاح الشاشات</li>
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> استبدال الزر الرئيسي</li>
                            <li><i class="bi bi-check-circle-fill"></i> مشاكل الشحن</li>
                        </ul>
                    </div>
                </div>

                {{-- الأكسسوارات --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 15px; padding: 40px 25px; text-align: center; color: white; height: 100%; transition: all 0.3s; cursor: pointer;" class="service-card">
                        <div style="font-size: 3.5rem; margin-bottom: 20px;">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة الأكسسوارات</h3>
                        <p style="opacity: 0.95; line-height: 1.6; margin-bottom: 20px;">
                            إصلاح الشواحن، السماعات، وأجهزة التهوية
                        </p>
                        <ul style="text-align: right; list-style: none; padding: 0; font-size: 0.95rem;">
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> إصلاح الشواحن</li>
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> إصلاح السماعات</li>
                            <li><i class="bi bi-check-circle-fill"></i> إصلاح الكابلات</li>
                        </ul>
                    </div>
                </div>

                {{-- خدمات إضافية --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); border-radius: 15px; padding: 40px 25px; text-align: center; color: white; height: 100%; transition: all 0.3s; cursor: pointer;" class="service-card">
                        <div style="font-size: 3.5rem; margin-bottom: 20px;">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">الخدمات الإضافية</h3>
                        <p style="opacity: 0.95; line-height: 1.6; margin-bottom: 20px;">
                            تنظيف شامل وتحسين الأداء وحماية من الفيروسات
                        </p>
                        <ul style="text-align: right; list-style: none; padding: 0; font-size: 0.95rem;">
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> تنظيف شامل</li>
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> استرجاع البيانات</li>
                            <li><i class="bi bi-check-circle-fill"></i> حماية من الفيروسات</li>
                        </ul>
                    </div>
                </div>

                {{-- الضمان --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 40px 25px; text-align: center; color: white; height: 100%; transition: all 0.3s; cursor: pointer;" class="service-card">
                        <div style="font-size: 3.5rem; margin-bottom: 20px;">
                            <i class="bi bi-award"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">الضمان</h3>
                        <p style="opacity: 0.95; line-height: 1.6; margin-bottom: 20px;">
                            ضمان شامل على جميع الصيانات والإصلاحات
                        </p>
                        <ul style="text-align: right; list-style: none; padding: 0; font-size: 0.95rem;">
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> ضمان 6 أشهر</li>
                            <li style="margin-bottom: 8px;"><i class="bi bi-check-circle-fill"></i> ضمان على قطع الغيار</li>
                            <li><i class="bi bi-check-circle-fill"></i> استرجاع مجاني</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ عملية الصيانة ══════════════════════ --}}
    <section style="background: #f8f9fa; padding: 80px 20px;">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.8rem; font-weight: 700; margin-bottom: 60px;">خطوات الصيانة لدينا</h2>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem; font-weight: 700;">
                            1
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 10px;">التشخيص</h3>
                        <p style="color: #666; font-size: 0.95rem;">
                            فحص شامل للجهاز وتحديد المشكلة بدقة
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem; font-weight: 700;">
                            2
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 10px;">العرض</h3>
                        <p style="color: #666; font-size: 0.95rem;">
                            تقديم عرض أسعار واضح ومفصل
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem; font-weight: 700;">
                            3
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 10px;">الإصلاح</h3>
                        <p style="color: #666; font-size: 0.95rem;">
                            تنفيذ الإصلاح بأيدي خبراء محترفين
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem; font-weight: 700;">
                            4
                        </div>
                        <h3 style="font-weight: 600; margin-bottom: 10px;">الاختبار</h3>
                        <p style="color: #666; font-size: 0.95rem;">
                            اختبار شامل قبل التسليم
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ CTA ══════════════════════ --}}
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 20px; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.8rem; font-weight: 700; margin-bottom: 20px;">هل تحتاج لصيانة الآن؟</h2>
            <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto;">
                اتصل بنا اليوم واحصل على تشخيص مجاني لجهازك
            </p>
            <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <a href="tel:01212345678" style="display: inline-block; background: white; color: #667eea; padding: 14px 40px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s; font-size: 1.1rem;">
                    <i class="bi bi-telephone"></i> اتصل بنا
                </a>
                <a href="{{ route('contact') }}" style="display: inline-block; background: rgba(255,255,255,0.25); color: white; padding: 14px 40px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s; font-size: 1.1rem;">
                    راسلنا رسالة
                </a>
            </div>
        </div>
    </section>
</main>

<style>
    .service-card {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
</style>
@endsection
