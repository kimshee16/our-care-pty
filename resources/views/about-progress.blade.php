@extends('layouts.public-v2', ['activePage' => 'about'])

@section('title', 'About Our Care')

@section('styles')
    .progress-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .progress-hero__grid, .split { display: grid; grid-template-columns: minmax(0, .9fr) minmax(320px, 1.1fr); gap: clamp(28px, 6vw, 72px); align-items: center; }
    .progress-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.4rem, 5vw, 4.45rem); line-height: 1.04; }
    .progress-hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .progress-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .progress-image img { width: 100%; height: 100%; object-fit: cover; }
    .section-heading { max-width: 760px; margin: 0 auto 34px; text-align: center; }
    .section-heading h2, .split h2 { margin: 0 0 12px; color: var(--ink); font-size: clamp(1.8rem, 3vw, 2.6rem); }
    .card-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .info-card { padding: 26px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .info-card strong { display: block; margin-bottom: 10px; color: var(--coral); font-size: 12px; text-transform: uppercase; }
    .info-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 21px; }
    .info-card p { margin: 0; font-size: 14px; }
    .cta-strip { padding: 62px var(--pad); color: #fff; background: var(--brand); text-align: center; }
    .cta-strip h2 { color: #fff; font-size: clamp(1.8rem, 3vw, 2.6rem); }
    .cta-strip p { max-width: 650px; margin: 0 auto 22px; color: rgba(255,255,255,.82); }
    @media (max-width: 900px) { .progress-hero__grid, .split, .card-grid { grid-template-columns: 1fr; } }
@endsection

@section('content')
    <section class="progress-hero">
        <div class="container progress-hero__grid">
            <div>
                <p class="eyebrow">About Our Care</p>
                <h1>Support built around dignity, choice, and everyday life.</h1>
                <p>Our Care connects participants, families, and support workers through clear intake, respectful communication, and practical NDIS-aligned services.</p>
                <a class="button" href="{{ url('/intake-v2') }}">Start intake</a>
            </div>
            <div class="progress-image"><img src="{{ asset('contact.jpg') }}" alt="Our Care participant supported in the community"></div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading"><h2>How we make support feel easier</h2><p>We keep the process simple, human, and focused on the person receiving support.</p></div>
            <div class="card-grid">
                <article class="info-card"><strong>Listen</strong><h3>Understand your routine</h3><p>We begin with goals, preferences, safety needs, and the everyday details that shape good support.</p></article>
                <article class="info-card"><strong>Plan</strong><h3>Map the right services</h3><p>We connect personal care, transport, domestic assistance, counselling, and community participation.</p></article>
                <article class="info-card"><strong>Support</strong><h3>Stay clear and responsive</h3><p>Our team keeps communication steady as needs, rosters, providers, and goals change.</p></article>
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="container split">
            <div class="progress-image"><img src="{{ asset('ready.jpg') }}" alt="Our Care support worker preparing care"></div>
            <div>
                <p class="eyebrow">Our approach</p>
                <h2>Care workers matched with purpose.</h2>
                <p>Dependable support starts with people who are prepared, qualified, and aligned with the participant's needs. Our worker pathway keeps expectations clear from the first step.</p>
                <a class="button" href="{{ url('/healthcare-register') }}">Apply as worker</a>
            </div>
        </div>
    </section>

    <section class="cta-strip">
        <div class="container">
            <h2>Ready to talk about support?</h2>
            <p>Tell us what you need and our team will help you understand the best next step.</p>
            <a class="button button--light" href="{{ url('/contact-v2') }}">Contact Our Care</a>
        </div>
    </section>
@endsection
