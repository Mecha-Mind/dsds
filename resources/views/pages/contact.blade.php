@extends('layouts.app')

@section('title', 'تواصل معنا - المتخصص')
@section('description', 'نحن هنا للإجابة على كل أسئلتك واستفساراتك')

@section('content')
<main id="contact-page">
    {{-- ══════════════════════ البطل ══════════════════════ --}}
    <section class="contact-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 20px;">
        <div class="container text-center">
            <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 20px;">تواصل معنا</h1>
            <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
                نحن هنا للإجابة على كل استفساراتك والمساعدة في حل أي مشكلة
            </p>
        </div>
    </section>

    {{-- ══════════════════════ المحتوى ══════════════════════ --}}
    <section class="contact-content py-5">
        <div class="container">
            <div class="row gap-5">
                {{-- الفورم --}}
                <div class="col-lg-7">
                    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">أرسل لنا رسالة</h2>
                    
                    @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}" style="background: #f8f9fa; padding: 30px; border-radius: 15px;">
                        @csrf

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">اسمك الكامل</label>
                            <input 
                                name="name" 
                                type="text"
                                required
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;"
                                placeholder="أدخل اسمك الكامل"
                            >
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">بريدك الإلكتروني</label>
                            <input 
                                name="email" 
                                type="email"
                                required
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;"
                                placeholder="example@email.com"
                            >
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">رقم الهاتف (اختياري)</label>
                            <input 
                                name="phone" 
                                type="tel"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;"
                                placeholder="01XXXXXXXXX"
                            >
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">الموضوع</label>
                            <select 
                                name="subject"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;"
                            >
                                <option value="">اختر الموضوع</option>
                                <option value="complaint">شكوى</option>
                                <option value="suggestion">اقتراح</option>
                                <option value="support">طلب دعم</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">رسالتك</label>
                            <textarea 
                                name="message" 
                                required
                                rows="6"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif; resize: none;"
                                placeholder="اكتب رسالتك هنا..."
                            ></textarea>
                        </div>

                        <button 
                            type="submit"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 40px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; font-family: 'Cairo', sans-serif; transition: all 0.3s; width: 100%;"
                        >
                            <i class="bi bi-send"></i> إرسال الرسالة
                        </button>
                    </form>
                </div>

                {{-- معلومات الاتصال --}}
                <div class="col-lg-5">
                    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">معلومات الاتصال</h2>

                    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-right: 4px solid #667eea;">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="font-size: 1.8rem; color: #667eea; flex-shrink: 0;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <h3 style="font-weight: 600; margin-bottom: 5px;">العنوان</h3>
                                <p style="color: #666; line-height: 1.6;">
                                    الإسماعيلية - الشيخ زايد<br>
                                    الشارع التجاري - بجوار كافية جراند
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-right: 4px solid #667eea;">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="font-size: 1.8rem; color: #667eea; flex-shrink: 0;">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <h3 style="font-weight: 600; margin-bottom: 5px;">الهاتف</h3>
                                <p style="color: #666; line-height: 1.6;">
                                    <a href="tel:01212345678" style="color: #667eea; text-decoration: none;">01212345678</a><br>
                                    <a href="tel:01012345678" style="color: #667eea; text-decoration: none;">01012345678</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-right: 4px solid #667eea;">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="font-size: 1.8rem; color: #667eea; flex-shrink: 0;">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div>
                                <h3 style="font-weight: 600; margin-bottom: 5px;">البريد الإلكتروني</h3>
                                <p style="color: #666; line-height: 1.6;">
                                    <a href="mailto:info@store.com" style="color: #667eea; text-decoration: none;">info@store.com</a><br>
                                    <a href="mailto:support@store.com" style="color: #667eea; text-decoration: none;">support@store.com</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); border-right: 4px solid #667eea;">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="font-size: 1.8rem; color: #667eea; flex-shrink: 0;">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div>
                                <h3 style="font-weight: 600; margin-bottom: 5px;">ساعات العمل</h3>
                                <p style="color: #666; line-height: 1.6;">
                                    السبت - الخميس: 9:00 ص - 10:00 م<br>
                                    الجمعة: 2:00 م - 10:00 م
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ الأسئلة الشائعة ══════════════════════ --}}
    <section style="background-color: #f8f9fa; padding: 60px 20px;">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 700; margin-bottom: 50px;">الأسئلة الشائعة</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item" style="margin-bottom: 15px; border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: white; font-weight: 600;">
                                    <i class="bi bi-question-circle" style="margin-right: 10px;"></i> ما هي طرق الدفع المتاحة؟
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="background: #f8f9fa; border-top: 1px solid #ddd;">
                                    نقدم عدة طرق دفع آمنة: بطاقات الائتمان (فيزا، ماستركارد)، المحافظ الرقمية، والدفع عند الاستلام.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="margin-bottom: 15px; border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: white; font-weight: 600;">
                                    <i class="bi bi-question-circle" style="margin-right: 10px;"></i> كم تستغرق عملية التوصيل؟
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="background: #f8f9fa; border-top: 1px solid #ddd;">
                                    التوصيل يستغرق من 1-3 أيام عمل حسب منطقتك الجغرافية.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="margin-bottom: 15px; border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: white; font-weight: 600;">
                                    <i class="bi bi-question-circle" style="margin-right: 10px;"></i> هل يمكنني استرجاع المنتج؟
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="background: #f8f9fa; border-top: 1px solid #ddd;">
                                    نعم، يمكنك استرجاع المنتج خلال 7 أيام من الشراء وفقاً لشروطنا.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: white; font-weight: 600;">
                                    <i class="bi bi-question-circle" style="margin-right: 10px;"></i> هل لديكم ضمان على المنتجات؟
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="background: #f8f9fa; border-top: 1px solid #ddd;">
                                    جميع منتجاتنا مغطاة بضمان صانع المنتج + ضمان متجرنا لمدة سنة.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════ CTA ══════════════════════ --}}
    <section style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 60px 20px; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.2rem; font-weight: 700; margin-bottom: 20px;">هل تواجه مشكلة؟</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                فريقنا مستعد للمساعدة على مدار الساعة
            </p>
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="tel:01212345678" style="display: inline-block; background: white; color: #f5576c; padding: 12px 35px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                    <i class="bi bi-telephone"></i> اتصل بنا
                </a>
                <a href="#contact-form" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 12px 35px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                    أرسل لنا رسالة
                </a>
            </div>
        </div>
    </section>
</main>
@endsection