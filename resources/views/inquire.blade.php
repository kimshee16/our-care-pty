@extends('layouts.public-v2', ['activePage' => 'inquire'])

@section('title', 'Inquire - Our Care')

@section('styles')
    .inquiry-panel {
        max-width: 680px;
        margin: 0 auto;
        padding: clamp(20px, 5vw, 44px);
        border: 1px solid var(--line);
        border-radius: 16px;
        background: #fff;
    }
    .inquiry-panel h1 { margin: 0; color: var(--ink); }
    .inquiry-form { display: grid; gap: 16px; margin-top: 24px; }
    .inquiry-field { display: grid; gap: 6px; }
    .inquiry-field label { font-weight: 700; }
    .inquiry-field input, .inquiry-field textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #827689;
        border-radius: 8px;
        color: var(--ink);
        font: inherit;
        font-size: 1rem;
    }
    .inquiry-field textarea { resize: vertical; }
    .inquiry-field [aria-invalid="true"] { border-color: #a02236; }
    .inquiry-error { margin: 0; color: #a02236; }
    .inquiry-form .button { justify-self: start; background: var(--brand); font: inherit; font-weight: 700; }
@endsection

@section('content')
    @php
        $oldText = fn (string $field) => is_string(old($field)) ? old($field) : '';
    @endphp
    <section class="section section--soft" aria-labelledby="inquiry-heading">
        <div class="inquiry-panel">
            <h1 id="inquiry-heading">Inquire about Our Care</h1>

            @if (session('inquiry_status'))
                <p role="status">{{ session('inquiry_status') }}</p>
            @else
                <p>Interested in our support services? Tell us how we can help.</p>

                <form class="inquiry-form" method="post" action="{{ route('inquiry.store') }}">
                    @csrf

                    <div class="inquiry-field">
                        <label for="inquiry-name">Full name</label>
                        <input id="inquiry-name" name="name" autocomplete="name" value="{{ $oldText('name') }}" maxlength="120" required
                            @error('name') aria-invalid="true" aria-describedby="inquiry-name-error" @enderror>
                        @error('name') <p class="inquiry-error" id="inquiry-name-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="inquiry-field">
                        <label for="inquiry-email">Email address</label>
                        <input id="inquiry-email" name="email" type="email" autocomplete="email" value="{{ $oldText('email') }}" maxlength="254" required
                            @error('email') aria-invalid="true" aria-describedby="inquiry-email-error" @enderror>
                        @error('email') <p class="inquiry-error" id="inquiry-email-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="inquiry-field">
                        <label for="inquiry-phone">Phone number (optional)</label>
                        <input id="inquiry-phone" name="phone" type="tel" autocomplete="tel" value="{{ $oldText('phone') }}" maxlength="40"
                            @error('phone') aria-invalid="true" aria-describedby="inquiry-phone-error" @enderror>
                        @error('phone') <p class="inquiry-error" id="inquiry-phone-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="inquiry-field">
                        <label for="inquiry-message">How can we help?</label>
                        <textarea id="inquiry-message" name="message" rows="5" maxlength="3000" required
                            @error('message') aria-invalid="true" aria-describedby="inquiry-message-error" @enderror>{{ $oldText('message') }}</textarea>
                        @error('message') <p class="inquiry-error" id="inquiry-message-error">{{ $message }}</p> @enderror
                    </div>

                    <button class="button" type="submit">Send inquiry</button>
                </form>
            @endif
        </div>
    </section>
@endsection
