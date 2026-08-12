@extends('layouts.public-v2', ['activePage' => 'services'])

@section('title', 'Our Care Services')

@section('styles')
    .services-hero { padding: clamp(58px, 8vw, 104px) var(--pad); background: linear-gradient(100deg, #e6badf 0%, #f6c7d7 52%, #ffd9c7 100%); }
    .services-hero__grid { display: grid; grid-template-columns: minmax(0, .88fr) minmax(320px, 1.12fr); gap: clamp(28px, 6vw, 72px); align-items: center; }
    .services-hero h1 { margin: 0 0 16px; color: var(--ink); font-size: clamp(2.4rem, 5vw, 4.4rem); line-height: 1.04; }
    .services-hero p { max-width: 590px; color: #443454; font-size: 16px; }
    .services-hero__image { overflow: hidden; min-height: 430px; border-radius: 8px; box-shadow: var(--shadow); }
    .services-hero__image img, .service-card img, .ndis-image img { width: 100%; height: 100%; object-fit: cover; }
    .service-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; }
    .service-card { overflow: hidden; border-radius: 8px; background: #fff; box-shadow: var(--shadow); }
    .service-card__image { height: 190px; }
    .service-card__body { padding: 24px; }
    .service-card h3 { margin: 0 0 10px; color: var(--ink); font-size: 21px; }
    .service-card p { min-height: 76px; margin: 0 0 18px; font-size: 14px; }
    .ndis-band { display: grid; grid-template-columns: minmax(0, 1fr) minmax(300px, .86fr); gap: 38px; align-items: center; }
    .ndis-band h2 { margin: 0 0 16px; color: var(--ink); font-size: clamp(1.9rem, 3vw, 2.6rem); }
    .ndis-image { overflow: hidden; min-height: 320px; border-radius: 8px; box-shadow: var(--shadow); }
    .pathway-row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 28px; }
    .pathway-pill { padding: 20px; border-radius: 8px; background: #fff; box-shadow: 0 12px 26px rgba(45,18,75,.08); }
    .pathway-pill strong { display: block; margin-bottom: 8px; color: var(--ink); }
    .pathway-pill span { color: var(--muted); font-size: 13px; line-height: 1.5; }
    @media (max-width: 980px) { .services-hero__grid, .ndis-band, .service-grid, .pathway-row { grid-template-columns: 1fr; } .services-hero__image { min-height: 320px; } }
@endsection

@section('content')
    @php($services = config('ourcare_v2.services'))

    <section class="services-hero">
        <div class="container services-hero__grid">
            <div>
                <p class="eyebrow">Our services</p>
                <h1>NDIS support for daily life, community, and confidence.</h1>
                <p>Choose practical services delivered with clear communication, respectful workers, and planning that keeps your goals in view.</p>
                <a class="button" href="{{ url('/intake-v2') }}">Book a consultation</a>
            </div>
            <div class="services-hero__image"><img src="{{ asset('hero.jpg') }}" alt="Support worker assisting a participant"></div>
        </div>
    </section>

    <section class="section">
        <div class="container service-grid">
            @foreach($services as $slug => $service)
                <article class="service-card">
                    <div class="service-card__image"><img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}"></div>
                    <div class="service-card__body">
                        <h3>{{ $service['label'] }}</h3>
                        <p>{{ $service['summary'] }}</p>
                        <a class="button" href="{{ url('/services/' . $slug) }}">Learn more</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section section--soft" id="ndis">
        <div class="container ndis-band">
            <div>
                <p class="eyebrow">NDIS updates</p>
                <h2>Keep support planning current.</h2>
                <p>NDIS pricing, categories, and plan usage can change. Our Care helps participants and families review service needs, prepare intake information, and keep support practical.</p>
                <div class="pathway-row">
                    <div class="pathway-pill"><strong>Participants</strong><span>Choose services that fit daily goals and wellbeing.</span></div>
                    <div class="pathway-pill"><strong>Families</strong><span>Coordinate providers and understand next steps.</span></div>
                    <div class="pathway-pill"><strong>Workers</strong><span>Stay aligned with onboarding and service expectations.</span></div>
                </div>
            </div>
            <div class="ndis-image"><img src="{{ asset('ready.jpg') }}" alt="Care worker preparing support information"></div>
        </div>
    </section>
@endsection
