@extends('layouts.public-v2', ['activePage' => 'about'])

@section('title', 'About Us - Our Care')

@section('styles')
    .hero-title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-bottom: 54px;
        color: #5f9ec0;
        text-align: center;
    }

    .hero-title img {
        width: 74px;
        height: 74px;
        object-fit: contain;
    }

    .hero-title h1 {
        margin: 0;
        font-size: clamp(2.3rem, 5vw, 4.5rem);
        font-weight: 300;
        line-height: 1.08;
        letter-spacing: 0;
    }

    .community {
        display: grid;
        grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
        gap: 48px;
        align-items: center;
    }

    .community h2,
    .section-heading h2,
    .split-row h2 {
        margin-bottom: 14px;
        color: var(--brand);
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.12;
    }

    .quote {
        display: grid;
        gap: 10px;
        margin-top: 22px;
        color: #394952;
        font-size: 14px;
        line-height: 1.65;
    }

    .image-panel {
        overflow: hidden;
        min-height: 330px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .image-panel img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .update-band {
        padding: 28px var(--pad);
        color: #ffffff;
        background: var(--brand);
    }

    .update-band__inner {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: center;
    }

    .update-band h2 {
        margin: 0;
        color: #ffffff;
        font-size: 18px;
        line-height: 1.45;
    }

    .update-band p {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.78);
        font-size: 13px;
    }

    .section-heading {
        max-width: 780px;
        margin: 0 auto 42px;
        text-align: center;
    }

    .promise-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
        max-width: 900px;
        margin: 0 auto;
    }

    .promise-card {
        display: grid;
        grid-template-rows: 250px auto;
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(20, 60, 78, 0.07);
    }

    .promise-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .promise-card__body {
        display: grid;
        justify-items: center;
        padding: 24px;
        text-align: center;
    }

    .promise-card h3 {
        margin: 0 0 12px;
        color: var(--brand);
        font-size: 24px;
        line-height: 1.25;
    }

    .split-rows {
        display: grid;
        gap: 30px;
    }

    .split-row {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.95fr);
        gap: 40px;
        align-items: center;
        padding: 32px 0;
        border-top: 1px solid var(--line);
    }

    .split-row:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .split-row .button {
        margin-top: 22px;
        width: min(100%, 480px);
    }

    @media (max-width: 900px) {
        .community,
        .update-band__inner,
        .promise-grid,
        .split-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .hero-title {
            flex-direction: column;
        }
    }
@endsection

@section('content')
    <section class="section section--soft">
        <div class="container">
            <div class="hero-title">
                <img src="{{ asset('logo2.png') }}" alt="Our Care logo mark">
                <h1>Our Care Your Wellness</h1>
            </div>

            <div class="community">
                <div>
                    <p class="eyebrow">Our community support</p>
                    <h2>Support that meets people where they are</h2>
                    <p>Our Care is built around community, dignity and practical support. We help participants, families and carers connect with dependable workers for everyday routines, community access and goal-focused NDIS support.</p>
                    <div class="quote">
                        <span>"Slow down, take it easy."</span>
                        <span>"My development with my disabilities is about having the right people beside me."</span>
                    </div>
                </div>
                <div class="image-panel">
                    <img src="{{ asset('contact.jpg') }}" alt="Our Care community support">
                </div>
            </div>
        </div>
    </section>

    <section class="update-band" id="ndis">
        <div class="container update-band__inner">
            <div>
                <h2>01 Jul 2026 imposing financial limits for categories including personal care, specialised nursing, and supported accommodation.</h2>
                <p>Keep your care planning current with NDIS pricing and service updates.</p>
            </div>
            <a class="button button--light" href="{{ url('/services-v2') }}">Read More</a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <h2>Support That Helps You Live Life Your Way</h2>
                <p>Our model is simple: listen carefully, match thoughtfully, and support consistently.</p>
            </div>

            <div class="promise-grid">
                <article class="promise-card">
                    <img src="{{ asset('hero.jpg') }}" alt="Person-centred support">
                    <div class="promise-card__body">
                        <h3>Person-Centred Support</h3>
                        <p>Support tailored around your needs, preferences and routines while achieving goals.</p>
                        <a class="button" href="{{ url('/signup-option') }}">Book a Consultation</a>
                    </div>
                </article>
                <article class="promise-card">
                    <img src="{{ asset('ready.jpg') }}" alt="Peace of mind for families">
                    <div class="promise-card__body">
                        <h3>Peace of Mind for Families</h3>
                        <p>Helping individuals, families and carers feel more supported every day with accountability and care.</p>
                        <a class="button" href="{{ url('/signup-option') }}">Book a Consultation</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container split-rows">
            <article class="split-row">
                <div>
                    <h2>Hire a Worker</h2>
                    <p>Check the qualification and hire your community support. Create an account to begin intake and tell us what kind of support you need.</p>
                    <a class="button" href="{{ url('/client-register') }}">Create Your Account Now</a>
                </div>
                <div class="image-panel">
                    <img src="{{ asset('hero.jpg') }}" alt="Hire a support worker">
                </div>
            </article>

            <article class="split-row">
                <div>
                    <h2>Be a Core Worker</h2>
                    <p>We value your qualification. Explore our network of community advocating professional care and build a profile clients can trust.</p>
                    <a class="button" href="{{ url('/healthcare-register') }}">Create Your Account</a>
                </div>
                <div class="image-panel">
                    <img src="{{ asset('ready.jpg') }}" alt="Become an Our Care worker">
                </div>
            </article>
        </div>
    </section>
@endsection
