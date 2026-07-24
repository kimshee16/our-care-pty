<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Our Care')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        :root {
            --brand: #247c9f;
            --brand-dark: #155d79;
            --brand-soft: #e7f4f8;
            --ink: #24313a;
            --muted: #6d7a83;
            --line: #e5edf1;
            --soft: #f7fbfc;
            --mint: #7fc7b1;
            --coral: #e77762;
            --sun: #f3c969;
            --pad: clamp(24px, 5vw, 72px);
            --content: 1180px;
            --shadow: 0 18px 42px rgba(28, 54, 68, 0.12);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            color: var(--ink);
            background: #ffffff;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a { color: inherit; text-decoration: none; }
        img, svg { display: block; max-width: 100%; }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            min-height: 42px;
            padding: 8px var(--pad);
            border-bottom: 1px solid var(--line);
            background: #fbfdfe;
            color: var(--muted);
            font-size: 13px;
        }

        .topbar__group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
        }

        .topbar__item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .icon { width: 18px; height: 18px; flex: 0 0 auto; }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            min-height: 78px;
            padding: 12px var(--pad);
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 180px;
        }

        .brand-link img {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .brand-wordmark { display: grid; gap: 2px; }
        .brand-wordmark strong { color: var(--brand-dark); font-size: 17px; line-height: 1; }
        .brand-wordmark span { color: var(--muted); font-size: 12px; }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 22px;
            color: #495761;
            font-size: 13px;
            font-weight: 700;
        }

        .nav-links a { padding: 8px 0; }
        .nav-links a:hover,
        .nav-links a[aria-current="page"] { color: var(--brand); }

        .nav-item { position: relative; }

        .nav-trigger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 0;
            color: inherit;
            font: inherit;
            font-weight: 700;
        }

        .nav-trigger:hover,
        .nav-item:hover .nav-trigger,
        .nav-item:focus-within .nav-trigger { color: var(--brand); }

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
            box-shadow: 0 18px 42px rgba(24, 50, 62, 0.16);
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
            color: #495761;
            line-height: 1.2;
        }

        .submenu a:hover,
        .submenu a[aria-current="page"] {
            color: var(--brand-dark);
            background: var(--brand-soft);
        }

        .section {
            padding: 62px var(--pad);
            border-bottom: 1px solid var(--line);
        }

        .section--soft {
            background:
                linear-gradient(180deg, rgba(231, 244, 248, 0.72), #ffffff 60%),
                linear-gradient(90deg, rgba(127, 199, 177, 0.14), rgba(231, 119, 98, 0.08));
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
            border-radius: 6px;
            color: #ffffff;
            background: var(--brand);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 22px rgba(22, 93, 121, 0.16);
        }

        .button--light {
            color: var(--brand-dark);
            background: #ffffff;
            border-color: rgba(36, 124, 159, 0.22);
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
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 28px var(--pad);
            color: rgba(255, 255, 255, 0.78);
            background: #1e303a;
            font-size: 13px;
        }

        .footer a { color: #ffffff; font-weight: 800; }

        @yield('styles')

        @media (max-width: 980px) {
            .site-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .submenu {
                position: static;
                min-width: min(100vw - 48px, 360px);
                margin-top: 8px;
                opacity: 1;
                visibility: visible;
                transform: none;
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
                align-items: flex-start;
                flex-direction: column;
            }

            .nav-links { gap: 14px; }
        }
    </style>
</head>
<body>
    @php
        $services = $services ?? config('ourcare_v2.services');
        $activePage = $activePage ?? '';
        $activeService = $activeService ?? null;
    @endphp

    <div class="topbar">
        <div class="topbar__group">
            <span class="topbar__item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6.6 4.8 8.7 3c.5-.4 1.2-.4 1.6.1l2 2.8c.3.5.3 1.1-.1 1.5l-1.1 1.2c.9 1.7 2.5 3.3 4.3 4.2l1.2-1c.5-.4 1.1-.4 1.6-.1l2.7 2c.5.4.6 1.1.2 1.6l-1.8 2.2c-.5.6-1.3.9-2.1.7C10.6 16.8 6.1 12.3 4.8 5.9c-.2-.7.1-1.5.8-2.1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Call: 0425 795 830
            </span>
            <span class="topbar__item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 6.5h16v11H4v-11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="m4.5 7 7.5 6 7.5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Email: admin@ourcarepty.com
            </span>
        </div>
        <div class="topbar__group">
            <a href="{{ url('/login') }}">Sign in</a>
            <a href="{{ url('/signup-option') }}">Create account</a>
        </div>
    </div>

    <header class="site-header">
        <a href="{{ url('/home-v2') }}" class="brand-link">
            <img src="{{ asset('logo3.png') }}" alt="Our Care logo">
            <span class="brand-wordmark">
                <strong>Our Care</strong>
                <span>Your Wellness</span>
            </span>
        </a>
        <nav class="nav-links" aria-label="Primary navigation">
            <a href="{{ url('/home-v2') }}" @if($activePage === 'home') aria-current="page" @endif>Home</a>
            <a href="{{ url('/about-v2') }}" @if($activePage === 'about') aria-current="page" @endif>About Us</a>
            <div class="nav-item">
                <a href="{{ url('/services-v2') }}" class="nav-trigger" @if($activePage === 'services') aria-current="page" @endif>Services
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <div class="submenu">
                    @foreach($services as $serviceSlug => $navService)
                        <a href="{{ route('services.detail.v2', $serviceSlug) }}" @if($activeService === $serviceSlug) aria-current="page" @endif>{{ $navService['label'] }}</a>
                    @endforeach
                </div>
            </div>
            <a href="{{ url('/services-v2#ndis') }}">NDIS Updates</a>
            <a href="{{ url('/onboarding-v2') }}" @if($activePage === 'onboarding') aria-current="page" @endif>Onboarding</a>
            <a href="{{ url('/intake-v2') }}" @if($activePage === 'intake') aria-current="page" @endif>Intake</a>
            <a href="{{ url('/contact-v2') }}" @if($activePage === 'contact') aria-current="page" @endif>Contact Us</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer" id="contact">
        <span>© {{ date('Y') }} Our Care Pty Ltd. All rights reserved.</span>
        <a href="mailto:admin@ourcarepty.com">admin@ourcarepty.com</a>
    </footer>
</body>
</html>
