@extends('layouts.app')

@section('title', 'عروض الصيانة - المتخصص')
@section('description', 'احصل على أفضل العروض والخصومات على خدمات الصيانة')

@section('content')
<main id="maintenance-offers-page">
    {{-- ══════════════════════ البطل ══════════════════════ --}}
    <section style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 80px 20px; text-align: center;">
        <div class="container">
            <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 20px;">عروض الصيانة المميزة</h1>
            <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                خصومات وعروض حصرية على جميع خدمات الصيانة
            </p>
        </div>
    </section>

    {{-- ══════════════════════ العروض ══════════════════════ --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- عرض 1 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div style="position: absolute; top: -30px; right: 20px; background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 1.1rem;">-30%</div>
                            <div style="font-size: 4rem;"><i class="bi bi-phone"></i></div>
                        </div>
                        <div style="padding: 30px;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة الموبايل الشاملة</h3>
                            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                فحص شامل + تنظيف داخلي + تحديث البرمجيات + استبدال أي قطعة معيبة
                            </p>
                            <div style="display: flex; gap: 10px; align-items: baseline; margin-bottom: 20px;">
                                <span style="font-size: 0.9rem; color: #999; text-decoration: line-through;">500 ج.م</span>
                                <span style="font-size: 1.8rem; font-weight: 700; color: #667eea;">350 ج.م</span>
                            </div>
                            <p style="color: #f5576c; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="bi bi-clock"></i> صالح لمدة شهر واحد
                            </p>
                            <button style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                                اطلب الآن
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 2 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s; position: relative; transform: scale(1.05);">
                        <div style="position: absolute; top: 0; left: 0; right: 0; background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%); color: white; text-align: center; padding: 8px; font-weight: 700; font-size: 0.85rem;">
                            ⭐ الأكثر طلباً
                        </div>
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: white; margin-top: 20px;">
                            <div style="position: absolute; top: 10px; right: 20px; background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 1.1rem;">-40%</div>
                            <div style="font-size: 4rem;"><i class="bi bi-laptop"></i></div>
                        </div>
                        <div style="padding: 30px;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة اللاب توب الكاملة</h3>
                            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                تنظيف الأوساخ + استبدال معجون حراري + فحص البطارية + تحسين الأداء
                            </p>
                            <div style="display: flex; gap: 10px; align-items: baseline; margin-bottom: 20px;">
                                <span style="font-size: 0.9rem; color: #999; text-decoration: line-through;">700 ج.م</span>
                                <span style="font-size: 1.8rem; font-weight: 700; color: #f5576c;">420 ج.م</span>
                            </div>
                            <p style="color: #f5576c; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="bi bi-clock"></i> صالح لمدة شهرين
                            </p>
                            <button style="width: 100%; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                                اطلب الآن
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 3 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div style="position: absolute; top: -30px; right: 20px; background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 1.1rem;">-25%</div>
                            <div style="font-size: 4rem;"><i class="bi bi-tablet"></i></div>
                        </div>
                        <div style="padding: 30px;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">صيانة الأجهزة اللوحية</h3>
                            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                فحص شامل + استبدال البطارية + تنظيف الشاشة + معايرة اللمس
                            </p>
                            <div style="display: flex; gap: 10px; align-items: baseline; margin-bottom: 20px;">
                                <span style="font-size: 0.9rem; color: #999; text-decoration: line-through;">400 ج.م</span>
                                <span style="font-size: 1.8rem; font-weight: 700; color: #00f2fe;">300 ج.م</span>
                            </div>
                            <p style="color: #f5576c; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="bi bi-clock"></i> صالح لمدة شهر واحد
                            </p>
                            <button style="width: 100%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                                اطلب الآن
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 4 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div style="position: absolute; top: -30px; right: 20px; background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 1.1rem;">-35%</div>
                            <div style="font-size: 4rem;"><i class="bi bi-lightning-charge"></i></div>
                        </div>
                        <div style="padding: 30px;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">استبدال الشواحن والكابلات</h3>
                            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                فحص وإصلاح الشواحن والكابلات التالفة + استبدال آمن وسريع
                            </p>
                            <div style="display: flex; gap: 10px; align-items: baseline; margin-bottom: 20px;">
                                <span style="font-size: 0.9rem; color: #999; text-decoration: line-through;">200 ج.م</span>
                                <span style="font-size: 1.8rem; font-weight: 700; color: #fa709a;">130 ج.م</span>
                            </div>
                            <p style="color: #f5576c; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="bi bi-clock"></i> صالح لمدة شهر واحد
                            </p>
                            <button style="width: 100%; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                                اطلب الآن
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 5 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div style="position: absolute; top: -30px; right: 20px; background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 1.1rem;">-45%</div>
                            <div style="font-size: 4rem;"><i class="bi bi-shield-lock"></i></div>
                        </div>
                        <div style="padding: 30px;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">حماية شاملة من الفيروسات</h3>
                            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                فحص شامل للفيروسات + تنظيف شامل + تثبيت برنامج حماية متقدم
                            </p>
                            <div style="display: flex; gap: 10px; align-items: baseline; margin-bottom: 20px;">
                                <span style="font-size: 0.9rem; color: #999; text-decoration: line-through;">300 ج.م</span>
                                <span style="font-size: 1.8rem; font-weight: 700; color: #a8edea;">165 ج.م</span>
                            </div>
                            <p style="color: #f5576c; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="bi bi-clock"></i> صالح لمدة 3 أشهر
                            </p>
                            <button style="width: 100%; background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                                اطلب الآن
                            </button>
                        </div>
                    </div>
                </div>

                {{-- عرض 6 --}}
                <div class="col-md-6 col-lg-4">
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: all 0.3s; position: relative;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div style="position: absolute; top: -30px; right: 20px; background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 1.1rem;">-50%</div>
                            <div style="font-size: 4rem;"><i class="bi bi-award"></i></div>
                        </div>
                        <div style="padding: 30px;">
                            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px;">حزمة الصيانة السنوية</h3>
                            <p style="color: #666; margin-bottom: 20px; line-height: 1.6;">
                                صيانة دورية شاملة + 4 خدمات مجانية + ضمان سنوي + أولوية في الخدمة
                            </p>
                            <div style="display: flex; gap: 10px; align-items: baseline; margin-bottom: 20px;">
                                <span style="font-size: 0.9rem; color: #999; text-decoration: line-through;">2000 ج.م</span>
                                <span style="font-size: 1.8rem; font-weight: 700; color: #667eea;">1000 ج.م</span>
                            </div>
                            <p style="color: #f5576c; font-size: 0.9rem; margin-bottom: 20px;">
                                <i class="bi bi-clock"></i> صالح لمدة سنة كاملة
                            </p>
                            <button style="width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif;">
                                اطلب الآن
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
            <h2 style="font-size: 2.8rem; font-weight: 700; margin-bottom: 20px;">احصل على عرضك المفضل اليوم</h2>
            <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 40px;">
                العروض محدودة ولا تفوت الفرصة
            </p>
            <a href="{{ route('contact') }}" style="display: inline-block; background: white; color: #667eea; padding: 14px 50px; border-radius: 50px; font-weight: 700; text-decoration: none; font-size: 1.1rem;">
                احجز الآن
            </a>
        </div>
    </section>
</main>
@endsection
