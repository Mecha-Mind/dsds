@extends('layouts.app')

@section('title', 'الأسئلة الشائعة - المتخصص')
@section('description', 'الإجابة على الأسئلة الشائعة حول خدماتنا')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">الرئيسية</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                الأسئلة الشائعة
            </li>
        </ol>
    </nav>

    <h1 style="margin-bottom: 30px; text-align: center;">الأسئلة الشائعة</h1>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="accordion" id="faqAccordion">

                {{-- سؤال 1 --}}
                <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: #f8f9fa; font-weight: 600; color: #333; padding: 15px; text-align: right;">
                            ما هي أوقات الدعم الفني؟
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body" style="padding: 20px; background: white; text-align: right;">
                            <p style="margin: 0; line-height: 1.8;">نحن متوفرون على مدار الأسبوع من الساعة 8 صباحاً إلى الساعة 10 مساءً. يمكنك التواصل معنا عبر الهاتف أو البريد الإلكتروني أو الدردشة المباشرة.</p>
                        </div>
                    </div>
                </div>

                {{-- سؤال 2 --}}
                <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: #f8f9fa; font-weight: 600; color: #333; padding: 15px; text-align: right;">
                            كم تستغرق عملية الشحن؟
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body" style="padding: 20px; background: white; text-align: right;">
                            <p style="margin: 0; line-height: 1.8;">تتم معالجة الطلبات في مدة 24 ساعة، والشحن يستغرق من 2 إلى 5 أيام عمل حسب المنطقة الجغرافية.</p>
                        </div>
                    </div>
                </div>

                {{-- سؤال 3 --}}
                <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: #f8f9fa; font-weight: 600; color: #333; padding: 15px; text-align: right;">
                            هل يمكنني إرجاع المنتج؟
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body" style="padding: 20px; background: white; text-align: right;">
                            <p style="margin: 0; line-height: 1.8;">نعم، لديك حق الإرجاع خلال 14 يوماً من تاريخ الاستقبال بشرط أن يكون المنتج في حالة جيدة وفي علبته الأصلية.</p>
                        </div>
                    </div>
                </div>

                {{-- سؤال 4 --}}
                <div class="accordion-item" style="margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: #f8f9fa; font-weight: 600; color: #333; padding: 15px; text-align: right;">
                            ما هي طرق الدفع المتاحة؟
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body" style="padding: 20px; background: white; text-align: right;">
                            <p style="margin: 0; line-height: 1.8;">نقبل جميع طرق الدفع الرئيسية بما في ذلك: بطاقات الائتمان، التحويل البنكي، المحفظ الرقمية، والدفع عند الاستلام.</p>
                        </div>
                    </div>
                </div>

                {{-- سؤال 5 --}}
                <div class="accordion-item" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="background: #f8f9fa; font-weight: 600; color: #333; padding: 15px; text-align: right;">
                            هل توجد ضمانة على المنتجات؟
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body" style="padding: 20px; background: white; text-align: right;">
                            <p style="margin: 0; line-height: 1.8;">جميع المنتجات لديها ضمانة من المصنع. تختلف مدة الضمانة حسب نوع المنتج، لكن عادة ما تكون سنة واحدة.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('content')

<h1>FAQ</h1>

<h3>Are the games free?</h3>
<p>Yes, all games are free.</p>

<h3>Do I need an account?</h3>
<p>No account needed.</p>

<h3>Is it mobile friendly?</h3>
<p>Yes, fully responsive.</p>

@endsection