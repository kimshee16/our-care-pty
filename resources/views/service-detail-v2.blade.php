@extends('layouts.public-v2', [
    'activePage' => 'services',
    'activeService' => $slug,
    'services' => $services,
])

@section('title', $service['label'] . ' - Our Care')

@section('styles')
    .service-detail {
        padding: 62px var(--pad) 96px;
        background:
            linear-gradient(180deg, rgba(231, 244, 248, 0.62), #ffffff 42%),
            linear-gradient(90deg, rgba(127, 199, 177, 0.12), rgba(231, 119, 98, 0.07));
    }

    .service-detail__inner {
        display: grid;
        grid-template-columns: minmax(300px, 0.82fr) minmax(0, 1.18fr);
        gap: 42px;
        align-items: start;
    }

    .service-photo {
        position: sticky;
        top: 122px;
        overflow: hidden;
        min-height: 360px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .service-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-copy {
        padding: 44px 46px;
        border: 1px solid rgba(36, 124, 159, 0.18);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 16px 36px rgba(20, 60, 78, 0.08);
    }

    .service-copy h1 {
        margin: 0 0 54px;
        color: var(--brand);
        text-align: center;
        font-size: clamp(2rem, 4.2vw, 3.45rem);
        line-height: 1.25;
        letter-spacing: 0;
    }

    .service-copy h2 {
        margin: 30px 0 18px;
        color: #0e1a21;
        font-size: 24px;
        line-height: 1.3;
    }

    .service-copy p {
        margin-bottom: 22px;
        color: #24313a;
        font-size: 18px;
        line-height: 1.58;
    }

    .registration {
        margin-bottom: 26px;
        padding: 14px 16px;
        border-left: 4px solid var(--brand);
        color: #34444d;
        background: var(--brand-soft);
        font-size: 14px;
        font-weight: 800;
        line-height: 1.45;
    }

    .support-list {
        display: grid;
        gap: 18px;
        margin: 0 0 30px;
        padding: 0;
        list-style: none;
    }

    .support-list li {
        color: #24313a;
        font-size: 17px;
        line-height: 1.55;
    }

    .support-list strong {
        color: #0d151a;
    }

    .detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 36px;
    }

    .detail-actions .button {
        min-width: 210px;
    }

    @media (max-width: 980px) {
        .service-detail__inner {
            grid-template-columns: 1fr;
        }

        .service-photo {
            position: static;
        }

        .service-copy h1 {
            margin-bottom: 36px;
        }
    }

    @media (max-width: 640px) {
        .service-detail {
            padding-left: 20px;
            padding-right: 20px;
        }

        .service-copy {
            padding: 30px 22px;
        }

        .service-copy p,
        .support-list li {
            font-size: 16px;
        }

        .service-photo {
            min-height: 250px;
        }
    }
@endsection

@section('content')
    <section class="service-detail">
        <div class="container service-detail__inner">
            <div class="service-photo">
                <img src="{{ asset($service['image']) }}" alt="{{ $service['label'] }}">
            </div>

            <article class="service-copy">
                <h1>{{ $service['title'] }}</h1>

                @if(!empty($service['heading']))
                    <h2>{{ $service['heading'] }}</h2>
                @endif

                <div class="registration">{{ $service['registration'] }}</div>

                @foreach($service['intro'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                <h2>{{ $service['section_heading'] }}</h2>
                <p>{{ $service['section_intro'] }}</p>

                @if(!empty($service['items_heading']))
                    <h2>{{ $service['items_heading'] }}</h2>
                @endif

                @if(!empty($service['items_intro']))
                    <p>{{ $service['items_intro'] }}</p>
                @endif

                <ul class="support-list">
                    @foreach($service['items'] as $item)
                        <li><strong>{{ $item['title'] }}:</strong> {{ $item['text'] }}</li>
                    @endforeach
                </ul>

                <div class="detail-actions">
                    <a class="button" href="{{ url('/signup-option') }}">Book a Consultation</a>
                    <a class="button" href="{{ url('/services-v2') }}">View All Services</a>
                </div>
            </article>
        </div>
    </section>
@endsection
