@extends('layouts.public-v2', ['activePage' => 'home'])

@section('title', 'Our Care - Home V2')

@section('styles')
    .hero-title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-bottom: 46px;
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

    .hero-grid,
    .mosaic-points,
    .support-services-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .hero-card,
    .service-preview,
    .support-service-card {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(20, 60, 78, 0.07);
    }

    .hero-card {
        display: grid;
        grid-template-rows: 245px auto;
    }

    .hero-card img,
    .feature-image img,
    .mosaic-image img,
    .mosaic-card img,
    .action-row__image img,
    .support-service-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-card__body {
        display: grid;
        justify-items: center;
        padding: 24px;
        text-align: center;
    }

    .hero-card h2 {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 24px;
        line-height: 1.2;
    }

    .hero-card p,
    .service-preview p {
        margin-bottom: 18px;
    }

    .mosaic-layout {
        display: grid;
        gap: 34px;
    }

    .mosaic-feature {
        display: grid;
        grid-template-columns: minmax(250px, 0.85fr) minmax(0, 1.75fr);
        gap: 24px;
        align-items: stretch;
    }

    .mosaic-stack {
        display: grid;
        gap: 24px;
    }

    .mosaic-image {
        overflow: hidden;
        min-height: 230px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .mosaic-image--large {
        min-height: 486px;
    }

    .mosaic-points {
        max-width: 840px;
        margin: 0 auto;
        gap: 46px;
    }

    .mosaic-card {
        display: grid;
        grid-template-columns: 145px minmax(0, 1fr);
        gap: 28px;
        align-items: center;
    }

    .mosaic-card__image {
        overflow: hidden;
        aspect-ratio: 1;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .mosaic-card h3 {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 20px;
        line-height: 1.25;
    }

    .feature-row {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 34px;
        align-items: center;
    }

    .feature-row h2,
    .section-heading h2 {
        margin-bottom: 14px;
        color: var(--brand);
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.12;
    }

    .feature-image {
        overflow: hidden;
        min-height: 330px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .community-vector-row {
        display: grid;
        grid-template-columns: minmax(0, 0.8fr) minmax(320px, 1.2fr);
        gap: 44px;
        align-items: center;
    }

    .community-vector-copy h2 {
        margin: 0 0 20px;
        color: var(--brand);
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.12;
    }

    .community-vector-copy p {
        margin: 0 0 10px;
        color: #1f2937;
        font-size: 15px;
        line-height: 1.75;
    }

    .vector-fade {
        position: relative;
        overflow: hidden;
        min-height: 340px;
        border-radius: 8px;
        background: #2f3336;
        box-shadow: var(--shadow);
    }

    .vector-fade img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        animation: vectorFade 30s infinite;
    }

    .vector-fade img:nth-child(1) {
        animation-delay: 0s;
    }

    .vector-fade img:nth-child(2) {
        animation-delay: 5s;
    }

    .vector-fade img:nth-child(3) {
        animation-delay: 10s;
    }

    .vector-fade img:nth-child(4) {
        animation-delay: 15s;
    }

    .vector-fade img:nth-child(5) {
        animation-delay: 20s;
    }

    .vector-fade img:nth-child(6) {
        animation-delay: 25s;
    }

    @keyframes vectorFade {
        0%,
        18%,
        100% {
            opacity: 0;
        }

        4%,
        14% {
            opacity: 1;
        }
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

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
    }

    .service-preview {
        display: grid;
        grid-template-rows: 180px auto;
    }

    .service-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-preview__body {
        display: grid;
        justify-items: center;
        padding: 22px;
        text-align: center;
    }

    .service-preview h3 {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 22px;
        line-height: 1.25;
    }

    .action-stack {
        display: grid;
        border-top: 1px solid var(--line);
        border-bottom: 1px solid var(--line);
    }

    .action-row {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(300px, 0.9fr);
        gap: 34px;
        align-items: center;
        padding: 44px 0;
    }

    .action-row + .action-row {
        border-top: 1px solid var(--line);
    }

    .action-row__copy {
        max-width: 620px;
    }

    .action-row__copy h2 {
        margin: 0 0 28px;
        color: #5f9ec0;
        font-size: clamp(2.2rem, 4vw, 3.1rem);
        font-weight: 300;
        line-height: 1.15;
    }

    .action-row__copy p {
        margin: 0 0 42px;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.7;
    }

    .action-row__copy .button {
        width: 100%;
        justify-content: center;
    }

    .action-row__image {
        overflow: hidden;
        min-height: 245px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .support-services-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .support-service-card {
        display: grid;
        grid-template-rows: 150px auto;
    }

    .support-service-card__body {
        padding: 18px 14px 20px;
    }

    .support-service-card h3 {
        margin: 0 0 8px;
        color: var(--brand);
        font-size: 17px;
        line-height: 1.25;
    }

    .support-service-card p {
        margin: 0;
        font-size: 13px;
        line-height: 1.65;
    }

    @media (max-width: 900px) {
        .hero-grid,
        .mosaic-feature,
        .mosaic-points,
        .feature-row,
        .community-vector-row,
        .action-row,
        .preview-grid,
        .support-services-grid,
        .update-band__inner {
            grid-template-columns: 1fr;
        }

        .mosaic-image--large {
            min-height: 330px;
        }
    }

    @media (max-width: 640px) {
        .hero-title {
            flex-direction: column;
        }

        .hero-card {
            grid-template-rows: 210px auto;
        }

        .mosaic-card {
            grid-template-columns: 112px minmax(0, 1fr);
            gap: 18px;
        }

        .action-row__image {
            min-height: 220px;
        }

        .vector-fade {
            min-height: 260px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .vector-fade img {
            animation: none;
        }

        .vector-fade img:first-child {
            opacity: 1;
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

            <div class="hero-grid">
                <article class="hero-card">
                    <img src="{{ asset('hero.jpg') }}" alt="Support worker assisting a client">
                    <div class="hero-card__body">
                        <h2>Hire trusted community support</h2>
                        <p>Find qualified workers for personal care, community access, domestic support, transport and daily living needs.</p>
                        <a class="button" href="{{ url('/client-register') }}">Start Client Intake</a>
                    </div>
                </article>

                <article class="hero-card">
                    <img src="{{ asset('ready.jpg') }}" alt="Care team preparing support">
                    <div class="hero-card__body">
                        <h2>Join as a core worker</h2>
                        <p>Show your qualifications, build your profile, and connect with families looking for dependable support.</p>
                        <a class="button" href="{{ url('/healthcare-register') }}">Apply as Worker</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container mosaic-layout">
            <div class="mosaic-feature">
                <div class="mosaic-stack">
                    <div class="mosaic-image">
                        <img src="{{ asset('hero.jpg') }}" alt="Support worker sharing a moment with an older participant">
                    </div>
                    <div class="mosaic-image">
                        <img src="{{ asset('ready.jpg') }}" alt="Support worker speaking with a participant">
                    </div>
                </div>

                <div class="mosaic-image mosaic-image--large">
                    <img src="{{ asset('contact.jpg') }}" alt="Participant receiving in-home mobility support">
                </div>
            </div>

            <div class="mosaic-points">
                <article class="mosaic-card">
                    <div class="mosaic-card__image">
                        <img src="{{ asset('hero.jpg') }}" alt="Community support conversation">
                    </div>
                    <div>
                        <h3>Care shaped around your day</h3>
                        <p>Personal support, domestic assistance, transport and community participation planned around real routines.</p>
                    </div>
                </article>

                <article class="mosaic-card">
                    <div class="mosaic-card__image">
                        <img src="{{ asset('ready.jpg') }}" alt="Qualified care worker consultation">
                    </div>
                    <div>
                        <h3>Workers ready to help</h3>
                        <p>Connect with qualified support workers who bring reliability, communication and respect to every visit.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container feature-row">
            <div>
                <p class="eyebrow">Our community support</p>
                <h2>Support that meets people where they are</h2>
                <p>Our Care helps participants, families and carers connect with dependable workers for everyday living, community participation and goal-focused NDIS support.</p>
            </div>
            <div class="feature-image">
                <img src="{{ asset('contact.jpg') }}" alt="Our Care community support">
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container community-vector-row">
            <div class="community-vector-copy">
                <h2>Our community support</h2>
                <p>"Slow down, take it easy."</p>
                <p>"My development with my disabilities..."</p>
            </div>

            <div class="vector-fade" aria-label="Our Care support illustration carousel">
                <img src="{{ asset('care-vector-01.png') }}" alt="Older couple walking together with support">
                <img src="{{ asset('care-vector-02.png') }}" alt="Two older adults using walking sticks">
                <img src="{{ asset('care-vector-03.png') }}" alt="Older adults using walkers">
                <img src="{{ asset('care-vector-04.png') }}" alt="Support worker assisting a wheelchair user">
                <img src="{{ asset('care-vector-05.png') }}" alt="Support worker helping an older participant with a walker">
                <img src="{{ asset('care-vector-06.png') }}" alt="Support worker sharing tea with an older participant">
            </div>
        </div>
    </section>

    <section class="update-band" id="ndis">
        <div class="container update-band__inner">
            <div>
                <h2>01 Jul 2026 imposing financial limits for categories including personal care, specialised nursing, and supported accommodation.</h2>
                <p>Keep care planning current with NDIS pricing and service updates.</p>
            </div>
            <a class="button button--light" href="{{ url('/services-v2') }}">Read More</a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <h2>Support That Helps You Live Life Your Way</h2>
                <p>Choose practical support from a team focused on reliability, respect and clear communication.</p>
            </div>

            <div class="preview-grid">
                @foreach(array_slice(config('ourcare_v2.services'), 0, 3) as $slug => $service)
                    <article class="service-preview">
                        <img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}">
                        <div class="service-preview__body">
                            <h3>{{ $service['label'] }}</h3>
                            <p>{{ $service['summary'] }}</p>
                            <a class="button" href="{{ route('services.detail.v2', $slug) }}">Learn More</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container action-stack">
            <article class="action-row">
                <div class="action-row__copy">
                    <h2>Hire a Worker</h2>
                    <p>Check the qualification and hire your community support. Create an account to begin intake and tell us what kind of support you need.</p>
                    <a class="button" href="{{ url('/client-register') }}">Create Your Account Now</a>
                </div>
                <div class="action-row__image">
                    <img src="{{ asset('hero.jpg') }}" alt="Client reviewing support options">
                </div>
            </article>

            <article class="action-row">
                <div class="action-row__copy">
                    <h2>Be a Core Worker</h2>
                    <p>We value your qualification. Explore our network of community advocating professional care. Be one of us.</p>
                    <a class="button" href="{{ url('/healthcare-register') }}">Create Your Account</a>
                </div>
                <div class="action-row__image">
                    <img src="{{ asset('ready.jpg') }}" alt="Support worker preparing to assist a client">
                </div>
            </article>
        </div>
    </section>

    <section class="section section--soft">
        <div class="container">
            <div class="section-heading">
                <h2>OUR CARE Support Services</h2>
                <p>At Our Care, we are committed to achieve your goal with our NDIS services:</p>
            </div>

            <div class="support-services-grid">
                @foreach(config('ourcare_v2.services') as $slug => $service)
                    <article class="support-service-card">
                        <img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}">
                        <div class="support-service-card__body">
                            <h3>{{ $service['label'] }}</h3>
                            <p>{{ $service['summary'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
