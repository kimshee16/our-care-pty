@extends('layouts.public-v2', ['activePage' => 'onboarding'])

@section('title', 'Onboarding - Our Care')

@section('styles')
    .progress-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .progress-hero__grid, .split { display: grid; grid-template-columns: minmax(0, .9fr) minmax(320px, 1.1fr); gap: clamp(28px, 6vw, 72px); align-items: center; }
    .progress-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.25rem); line-height: 1.04; }
    .progress-hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .progress-image { overflow: hidden; min-height: 400px; border-radius: 8px; box-shadow: var(--shadow); }
    .progress-image img { width: 100%; height: 100%; object-fit: cover; }
    .heading { max-width: 740px; margin: 0 auto 34px; text-align: center; }
    .heading h2, .split h2 { margin: 0 0 12px; color: var(--ink); font-size: clamp(1.8rem, 3vw, 2.6rem); }
    .steps, .check-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }
    .step, .check-card { padding: 24px; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .step span { display: grid; place-items: center; width: 42px; height: 42px; margin-bottom: 16px; border-radius: 50%; color: #fff; background: var(--brand); font-weight: 900; }
    .step h3, .check-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 19px; }
    .step p { margin: 0; font-size: 13px; }
    .check-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .check-card ul { display: grid; gap: 9px; margin: 0; padding-left: 18px; color: var(--muted); font-size: 14px; line-height: 1.55; }
    @media (max-width: 980px) { .progress-hero__grid, .split { grid-template-columns: 1fr; } .steps { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 640px) { .steps, .check-grid { grid-template-columns: 1fr; } }
@endsection

@section('content')
    <section class="progress-hero">
        <div class="container progress-hero__grid">
            <div><p class="eyebrow">Onboarding</p><h1>A clearer start for workers and participants.</h1><p>Our onboarding flow helps everyone understand expectations, documents, goals, communication, and the first steps of support.</p><a class="button" href="{{ url('/signup-option') }}">Create account</a></div>
            <div class="progress-image"><img src="{{ asset('ready.jpg') }}" alt="Support worker preparing for onboarding"></div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="heading"><h2>What onboarding covers</h2><p>Simple steps, clear documentation, and practical guidance.</p></div>
            <div class="steps">
                <article class="step"><span>01</span><h3>Create profile</h3><p>Start with the account type and essential contact information.</p></article>
                <article class="step"><span>02</span><h3>Share details</h3><p>Add goals, availability, qualifications, preferences, or care needs.</p></article>
                <article class="step"><span>03</span><h3>Review fit</h3><p>Our team checks details and helps clarify the right pathway.</p></article>
                <article class="step"><span>04</span><h3>Begin support</h3><p>Move into intake, matching, applications, or service coordination.</p></article>
            </div>
        </div>
    </section>
    <section class="section section--soft">
        <div class="container split">
            <div class="progress-image"><img src="{{ asset('contact.jpg') }}" alt="Support conversation in the community"></div>
            <div><p class="eyebrow">Before you begin</p><h2>Have the right information ready.</h2><div class="check-grid"><article class="check-card"><h3>Participants</h3><ul><li>NDIS goals and support needs</li><li>Preferred routines and times</li><li>Safety or access information</li><li>Family or carer contacts</li></ul></article><article class="check-card"><h3>Workers</h3><ul><li>Qualifications and checks</li><li>Availability and location</li><li>Experience and key skills</li><li>Profile photo and contact details</li></ul></article></div></div>
        </div>
    </section>
@endsection
