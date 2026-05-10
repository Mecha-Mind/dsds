{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $Branch->branch_name ?? 'المتخصص')</title>
    <meta name="description" content="@yield('description', '')">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap">

    {{-- ── ألوان من الـ DB ── --}}
    @if(isset($AppColors) && $AppColors)
    <style>
        :root {
            --primary:    {{ $AppColors->ecommerceprimary_color   ?? '#017bbe' }};
            --secondary:  {{ $AppColors->ecommercesecondary_color ?? '#f99e0a' }};
            --text:       {{ $AppColors->ecommercetext_color      ?? '#4B5563' }};
            --heading:    {{ $AppColors->dark_color               ?? '#0E001A' }};
            --bg-heading: {{ $AppColors->dark_color               ?? '#0E001A' }};
        }
    </style>
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    @include('components.navbar', [
        'staticLinks' => $staticLinks   ?? [],
        'navData'     => ['categories' => $navCategories ?? []],
        'branchName'  => $branchName    ?? ($Branch->branch_name  ?? ''),
        'branchImage' => $branchImage   ?? ($Branch->branch_image ?? ''),
        'phone'       => $phone         ?? ($Branch->branch_phone ?? ''),
        'logo'        => $logo          ?? ('images/brancheslogo/' . ($Branch->branch_image ?? '')),
    ])

    <main id="main-content" role="main">
        @yield('content')
    </main>

    @include('components.footer', [
        'FooterCategories'  => $FooterCategories  ?? collect(),
        'Branch'            => $Branch            ?? null,
        'SocialMediaContact'=> $SocialMediaContact?? null,
        'mapUrl'            => $mapUrl            ?? '#',
    ])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @yield('scripts')
</body>
</html>