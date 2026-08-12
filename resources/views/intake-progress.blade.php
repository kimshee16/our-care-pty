@extends('layouts.public-v2', ['activePage' => 'intake'])

@section('title', 'Intake - Our Care')

@section('styles')
    .progress-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .progress-hero__grid, .split { display: grid; grid-template-columns: minmax(0, .9fr) minmax(320px, 1.1fr); gap: clamp(28px, 6vw, 72px); align-items: center; }
    .progress-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.25rem); line-height: 1.04; }
    .progress-hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .progress-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .progress-image img { width: 100%; height: 100%; object-fit: cover; }
    .heading { max-width: 740px; margin: 0 auto 34px; text-align: center; }
    .heading h2, .split h2 { margin: 0 0 12px; color: var(--ink); font-size: clamp(1.8rem, 3vw, 2.6rem); }
    .intake-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .intake-card { padding: 26px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .intake-card strong { display: inline-flex; margin-bottom: 14px; color: var(--coral); font-size: 12px; text-transform: uppercase; }
    .intake-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 21px; }
    .list { display: grid; gap: 12px; margin: 0 0 24px; padding: 0; list-style: none; }
    .list li { padding: 14px 16px; border-radius: 8px; color: var(--ink); background: #fff; box-shadow: 0 10px 22px rgba(45,18,75,.06); font-size: 14px; font-weight: 800; }
    @media (max-width: 980px) { .progress-hero__grid, .split, .intake-grid { grid-template-columns: 1fr; } }
@endsection

@section('content')
    <section class="progress-hero">
        <div class="container progress-hero__grid">
            <div><p class="eyebrow">Intake</p><h1>Tell us what support needs to feel right.</h1><p>Intake helps us understand goals, routines, safety needs, preferences, and the services that may be the best fit.</p><a class="button" href="{{ url('/client-register') }}">Start client intake</a></div>
            <div class="progress-image"><img src="{{ asset('hero.jpg') }}" alt="Participant intake conversation"></div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="heading"><h2>How intake works</h2><p>A focused process that gives the team the context needed to support you well.</p></div>
            <div class="intake-grid">
                <article class="intake-card"><strong>Step 01</strong><h3>Share your needs</h3><p>Tell us about routines, goals, supports, and important preferences.</p></article>
                <article class="intake-card"><strong>Step 02</strong><h3>Review options</h3><p>We help identify services and the right pathway for support or worker matching.</p></article>
                <article class="intake-card"><strong>Step 03</strong><h3>Plan next steps</h3><p>Move into onboarding, coordination, applications, scheduling, or consultation.</p></article>
            </div>
        </div>
    </section>
    <section class="section section--soft">
        <div class="container split">
            <div><p class="eyebrow">Helpful details</p><h2>Bring the everyday picture.</h2><ul class="list"><li>Current support goals and NDIS plan information</li><li>Preferred days, times, location, and access needs</li><li>Personal care, transport, domestic, or community support priorities</li><li>Family, carer, or coordinator contact preferences</li></ul><a class="button" href="{{ url('/contact-v2') }}">Ask a question</a></div>
            <div class="progress-image"><img src="{{ asset('contact.jpg') }}" alt="Community support planning"></div>
        </div>
    </section>
@endsection
