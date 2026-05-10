README.md
markdown# Plus Digital — Ecommerce Frontend

مشروع Laravel 12 لإعادة تصميم واجهة المتجر الإلكتروني لـ Plus Digital.

## Stack
- PHP 8.2 / Laravel 12
- Bootstrap 5.3 RTL
- Bootstrap Icons + Font Awesome 6
- Cairo Font (Google Fonts)

## Structure
app/
Http/Controllers/
Ecommerce/              ← controllers الـ ecommerce الأصلية
Services/
SharedDataService.php   ← بيانات الـ navbar والـ footer لكل الصفحات
Models/
ApplicationColor.php    ← ألوان الموقع من الـ DB
resources/views/
layouts/
app.blade.php           ← الـ layout الرئيسي
components/
navbar.blade.php        ← الـ navbar (static links + DB categories)
footer.blade.php        ← الـ footer (branch + social + categories)
pages/                   ← صفحات الـ ecommerce
welcome.blade.php         ← الهوم بيدج

## الـ SharedDataService

أي controller جديد أو معدّل لازم يبدأ بـ:

```php
use App\Services\SharedDataService;

$data = SharedDataService::get();
// بعدين بيانات الصفحة الخاصة
return view('pages.xxx', $data);
```

ده بيضمن إن الـ navbar والـ footer عندهم البيانات في كل صفحة.

## الألوان

الألوان جاية من جدول `applicationcolors` في الـ DB.
في الـ layout بيتحط في CSS variables:

```css
:root {
  --primary:   /* من DB */;
  --secondary: /* من DB */;
}
```

يعني لو صاحب الشغل غيّر اللون من الـ admin، الموقع بيتغير أوتوماتيك.

## Routes المهمة

| الاسم | الـ URL | الوصف |
|-------|---------|-------|
| `home` | `/` | الهوم بيدج |
| `EcommerceAllProducts` | `/Products/All` | كل المنتجات |
| `EcommerceAllCategories` | `/Categories` | كل التصنيفات |
| `CategoryProduct` | `/CategoryProduct/{id}` | منتجات category معينة |
| `SubcategoryProduct` | `/SubcategoryProduct/{id}` | منتجات subcategory |
| `ProductDetails` | `/ProductDetails/{id}` | تفاصيل منتج |
| `EcommerceKnowAboutUs` | `/KnowAboutUs` | من نحن |
| `EcommerceContactUs` | `/ContactUs` | تواصل معنا |
| `EcommerceOffers` | `/Offers` | العروض |
| `UserMaintenance` | `/Maintenance` | الصيانة |
| `ShoppingCart` | `/ShoppingCart` | السلة |
| `CustomerLogin` | `/Customer/Login` | تسجيل الدخول |

## ملاحظات

- الـ URL الخاص بالـ categories بيستخدم الـ ID مش الاسم العربي.
- الاسم العربي بيظهر في الـ breadcrumb وعنوان الصفحة بس.
- لا تعدّل في ملف `routes/web.php` الأصلي عشان متكسرش الـ admin panel.
