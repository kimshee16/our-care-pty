<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Our Care')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        :root {
            --brand: #2d124b;
            --brand-dark: #1f0d35;
            --brand-soft: #fff2f6;
            --ink: #2c1746;
            --muted: #6f6278;
            --line: rgba(45, 18, 75, 0.12);
            --soft: #fff8f2;
            --mint: #7fc7b1;
            --coral: #ff7044;
            --sun: #ffd36b;
            --pad: clamp(24px, 5vw, 72px);
            --content: 1180px;
            --hero-content: 1544px;
            --shadow: 0 18px 42px rgba(45, 18, 75, 0.12);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            color: var(--ink);
            background: #fffaf7;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a { color: inherit; text-decoration: none; }
        img, svg { display: block; max-width: 100%; }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            min-height: 47px;
            padding: 8px max(18px, calc((100vw - var(--hero-content)) / 2));
            background: var(--brand);
            color: rgba(255, 255, 255, 0.86);
            font-size: 14px;
            line-height: 1.15;
        }

        .topbar__group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 26px;
        }

        .topbar__item {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            white-space: nowrap;
        }

        .topbar a { color: #ffffff; font-size: 14px; font-weight: 800; }
        .icon { width: 21px; height: 21px; flex: 0 0 auto; }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            min-height: 111px;
            padding: 14px max(18px, calc((100vw - var(--hero-content)) / 2));
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: none;
        }

        .brand-link {
            display: flex;
            align-items: center;
            min-width: 260px;
        }

        .brand-link img {
            width: 250px;
            height: 92px;
            object-fit: contain;
            object-position: left center;
        }

        .brand-wordmark { display: grid; gap: 5px; }
        .brand-wordmark strong { color: var(--brand); font-size: 34px; font-weight: 900; line-height: .85; }
        .brand-wordmark span { color: var(--brand); font-size: 10px; font-weight: 900; line-height: 1; text-transform: uppercase; letter-spacing: .38em; }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 27px;
            color: var(--ink);
            font-size: 18px;
            font-weight: 700;
        }

        .nav-links a { padding: 12px 0; }
        .nav-links a:hover,
        .nav-links a[aria-current="page"] { color: var(--coral); }

        .nav-item { position: relative; }

        .nav-trigger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 12px 0;
            color: inherit;
            font: inherit;
            font-weight: 700;
        }

        .nav-trigger:hover,
        .nav-item:hover .nav-trigger,
        .nav-item:focus-within .nav-trigger { color: var(--coral); }

        .submenu {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            display: grid;
            min-width: 280px;
            padding: 10px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 18px 42px rgba(45, 18, 75, 0.16);
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        }

        .nav-item:hover .submenu,
        .nav-item:focus-within .submenu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .submenu a {
            padding: 10px 12px;
            border-radius: 6px;
            color: var(--ink);
            line-height: 1.2;
        }

        .submenu a:hover,
        .submenu a[aria-current="page"] {
            color: var(--brand);
            background: var(--brand-soft);
        }

        .section {
            padding: 62px var(--pad);
            border-bottom: 1px solid var(--line);
        }

        .section--soft {
            background: linear-gradient(180deg, #ffd3df 0%, #ffd6c3 100%);
        }

        .container {
            width: min(100%, var(--content));
            margin: 0 auto;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 18px;
            border: 1px solid transparent;
            border-radius: 999px;
            color: #ffffff;
            background: var(--coral);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px rgba(255, 112, 68, 0.22);
        }

        .button--light {
            color: var(--brand);
            background: #ffffff;
            border-color: rgba(45, 18, 75, 0.16);
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--coral);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        h1, h2, h3, p { margin-top: 0; }
        p { color: var(--muted); line-height: 1.75; }

        .footer {
            padding: 48px var(--pad) 28px;
            color: rgba(255, 255, 255, 0.78);
            background: var(--brand);
        }

        .footer__grid {
            display: grid;
            grid-template-columns: minmax(220px, 1.2fr) repeat(3, minmax(140px, 1fr));
            gap: 36px;
            width: min(100%, var(--content));
            margin: 0 auto 32px;
        }

        .footer__brand img {
            width: min(100%, 300px);
            height: auto;
            padding: 8px 10px;
            border-radius: 8px;
            background: #ffffff;
        }

        .footer h3 {
            margin: 0 0 14px;
            color: #ffffff;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.15;
        }

        .footer a,
        .footer p {
            display: block;
            margin: 0 0 24px;
            color: rgba(255, 255, 255, 0.92);
            font-size: 18px;
            font-weight: 700;
            line-height: 1.55;
        }

        .footer__bottom {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            width: min(100%, var(--content));
            margin: 0 auto;
            padding-top: 28px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.92);
            font-size: 18px;
            font-weight: 700;
        }

        @yield('styles')

        @media (max-width: 980px) {
            .site-header {
                align-items: center;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px 18px;
            }

            .brand-link {
                justify-content: flex-start;
                min-width: auto;
            }

            .brand-link img {
                width: 220px;
                height: 82px;
            }

            .nav-links {
                flex: 1 1 100%;
                flex-wrap: wrap;
                justify-content: center;
                gap: 14px;
                max-width: 100%;
                overflow: hidden;
            }

            .submenu {
                display: none !important;
            }

            .footer__grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .topbar,
            .site-header,
            .section,
            .footer {
                padding-left: 20px;
                padding-right: 20px;
            }

            .topbar {
                align-items: center;
                flex-direction: column;
                gap: 8px;
                min-width: 0;
            }

            .topbar__group {
                justify-content: center;
                width: 100%;
                gap: 8px 12px;
            }

            .topbar__item {
                white-space: normal;
            }

            .topbar__group:first-child .topbar__item:nth-child(2) {
                display: none;
            }

            .site-header {
                justify-content: center;
                min-width: 0;
            }

            .brand-link img {
                width: 190px;
                height: 70px;
            }

            .nav-links {
                justify-content: flex-start;
                gap: 12px 14px;
                width: 100%;
                min-width: 0;
                overflow-x: auto;
                overflow-y: hidden;
                padding-bottom: 5px;
                font-size: 9px;
                line-height: 1.2;
                scrollbar-width: none;
            }

            .nav-links::-webkit-scrollbar { display: none; }

            .nav-links a,
            .nav-trigger { white-space: nowrap; }

            .footer__grid {
                grid-template-columns: 1fr;
            }

            .footer__bottom {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @php
        $services = $services ?? config('ourcare_v2.services');
        $activePage = $activePage ?? '';
        $activeService = $activeService ?? null;
        $brand = \App\Support\CmsContent::get('brand', config('cms.brand'));
        $brandName = $brand['site_name'] ?? config('cms.brand.site_name');
        $footerLogo = ($brand['footer_logo'] ?? null) ?: (($brand['logo'] ?? null) ?: config('cms.brand.footer_logo'));
        $cmsPages = \App\Support\CmsContent::get('pages', config('cms.pages', []));
        $homePage = \App\Support\CmsContent::page('home-v2');
        $footerServiceLinks = array_slice(\App\Support\CmsContent::services(), 0, 3, true);
        $footerLocations = $homePage['locations'] ?? config('cms.pages.home-v2.locations', []);
    @endphp

    <div class="topbar">
        <div class="topbar__group">
            <span class="topbar__item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6.6 4.8 8.7 3c.5-.4 1.2-.4 1.6.1l2 2.8c.3.5.3 1.1-.1 1.5l-1.1 1.2c.9 1.7 2.5 3.3 4.3 4.2l1.2-1c.5-.4 1.1-.4 1.6-.1l2.7 2c.5.4.6 1.1.2 1.6l-1.8 2.2c-.5.6-1.3.9-2.1.7C10.6 16.8 6.1 12.3 4.8 5.9c-.2-.7.1-1.5.8-2.1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Call: {{ $brand['phone'] ?? config('cms.brand.phone') }}
            </span>
            <span class="topbar__item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 6.5h16v11H4v-11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="m4.5 7 7.5 6 7.5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Email: {{ $brand['email'] ?? config('cms.brand.email') }}
            </span>
        </div>
        <div class="topbar__group">
            <a href="{{ url('/login') }}">{{ $brand['sign_in_label'] ?? config('cms.brand.sign_in_label') }}</a>
            <a href="{{ url('/signup-option') }}">{{ $brand['create_account_label'] ?? config('cms.brand.create_account_label') }}</a>
        </div>
    </div>

    <header class="site-header">
        <a href="{{ url('/home-v2') }}" class="brand-link">
            <img src="{{ asset($brand['logo'] ?? config('cms.brand.logo')) }}" alt="{{ $brandName }} logo">
        </a>
        <nav class="nav-links" aria-label="Primary navigation">
            <a href="{{ url('/home-v2') }}" @if($activePage === 'home') aria-current="page" @endif>{{ \App\Support\CmsContent::get('pages.home-v2.label', 'Home') }}</a>
            <a href="{{ url('/about-v2') }}" @if($activePage === 'about') aria-current="page" @endif>{{ \App\Support\CmsContent::get('pages.about-v2.label', 'About Us') }}</a>
            <div class="nav-item">
                <a href="{{ url('/services-v2') }}" class="nav-trigger" @if($activePage === 'services') aria-current="page" @endif>{{ \App\Support\CmsContent::get('pages.services-v2.label', 'Services') }}
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="submenu">
                    @foreach($services as $serviceSlug => $navService)
                        <a href="{{ url('/services/' . $serviceSlug) }}" @if($activeService === $serviceSlug) aria-current="page" @endif>{{ $navService['label'] }}</a>
                    @endforeach
                </div>
            </div>
            <a href="{{ url($brand['updates_url'] ?? config('cms.brand.updates_url')) }}">{{ $brand['updates_label'] ?? config('cms.brand.updates_label') }}</a>
            <a href="{{ url('/onboarding-v2') }}" @if($activePage === 'onboarding') aria-current="page" @endif>{{ \App\Support\CmsContent::get('pages.onboarding-v2.label', 'Onboarding') }}</a>
            <a href="{{ url('/intake-v2') }}" @if($activePage === 'intake') aria-current="page" @endif>{{ \App\Support\CmsContent::get('pages.intake-v2.label', 'Intake') }}</a>
            <a href="{{ url('/contact-v2') }}" @if($activePage === 'contact') aria-current="page" @endif>{{ \App\Support\CmsContent::get('pages.contact-v2.label', 'Contact Us') }}</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer" id="contact">
        <div class="footer__grid">
            <div class="footer__brand">
                <img src="{{ asset($footerLogo) }}" alt="{{ $brandName }} footer logo">
                <p>{{ $homePage['footer_text'] ?? config('cms.pages.home-v2.footer_text') }}</p>
            </div>
            <div>
                <h3>{{ $brand['footer_services_label'] ?? config('cms.brand.footer_services_label') }}</h3>
                <a href="{{ url('/services-v2') }}">{{ $cmsPages['services-v2']['label'] ?? 'Services' }}</a>
                @foreach($footerServiceLinks as $slug => $service)
                    <a href="{{ route('services.detail.v2', $slug) }}">{{ $service['label'] ?? $service['title'] ?? $slug }}</a>
                @endforeach
            </div>
            <div>
                <h3>{{ $brand['footer_quick_links_label'] ?? config('cms.brand.footer_quick_links_label') }}</h3>
                <a href="{{ url('/about-v2') }}">{{ ($cmsPages['about-v2']['label'] ?? null) ?: 'About Us' }}</a>
                <a href="{{ url('/intake-v2') }}">{{ ($cmsPages['intake-v2']['label'] ?? null) ?: 'Intake' }}</a>
                <a href="{{ url('/onboarding-v2') }}">{{ ($cmsPages['onboarding-v2']['label'] ?? null) ?: 'Onboarding' }}</a>
                <a href="{{ url('/contact-v2') }}">{{ ($cmsPages['contact-v2']['label'] ?? null) ?: 'Contact Us' }}</a>
            </div>
            <div>
                <h3>{{ $brand['footer_contact_label'] ?? config('cms.brand.footer_contact_label') }}</h3>
                <a href="tel:{{ preg_replace('/\D+/', '', $brand['phone'] ?? config('cms.brand.phone')) }}">{{ $brand['phone'] ?? config('cms.brand.phone') }}</a>
                <a href="mailto:{{ $brand['email'] ?? config('cms.brand.email') }}">{{ $brand['email'] ?? config('cms.brand.email') }}</a>
                <p>{{ implode(', ', $footerLocations) }}</p>
            </div>
        </div>
        <div class="footer__bottom">
            <span>Copyright &copy; {{ date('Y') }} {{ $brandName }}.</span>
            <span>{{ $homePage['footer_credit'] ?? config('cms.pages.home-v2.footer_credit') }}</span>
        </div>
    </footer>
</body>
</html>
