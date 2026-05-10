</div>
</main>
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="{{ url('js/bootstrap.bundle.js') }}"></script>
<script src="{{ url('js/bootstrap.bundle.js.map') }}"></script>


<script>
    function initScroll(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return; // لو العنصر مش موجود (مثلاً الصفحة الموبايل مش ظاهرة)

        function scrollImages() {
            const first = track.firstElementChild;
            if (!first) return; // أمان في حالة عدم وجود عناصر
            const width = first.offsetWidth;
            track.style.transition = 'transform 0.5s ease';
            track.style.transform = `translateX(-${width}px)`;
            setTimeout(() => {
                track.style.transition = 'none';
                track.appendChild(first);
                track.style.transform = 'translateX(0)';
            }, 500);
        }

        setInterval(scrollImages, 3000);
    }

    // استدعاء الدالتين
    initScroll('scrollTrackDesktop');
    initScroll('scrollTrackMobile');
</script>


<script src="{{ url('js/fp.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', async function() {
        // 1️⃣ fingerprint
        const fp = await FingerprintJS.load();
        const result = await fp.get();
        document.getElementById('fingerprint').value = result.visitorId;

        // 2️⃣ client_token (يتخزن في localStorage عشان يفضل ثابت)
        const TOKEN_KEY = 'client_token';
        let token = localStorage.getItem(TOKEN_KEY);
        if (!token) {
            token = crypto.randomUUID(); // متوفر في معظم المتصفحات الحديثة
            localStorage.setItem(TOKEN_KEY, token);
        }
        document.getElementById('client_token').value = token;
    });
</script>



{{-- Google Adsense --}}

<meta name="google-adsense-account" content="ca-pub-3774967562670078">

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3774967562670078"
    crossorigin="anonymous"></script>

<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXX" data-ad-slot="1234567890"
    data-ad-format="auto" data-full-width-responsive="true"></ins>
<script>
    (adsbygoogle = window.adsbygoogle || []).push({});
</script>


{{-- meta Adsense --}}
<script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '707039765033390');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=707039765033390&ev=PageView&noscript=1" /></noscript>
<!-- End Meta Pixel Code -->


@yield('js')

</body>

</html>
