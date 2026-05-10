<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run()
    {
        // ════════════════════════ صفحة من نحن ════════════════════════
        $aboutPage = Page::create([
            'title' => 'من نحن',
            'slug' => 'about',
            'description' => 'تعرف على قصة متجر المتخصص وقيمنا',
            'content' => '<p>متجر متخصص في توفير أحدث التكنولوجيا والإلكترونيات بأفضل الأسعار والجودة العالية.</p>',
            'meta_title' => 'من نحن - المتخصص',
            'meta_description' => 'تعرف على قصة متجر المتخصص وقيمنا',
            'is_active' => true,
        ]);

        // أقسام صفحة من نحن
        PageSection::create([
            'page_id' => $aboutPage->id,
            'title' => 'قصتنا',
            'description' => 'أسسنا متجرنا برؤية واضحة: توفير تجربة تسوق استثنائية للعملاء.',
            'image' => 'images/hero.png',
            'type' => 'content',
            'order' => 1,
        ]);

        PageSection::create([
            'page_id' => $aboutPage->id,
            'title' => 'قيمنا الأساسية',
            'type' => 'features',
            'order' => 2,
            'data' => [
                'items' => [
                    [
                        'icon' => 'bi-check-circle-fill',
                        'title' => 'الجودة',
                        'description' => 'نضمن أعلى معايير الجودة لكل منتج',
                    ],
                    [
                        'icon' => 'bi-heart-fill',
                        'title' => 'رضا العميل',
                        'description' => 'رضاكم هو أولويتنا الأولى',
                    ],
                    [
                        'icon' => 'bi-lightning-fill',
                        'title' => 'السرعة',
                        'description' => 'توصيل سريع وآمن',
                    ],
                    [
                        'icon' => 'bi-shield-check',
                        'title' => 'الأمان',
                        'description' => 'عمليات دفع آمنة',
                    ],
                ],
            ],
        ]);

        PageSection::create([
            'page_id' => $aboutPage->id,
            'title' => 'الإحصائيات',
            'type' => 'stats',
            'order' => 3,
            'data' => [
                'stats' => [
                    ['value' => '+50K', 'label' => 'عميل سعيد'],
                    ['value' => '+1000', 'label' => 'منتج متنوع'],
                    ['value' => '24/7', 'label' => 'دعم فني'],
                    ['value' => '100%', 'label' => 'رضا العملاء'],
                ],
            ],
        ]);

        // ════════════════════════ صفحة التواصل ════════════════════════
        $contactPage = Page::create([
            'title' => 'تواصل معنا',
            'slug' => 'contact',
            'description' => 'نحن هنا للإجابة على كل استفساراتك',
            'meta_title' => 'تواصل معنا - المتخصص',
            'meta_description' => 'للتواصل معنا عبر النموذج أو المعلومات المباشرة',
            'is_active' => true,
        ]);

        // أقسام صفحة التواصل
        PageSection::create([
            'page_id' => $contactPage->id,
            'title' => 'معلومات الاتصال',
            'type' => 'features',
            'order' => 1,
            'data' => [
                'items' => [
                    [
                        'icon' => 'bi-geo-alt-fill',
                        'title' => 'العنوان',
                        'description' => 'الإسماعيلية - الشيخ زايد - الشارع التجاري',
                    ],
                    [
                        'icon' => 'bi-telephone-fill',
                        'title' => 'الهاتف',
                        'description' => '01212345678',
                    ],
                    [
                        'icon' => 'bi-envelope-fill',
                        'title' => 'البريد الإلكتروني',
                        'description' => 'info@store.com',
                    ],
                    [
                        'icon' => 'bi-clock-fill',
                        'title' => 'ساعات العمل',
                        'description' => 'السبت - الخميس: 9am - 10pm',
                    ],
                ],
            ],
        ]);

        // ════════════════════════ صفحة الأسئلة الشائعة ════════════════════════
        $faqPage = Page::create([
            'title' => 'الأسئلة الشائعة',
            'slug' => 'faq',
            'description' => 'إجابات على الأسئلة الشائعة من عملائنا',
            'meta_title' => 'الأسئلة الشائعة - المتخصص',
            'meta_description' => 'إجابات على الأسئلة الشائعة والمتكررة',
            'is_active' => true,
        ]);

        PageSection::create([
            'page_id' => $faqPage->id,
            'type' => 'features',
            'order' => 1,
            'data' => [
                'items' => [
                    [
                        'icon' => 'bi-question-circle',
                        'title' => 'طرق الدفع',
                        'description' => 'نقدم فيزا، ماستركارد، والدفع عند الاستلام',
                    ],
                    [
                        'icon' => 'bi-question-circle',
                        'title' => 'التوصيل',
                        'description' => 'التوصيل يستغرق 1-3 أيام عمل',
                    ],
                    [
                        'icon' => 'bi-question-circle',
                        'title' => 'الاسترجاع',
                        'description' => 'يمكن استرجاع المنتج خلال 7 أيام',
                    ],
                    [
                        'icon' => 'bi-question-circle',
                        'title' => 'الضمان',
                        'description' => 'جميع المنتجات مغطاة بضمان سنة',
                    ],
                ],
            ],
        ]);

        // ════════════════════════ سياسة الخصوصية ════════════════════════
        Page::create([
            'title' => 'سياسة الخصوصية',
            'slug' => 'privacy',
            'description' => 'سياسة الخصوصية والبيانات الشخصية',
            'content' => '<h3>حماية خصوصيتك</h3><p>نحن ملتزمون بحماية خصوصية بيانات عملائنا. تُستخدم جميع المعلومات الشخصية فقط لأغراض تحسين الخدمة.</p>',
            'meta_title' => 'سياسة الخصوصية - المتخصص',
            'meta_description' => 'سياسة الخصوصية والبيانات الشخصية',
            'is_active' => true,
        ]);

        // ════════════════════════ الشروط والأحكام ════════════════════════
        Page::create([
            'title' => 'الشروط والأحكام',
            'slug' => 'terms',
            'description' => 'شروط وأحكام استخدام الموقع',
            'content' => '<h3>شروط الاستخدام</h3><p>بزيارتك للموقع، فأنت تقبل جميع الشروط والأحكام التالية.</p>',
            'meta_title' => 'الشروط والأحكام - المتخصص',
            'meta_description' => 'شروط وأحكام استخدام الموقع',
            'is_active' => true,
        ]);
    }
}
