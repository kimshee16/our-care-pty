@extends('layouts.public-v2', ['activePage' => 'intake'])

@section('title', 'Intake - Our Care')

@section('styles')
    .intake-hero {
        text-align: center;
    }

    .intake-hero h1 {
        max-width: 880px;
        margin: 0 auto 18px;
        color: var(--brand);
        font-size: clamp(2.5rem, 5vw, 4.35rem);
        line-height: 1.08;
        letter-spacing: 0;
    }

    .intake-hero p {
        max-width: 760px;
        margin: 0 auto;
        color: #4d5e68;
        font-size: 17px;
    }

    .path-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
        margin-top: 48px;
    }

    .path-card {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: var(--shadow);
    }

    .path-card img {
        width: 100%;
        height: 260px;
        object-fit: cover;
    }

    .path-card__body {
        padding: 30px;
    }

    .path-card h2 {
        margin-bottom: 12px;
        color: var(--brand);
        font-size: 30px;
        line-height: 1.2;
    }

    .checklist {
        display: grid;
        gap: 14px;
        margin: 24px 0;
        padding: 0;
        list-style: none;
    }

    .checklist li {
        display: flex;
        gap: 10px;
        color: #34444d;
        line-height: 1.55;
    }

    .checklist li::before {
        content: "";
        width: 9px;
        height: 9px;
        flex: 0 0 auto;
        margin-top: 8px;
        border-radius: 50%;
        background: var(--brand);
    }

    .steps {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .step {
        padding: 24px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: linear-gradient(180deg, #ffffff, var(--soft));
    }

    .step span {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        margin-bottom: 16px;
        border-radius: 50%;
        color: #ffffff;
        background: var(--brand);
        font-weight: 900;
    }

    .step h3 {
        margin: 0 0 8px;
        color: var(--brand-dark);
        font-size: 18px;
    }

    @media (max-width: 900px) {
        .path-grid,
        .steps {
            grid-template-columns: 1fr;
        }
    }
@endsection

@section('content')
    <section class="section section--soft">
        <div class="container intake-hero">
            <h1>Start Intake With the Right Support Path</h1>
            <p>Whether you are looking for a support worker or joining Our Care as a qualified worker, intake helps us understand the essentials before we move forward.</p>

            <div class="path-grid">
                <article class="path-card">
                    <img src="{{ asset('hero.jpg') }}" alt="Client intake">
                    <div class="path-card__body">
                        <h2>I Need Support</h2>
                        <p>Tell us about your goals, routines, location, services, and preferred support style so we can help match the right care pathway.</p>
                        <ul class="checklist">
                            <li>Daily living and personal care needs</li>
                            <li>Community access, transport, or domestic assistance</li>
                            <li>NDIS goals, routines, preferences, and availability</li>
                        </ul>
                        <a class="button" href="{{ url('/client-register') }}">Start Client Intake</a>
                    </div>
                </article>

                <article class="path-card">
                    <img src="{{ asset('ready.jpg') }}" alt="Worker intake">
                    <div class="path-card__body">
                        <h2>I Am a Worker</h2>
                        <p>Create your worker profile, share your qualifications, and join a care network focused on reliable, respectful support.</p>
                        <ul class="checklist">
                            <li>Profile and contact details</li>
                            <li>Qualifications, skills, and care experience</li>
                            <li>Availability, preferred roles, and service areas</li>
                        </ul>
                        <a class="button" href="{{ url('/healthcare-register') }}">Start Worker Intake</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="steps">
                <article class="step">
                    <span>1</span>
                    <h3>Create Account</h3>
                    <p>Choose the intake path that matches your role and create your profile.</p>
                </article>
                <article class="step">
                    <span>2</span>
                    <h3>Share Details</h3>
                    <p>Provide support needs, skills, preferences, availability, and key requirements.</p>
                </article>
                <article class="step">
                    <span>3</span>
                    <h3>Review</h3>
                    <p>Our team reviews information so the next steps are clear and well matched.</p>
                </article>
                <article class="step">
                    <span>4</span>
                    <h3>Connect</h3>
                    <p>Move forward with a practical support pathway designed around everyday life.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
