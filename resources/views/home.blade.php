<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', '')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- SEO --}}
    <title>@yield('title', 'Plus Digital')</title>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/cairo/Cairo-Regular.woff2') }}" as="font"
        type="font/woff2" crossorigin>
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/css/products.css', 'resources/css/page-details.css', 'resources/js/app.js'])
</head>

<body>
    @include('components.navbar', [
        'staticLinks' => $ecommerceSharedData['staticLinks'] ?? [],
        'navData' => $ecommerceSharedData['navData'] ?? [],
        'branchName' => $ecommerceSharedData['branchName'] ?? '',
        'branchImage' => $ecommerceSharedData['branchImage'] ?? '',
        'phone' => $ecommerceSharedData['phone'] ?? '',
        'logo' => $ecommerceSharedData['logo'] ?? '',
    ])

    <main id="main-content" role="main">
        @yield('content')
    </main>

    @include('components.footer', [
        'footerCategories' => $footerCategories ?? [],
        'branch' => $Branch ?? null,
        'SocialMediaContact' => $SocialMediaContact ?? null,
        'mapUrl' => $mapUrl ?? '#',
        'phone2' => $Branch?->branch_phone2 ?? '',
    ])


    @stack('scripts')

    {{-- ══════ Search Modal ══════ --}}
    <div id="searchModal" class="search-modal" role="dialog" aria-modal="true" aria-label="البحث عن منتج" hidden>

        {{-- Backdrop --}}
        <div class="search-modal__backdrop" id="searchBackdrop"></div>

        {{-- Panel --}}
        <div class="search-modal__panel">

            {{-- Input Row --}}
            <div class="search-modal__input-wrap">
                <button type="button" class="search-modal__close" id="searchModalClose" aria-label="إغلاق البحث">
                    <i class="bi bi-x-lg"></i>
                </button>

                <input type="search" id="searchModalInput" class="search-modal__input" placeholder="ابحث عن منتج..."
                    autocomplete="off" spellcheck="false" maxlength="100" dir="rtl">

                <i class="bi bi-search search-modal__icon" aria-hidden="true"></i>
            </div>

            {{-- Hint --}}
            <div class="search-modal__hint" id="searchHint">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span>اكتب للبحث في المنتجات</span>
            </div>

            {{-- Loading --}}
            <div class="search-modal__loading d-none" id="searchLoading">
                <span class="search-spinner"></span>
                <span>جاري البحث...</span>
            </div>

            {{-- Quick Categories --}}
            @if (!empty($ecommerceSharedData['navData']['categories']))
                <div class="search-modal__quick">
                    <span class="search-modal__quick-label">تصفح بالتصنيف:</span>
                    <div class="search-modal__quick-tags">
                        @foreach (array_slice($ecommerceSharedData['navData']['categories'], 0, 6) as $cat)
                            <a href="{{ route('CategoryProduct', $cat['slug']) }}" class="search-modal__quick-tag">
                                {{ $cat['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
    <script>
        (function () {
            'use strict';

            // ── Helpers ──────────────────────────────────────
            function csrf() {
                return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            }

            function updateBadges(selector, count) {
                document.querySelectorAll(selector).forEach(el => {
                    // textContent آمن من XSS
                    el.textContent = count;
                    el.classList.toggle('d-none', Number(count) === 0);
                });
            }

            // ── Add to Cart ───────────────────────────────────
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-add-to-cart');
                if (!btn) return;
                e.preventDefault();

                const id = btn.dataset.id;
                if (!id) return;

                // قراءة الكمية من صفحة تفاصيل المنتج لو موجودة
                const qtyEl = document.getElementById('qtyInput');
                const qty   = qtyEl ? (parseInt(qtyEl.textContent, 10) || 1) : 1;

                const icon         = btn.querySelector('i');
                const text         = btn.querySelector('.btn-text');
                const originalIcon = icon?.className ?? 'bi bi-bag';
                const originalText = text?.textContent ?? 'أضف إلى السلة';

                btn.disabled = true;

                fetch(`/cart/add/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':  csrf(),
                        'Accept':        'application/json',
                        'Content-Type':  'application/json',
                    },
                    // نبعت الكمية مع الـ request
                    body: JSON.stringify({ quantity: qty }),
                })
                .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(data => {
                    if (!data.success) { btn.disabled = false; return; }

                    if (icon) icon.className        = 'bi bi-check';
                    if (text) text.textContent      = 'تمت الإضافة';
                    updateBadges('[data-cart-count]', data.cart_count);

                    setTimeout(() => {
                        if (icon) icon.className   = originalIcon;
                        if (text) text.textContent = originalText;
                        btn.disabled = false;
                    }, 1500);
                })
                .catch(() => { btn.disabled = false; });
            });

            // ── Wishlist Toggle ───────────────────────────────
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-wishlist-toggle');
                if (!btn) return;
                e.preventDefault();

                const id = btn.dataset.id;
                if (!id) return;

                const icon = btn.querySelector('i');

                fetch(`/wishlist/toggle/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    if (data.in_wishlist) {
                        if (icon) icon.className = 'bi bi-heart-fill';
                        btn.classList.add('is-wishlisted');
                        btn.setAttribute('aria-pressed', 'true');
                        btn.setAttribute('aria-label', 'إزالة من المفضلة');
                    } else {
                        if (icon) icon.className = 'bi bi-heart';
                        btn.classList.remove('is-wishlisted');
                        btn.setAttribute('aria-pressed', 'false');
                        btn.setAttribute('aria-label', 'أضف للمفضلة');
                    }
                    updateBadges('[data-wishlist-count]', data.wishlist_count);
                })
                .catch(console.error);
            });

            // ── Save for Later ────────────────────────────────
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-save-later');
                if (!btn) return;
                e.preventDefault();

                const id   = btn.dataset.id;
                const icon = btn.querySelector('i');
                const text = btn.querySelector('.btn-text');

                fetch(`/wishlist/toggle/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    if (data.in_wishlist) {
                        if (icon) icon.className   = 'bi bi-heart-fill';
                        if (text) text.textContent = 'في المفضلة';
                        btn.classList.add('is-wishlisted');
                    } else {
                        if (icon) icon.className   = 'bi bi-heart';
                        if (text) text.textContent = 'حفظ لاحقاً';
                        btn.classList.remove('is-wishlisted');
                    }
                    updateBadges('[data-wishlist-count]', data.wishlist_count);
                })
                .catch(console.error);
            });

            // ── Cart Qty AJAX (زيادة/تقليل بدون refresh) ─────
            /*
            | الـ buttons بيحملوا:
            |   class="js-qty-btn"
            |   data-action="increase" أو "decrease"
            |   data-key="{{ '$key' }}"  ← مفتاح المنتج في الـ session
            */
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-qty-btn');
                if (!btn) return;
                e.preventDefault();

                const action  = btn.dataset.action;   // 'increase' | 'decrease'
                const itemKey = btn.dataset.key;
                if (!action || itemKey === undefined) return;

                // منع الضغط المتكرر
                if (btn.disabled) return;
                btn.disabled = true;

                const cartItem = btn.closest('.cart-item');
                const qtyEl    = cartItem?.querySelector('.cart-qty__val');
                const totalEl  = cartItem?.querySelector('.cart-item__total');
                const currentQty = parseInt(qtyEl?.textContent ?? '1', 10);

                fetch(`/cart/update/${itemKey}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN':  csrf(),
                        'Accept':        'application/json',
                        'Content-Type':  'application/json',
                    },
                    body: JSON.stringify({ action }),
                })
                .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(data => {
                    if (!data.success) { btn.disabled = false; return; }

                    if (data.removed) {
                        // المنتج اتحذف (الكمية وصلت لصفر)
                        cartItem?.remove();
                    } else {
                        // تحديث الكمية والإجمالي بدون Refresh
                        if (qtyEl)   qtyEl.textContent  = data.new_quantity;
                        if (totalEl) totalEl.textContent = data.item_total + ' جنية';
                    }

                    // تحديث الإجمالي الكلي
                    const summaryTotalEl = document.querySelector('[data-cart-total]');
                    if (summaryTotalEl) summaryTotalEl.textContent = data.cart_total + ' جنية';

                    updateBadges('[data-cart-count]', data.cart_count);

                    btn.disabled = false;
                })
                .catch(() => { btn.disabled = false; });
            });

            // ── Delete Confirm Modal ──────────────────────────
            /*
            | المشكلة كانت:
            | btn.closest('form') مش بيشتغل لأن الـ button
            | مش جوه الـ form — كل منهم في div منفصل
            |
            | الحل:
            | الـ form بياخد id="delete-form-{key}"
            | الـ button بياخد data-form-id="delete-form-{key}"
            | الـ JS بيجيب الـ form بـ getElementById
            */
            // ── Delete Confirm Modal ──────────────────────────────────
            let _pendingFormId = null;

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-delete-confirm');
                if (!btn) return;
                e.preventDefault();

                _pendingFormId = btn.dataset.formId;

                // textContent — آمن من XSS
                const nameEl = document.getElementById('deleteProductName');
                if (nameEl) nameEl.textContent = btn.dataset.name ?? 'هذا المنتج';

                const modalEl = document.getElementById('deleteConfirmModal');
                if (!modalEl) return;

                // ← الحل: نشيل aria-hidden قبل ما Bootstrap يضيفه غلط
                modalEl.removeAttribute('aria-hidden');

                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });

            // تأكيد الحذف
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#confirmDeleteBtn')) return;

                // ← هنا كانت المشكلة: modalEl معرفش هنا
                // الحل: نعرفه من جديد في نفس الـ scope
                const modalEl = document.getElementById('deleteConfirmModal');

                if (_pendingFormId) {
                    const form = document.getElementById(_pendingFormId);
                    if (form) {
                        // نخفي الـ modal الأول عشان ما يبقاش في conflict
                        if (modalEl && window.bootstrap) {
                            const bsModal = bootstrap.Modal.getInstance(modalEl);
                            if (bsModal) {
                                bsModal.hide();
                            }
                        }
                        // بعد ما الـ modal يتخفى نعمل submit
                        setTimeout(() => {
                            form.submit();
                        }, 200);
                    }
                    _pendingFormId = null;
                }
            });
            // ── Search Modal ──────────────────────────────────
            const PRODUCTS_URL = '{{ route("EcommerceAllProducts") }}';
            const searchModal  = document.getElementById('searchModal');
            const backdrop     = document.getElementById('searchBackdrop');
            const searchInput  = document.getElementById('searchModalInput');
            const closeBtn     = document.getElementById('searchModalClose');
            const trigger      = document.getElementById('navSearchBtn');
            const triggerDesk  = document.getElementById('navSearchBtnDesk');
            const hint         = document.getElementById('searchHint');
            const loading      = document.getElementById('searchLoading');

            if (searchModal && trigger && searchInput) {
                let isOpen   = false;
                let debounce = null;

                function openModal() {
                    searchModal.removeAttribute('hidden');
                    requestAnimationFrame(() => searchModal.classList.add('search-modal--open'));
                    isOpen = true;
                    trigger.setAttribute('aria-expanded', 'true');
                    if (triggerDesk) triggerDesk.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => searchInput.focus(), 50);
                }

                function closeModal() {
                    searchModal.classList.remove('search-modal--open');
                    isOpen = false;
                    trigger.setAttribute('aria-expanded', 'false');
                    if (triggerDesk) triggerDesk.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                    setTimeout(() => {
                        searchModal.setAttribute('hidden', '');
                        searchInput.value = '';
                        if (hint)    hint.classList.remove('d-none');
                        if (loading) loading.classList.add('d-none');
                    }, 260);
                }

                function goToResults(query) {
                    if (!query || query.trim().length < 2) return;
                    if (hint)    hint.classList.add('d-none');
                    if (loading) loading.classList.remove('d-none');
                    const url = new URL(PRODUCTS_URL);
                    url.searchParams.set('search', query.trim());
                    window.location.href = url.toString();
                }

                trigger.addEventListener('click', openModal);
                if (triggerDesk) triggerDesk.addEventListener('click', openModal);
                if (backdrop)    backdrop.addEventListener('click', closeModal);
                if (closeBtn)    closeBtn.addEventListener('click', closeModal);

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && isOpen) closeModal();
                });

                searchInput.addEventListener('input', function () {
                    const val = this.value.trim();
                    clearTimeout(debounce);
                    if (val.length < 2) {
                        if (hint)    hint.classList.remove('d-none');
                        if (loading) loading.classList.add('d-none');
                        return;
                    }
                    debounce = setTimeout(() => goToResults(val), 3000);
                });

                searchInput.addEventListener('keydown', e => {
                    if (e.key === 'Enter') { clearTimeout(debounce); goToResults(searchInput.value); }
                });
            }

        })();
    </script>
    {{-- ══════ End Search Modal ══════ --}}
</body>

</html>
