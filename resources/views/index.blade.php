<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Care - Care Workers Marketplace</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        :root {
            --brand-purple: #6a4bcf;
            --brand-purple-dark: #5232a3;
            --text: #1c1c28;
            --muted: #6b6b7c;
            --surface: #ffffff;
            --surface-alt: #f7f6ff;
            --shadow: rgba(0, 0, 0, 0.15);
            --radius: 18px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, rgba(106, 75, 207, 0.18), transparent 55%),
                        linear-gradient(180deg, rgba(106, 75, 207, 0.07), rgba(255, 255, 255, 0));
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        nav {
            position: static;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 44px;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .logo img {
            height: 72px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 26px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .nav-links a {
            padding: 10px 0;
            color: rgba(28,28,40,0.82);
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--brand-purple);
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--brand-purple), var(--brand-purple-dark));
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.75);
            color: var(--text);
            border-color: rgba(106, 75, 207, 0.35);
        }

        .hero {
            position: relative;
            padding: 120px 24px 80px;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('{{ asset('hero.jpg') }}') center/cover no-repeat;
            filter: brightness(0.5) contrast(1);
            z-index: 0;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top, rgba(106, 75, 207, 0.46), rgba(106, 75, 207, 0.1) 55%, rgba(255,255,255,0.35));
            mix-blend-mode: screen;
            z-index: 1;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 1190px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 60px;
            align-items: center;
        }

        .hero-copy {
            max-width: 540px;
        }

        .hero-copy h1 {
            font-size: clamp(2.9rem, 5vw, 4.8rem);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: white;
            margin-bottom: 26px;
        }

        .hero-copy p {
            font-size: 1.05rem;
            line-height: 1.75;
            color: rgba(255,255,255,0.9);
            margin-bottom: 32px;
            max-width: 520px;
        }

        .search-pill {
            display: flex;
            gap: 10px;
            padding: 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.92);
            box-shadow: 0 16px 30px rgba(0,0,0,0.18);
            border: 1px solid rgba(255,255,255,0.6);
            max-width: 630px;
        }

        .search-pill input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 15px;
            font-weight: 600;
            color: #1c1c28;
            background: transparent;
        }

        .search-pill input::placeholder {
            color: rgba(28,28,40,0.6);
        }

        .search-btn {
            width: 54px;
            height: 54px;
            border-radius: 999px;
            border: none;
            background: var(--brand-purple);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
        }

        .hero-visual {
            border-radius: 22px;
            overflow: hidden;
            min-height: 420px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.35);
            backdrop-filter: blur(0px);
            display: grid;
            place-items: center;
        }

        .hero-logo {
            width: 400px;
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 12px 22px rgba(0,0,0,0.28));
        }

        .features {
            padding: 80px 24px 64px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .features__title {
            text-align: center;
            font-size: 2.6rem;
            color: var(--brand-purple);
            font-weight: 800;
            margin-bottom: 28px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .feature-card {
            background: rgba(255,255,255,0.82);
            border-radius: var(--radius);
            padding: 28px 26px 30px;
            box-shadow: 0 20px 46px rgba(0,0,0,0.1);
            border: 1px solid rgba(106, 75, 207, 0.15);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 55px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: rgba(106, 75, 207, 0.15);
            font-size: 1.6rem;
            margin-bottom: 16px;
            color: var(--brand-purple);
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: rgba(28,28,40,0.92);
        }

        .feature-card p {
            color: rgba(28,28,40,0.72);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .features-cta {
            text-align: center;
            margin-top: 40px;
        }

        .ready-banner {
            padding: 72px 24px;
            background: linear-gradient(140deg, rgba(106, 75, 207, 0.95) 0%, rgba(86, 57, 168, 0.65) 70%);
            border-radius: 32px;
            max-width: 1100px;
            margin: 0 auto 80px;
            box-shadow: 0 30px 55px rgba(0,0,0,0.15);
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 38px;
            align-items: center;
        }

        .ready-banner h2 {
            font-size: 2.4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 18px;
            max-width: 480px;
        }

        .ready-banner p {
            color: rgba(255,255,255,0.92);
            line-height: 1.7;
            margin-bottom: 24px;
            max-width: 520px;
            font-size: 1.05rem;
        }

        .ready-banner .btn {
            padding: 12px 22px;
        }

        .ready-banner__media {
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 45px rgba(0,0,0,0.2);
            height: 320px;
            position: relative;
        }

        .ready-banner__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ready-banner__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.3), rgba(0,0,0,0.05));
        }

        .worker-steps {
            padding: 64px 24px 96px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .worker-steps__title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--brand-purple);
            margin-bottom: 42px;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: center;
        }

        .step-card {
            background: rgba(255,255,255,0.9);
            border-radius: 22px;
            padding: 22px 18px;
            text-align: center;
            box-shadow: 0 22px 42px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .step-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 46px rgba(0,0,0,0.16);
        }

        .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            background: rgba(106, 75, 207, 0.15);
            color: var(--brand-purple);
            font-size: 1.75rem;
        }

        .step-card h3 {
            font-size: 1.05rem;
            margin: 0;
            color: rgba(28,28,40,0.88);
            font-weight: 700;
        }

        .describe {
            padding: 80px 24px 100px;
            background: radial-gradient(circle at 30% 20%, rgba(106, 75, 207, 0.35), transparent 55%),
                        linear-gradient(180deg, white, rgba(241, 238, 255, 0.5));
        }

        .describe__inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 42px;
            align-items: center;
        }

        .describe__copy {
            max-width: 520px;
        }

        .describe__copy h2 {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.1;
            color: var(--brand-purple);
            margin-bottom: 22px;
        }

        .describe__copy p {
            color: rgba(28,28,40,0.75);
            line-height: 1.7;
            margin-bottom: 28px;
            font-size: 1.05rem;
        }

        .describe__copy .btn {
            width: fit-content;
        }

        .describe__media {
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 28px 58px rgba(0,0,0,0.16);
            height: 360px;
            position: relative;
        }

        .describe__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        footer {
            padding: 68px 24px 42px;
            background: rgba(16, 2, 29, 0.95);
            color: rgba(255,255,255,0.88);
        }

        .footer-inner {
            max-width: 1120px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 32px;
        }

        .footer-col h4 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
            color: rgba(255,255,255,0.95);
        }

        .footer-col a {
            display: block;
            margin-bottom: 12px;
            font-size: 14px;
            color: rgba(255,255,255,0.72);
            transition: color 0.2s ease;
        }

        .footer-col a:hover {
            color: rgba(255,255,255,0.95);
        }

        .footer-bottom {
            margin-top: 52px;
            text-align: center;
            font-size: 13px;
            color: rgba(255,255,255,0.62);
        }

        .footer-social {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-top: 18px;
        }

        .footer-social a {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .footer-social a:hover {
            background: rgba(255,255,255,0.25);
        }

        @media (max-width: 1100px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .ready-banner {
                grid-template-columns: 1fr;
            }

            .describe__inner {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            nav {
                padding: 18px 18px;
                flex-wrap: wrap;
                gap: 14px;
            }

            .logo img {
                height: 58px;
            }

            .nav-links {
                order: 3;
                width: 100%;
                justify-content: center;
            }

            .nav-actions {
                order: 2;
                width: 100%;
                justify-content: center;
            }

            .features {
                padding: 62px 18px 48px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 100px 18px 60px;
            }

            .worker-steps {
                padding: 62px 18px 70px;
            }

            .steps {
                grid-template-columns: 1fr;
            }

            .footer-inner {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .logo img {
                height: 52px;
            }

            .nav-links {
                display: none;
            }

            .hero-copy h1 {
                font-size: 2.4rem;
            }

            .hero-copy p {
                font-size: 1rem;
            }

            .search-pill {
                flex-direction: column;
                align-items: stretch;
            }

            .search-btn {
                width: 100%;
                height: 52px;
            }

            .ready-banner {
                padding: 52px 18px;
            }

            .describe {
                padding: 56px 18px 72px;
            }

            .footer-inner {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <a href="{{ url('/') }}"><img src="{{ asset('logo2.png') }}" alt="Our Care" /></a>
        </div>
        <div class="nav-links">
            <a href="#how">How it works</a>
            <a href="#why">Why Our Care?</a>
        </div>
        <div class="nav-actions">
            <a href="{{ url('/login') }}" class="btn btn-secondary">Login</a>
            <a href="{{ url('/signup-option') }}" class="btn btn-primary">Create Your Account Now</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-inner">
            <div class="hero-copy">
                <h1>Find trusted health workers who deliver</h1>
                <p>Search across verified carers, nurses and support staff that match your preferred skills and availability.</p>

                <form class="search-pill" onsubmit="event.preventDefault();">
                    <input type="text" placeholder="Search by service, role, skills, or keywords" />
                    <button class="search-btn" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L15.5 15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="hero-visual">
                <img src="{{ asset('logo3.png') }}" alt="Our Care" class="hero-logo" />
            </div>
        </div>
    </section>

    <section class="features" id="why">
        <h2 class="features__title">How "Our Care" cares for you and your family</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">❤️</div>
                <h3>Personalized Care</h3>
                <p>We provide compassionate and personalized care plans tailored to your family's unique needs.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💲</div>
                <h3>Affordable Pricing</h3>
                <p>We offer competitive pricing with no hidden fees, ensuring quality care that fits your budget.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🌐</div>
                <h3>Vast Network</h3>
                <p>Access a vast network of qualified and vetted care workers, ensuring you find the right match.</p>
            </div>
        </div>
        <div class="features-cta">
            <a href="{{ url('/signup-option') }}" class="btn btn-primary">Create Your Account Now</a>
        </div>
    </section>

    <section class="ready-banner" id="hire">
        <div>
            <h2>Ready to hire the best worker you need?</h2>
            <p>Describe your needs and find the perfect caregiver to assist your loved ones. It’s quick, easy, and free to post a job.</p>
            <a href="{{ url('/signup-option') }}" class="btn btn-secondary">Post a Job Now</a>
        </div>
        <div class="ready-banner__media">
            <img src="{{ asset('ready.jpg') }}" alt="Ready to hire" />
            <div class="ready-banner__overlay"></div>
        </div>
    </section>

    <section class="worker-steps">
        <h2 class="worker-steps__title">Interested to join "Our Care" as core worker? Here's how</h2>
        <div class="steps">
            <div class="step-card">
                <div class="step-icon">📝</div>
                <h3>Apply to a job</h3>
            </div>
            <div class="step-card">
                <div class="step-icon">✅</div>
                <h3>Get certified</h3>
            </div>
            <div class="step-card">
                <div class="step-icon">🤝</div>
                <h3>Get hired</h3>
            </div>
            <div class="step-card">
                <div class="step-icon">💰</div>
                <h3>Receive pay</h3>
            </div>
        </div>
    </section>

    <section class="describe">
        <div class="describe__inner">
            <div class="describe__copy">
                <h2>Describe your needs and find the perfect caregiver</h2>
                <p>We make it easy to connect with qualified carers who match your requirements, availability, and care preferences.</p>
                <a href="{{ url('/signup-option') }}" class="btn btn-primary">Learn More</a>
            </div>
            <div class="describe__media">
                <img src="{{ asset('hero.jpg') }}" alt="Caregiver matching" />
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-inner">
            <div class="footer-col">
                <h4>Getting Started</h4>
                <a href="#how">How it works</a>
                <a href="#">FAQ</a>
                <a href="#">Support</a>
                <a href="#">Blog</a>
            </div>
            <div class="footer-col">
                <h4>For Clients</h4>
                <a href="{{ url('/signup-option') }}">Post a Job</a>
                <a href="#">Find Workers</a>
                <a href="#">Pricing</a>
                <a href="#">Reviews</a>
            </div>
            <div class="footer-col">
                <h4>For Workers</h4>
                <a href="#">Browse Jobs</a>
                <a href="#">My Applications</a>
                <a href="#">Earnings</a>
                <a href="#">Profile</a>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Contact Us</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© {{ date('Y') }} Our Care. All rights reserved.</div>
            <div class="footer-social">
                <a href="#" aria-label="Facebook">F</a>
                <a href="#" aria-label="Twitter">T</a>
                <a href="#" aria-label="Instagram">I</a>
                <a href="#" aria-label="LinkedIn">L</a>
            </div>
        </div>
    </footer>

    <script>
        // Simple search stub
        document.querySelectorAll('.search-pill').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                const query = form.querySelector('input')?.value?.trim();
                if (!query) return;
                alert('Searching for: ' + query);
            });
        });
    </script>
</body>
</html>
