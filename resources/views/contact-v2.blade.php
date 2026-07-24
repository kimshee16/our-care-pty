@extends('layouts.public-v2', ['activePage' => 'contact'])

@section('title', 'Contact Us - Our Care')

@section('styles')
    .contact-hero {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(340px, 1.1fr);
        gap: 48px;
        align-items: center;
    }

    .contact-hero h1 {
        margin-bottom: 18px;
        color: var(--brand);
        font-size: clamp(2.5rem, 5vw, 4.35rem);
        line-height: 1.08;
        letter-spacing: 0;
    }

    .contact-hero p {
        color: #4d5e68;
        font-size: 17px;
    }

    .contact-photo {
        overflow: hidden;
        min-height: 380px;
        border-radius: 8px;
        background: var(--brand-soft);
        box-shadow: var(--shadow);
    }

    .contact-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .contact-methods {
        display: grid;
        gap: 14px;
        margin-top: 30px;
    }

    .method {
        display: flex;
        gap: 14px;
        align-items: center;
        padding: 16px;
        border: 1px solid rgba(36, 124, 159, 0.16);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.82);
    }

    .method__icon {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        flex: 0 0 auto;
        border-radius: 50%;
        color: #ffffff;
        background: var(--brand);
    }

    .method strong {
        display: block;
        color: var(--brand-dark);
        font-size: 15px;
    }

    .method > span:last-child span {
        color: var(--muted);
        font-size: 14px;
    }

    .contact-card {
        padding: 34px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        box-shadow: var(--shadow);
    }

    .contact-card h2 {
        margin-bottom: 10px;
        color: var(--brand);
        font-size: 30px;
        line-height: 1.2;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-top: 24px;
    }

    .field {
        display: grid;
        gap: 8px;
    }

    .field--full {
        grid-column: 1 / -1;
    }

    label {
        color: var(--brand-dark);
        font-size: 13px;
        font-weight: 800;
    }

    input,
    select,
    textarea {
        width: 100%;
        min-height: 46px;
        padding: 12px 13px;
        border: 1px solid var(--line);
        border-radius: 6px;
        color: var(--ink);
        background: #fbfdfe;
        font: inherit;
    }

    textarea {
        min-height: 130px;
        resize: vertical;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: 2px solid rgba(36, 124, 159, 0.22);
        border-color: var(--brand);
        background: #ffffff;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .info-card {
        padding: 26px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: linear-gradient(180deg, #ffffff, var(--soft));
    }

    .info-card h3 {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 22px;
    }

    @media (max-width: 980px) {
        .contact-hero,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .contact-card {
            padding: 24px;
        }
    }
@endsection

@section('content')
    <section class="section section--soft">
        <div class="container contact-hero">
            <div>
                <p class="eyebrow">Contact Our Care</p>
                <h1>Let’s Talk About the Support You Need</h1>
                <p>Reach out for intake questions, service enquiries, worker onboarding, or help choosing the right Our Care pathway.</p>

                <div class="contact-methods">
                    <a class="method" href="tel:0425795830">
                        <span class="method__icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M6.6 4.8 8.7 3c.5-.4 1.2-.4 1.6.1l2 2.8c.3.5.3 1.1-.1 1.5l-1.1 1.2c.9 1.7 2.5 3.3 4.3 4.2l1.2-1c.5-.4 1.1-.4 1.6-.1l2.7 2c.5.4.6 1.1.2 1.6l-1.8 2.2c-.5.6-1.3.9-2.1.7C10.6 16.8 6.1 12.3 4.8 5.9c-.2-.7.1-1.5.8-2.1Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span><strong>Call Us</strong><span>0425 795 830</span></span>
                    </a>
                    <a class="method" href="mailto:admin@ourcarepty.com">
                        <span class="method__icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6.5h16v11H4v-11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="m4.5 7 7.5 6 7.5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span><strong>Email Us</strong><span>admin@ourcarepty.com</span></span>
                    </a>
                </div>
            </div>

            <div class="contact-card">
                <h2>Send an Enquiry</h2>
                <p>Use this form as a presentation-ready contact experience. It can be wired to email or CRM handling later.</p>
                <form class="form-grid" onsubmit="event.preventDefault(); alert('Thank you. Our Care will contact you soon.');">
                    <div class="field">
                        <label for="name">Full Name</label>
                        <input id="name" name="name" type="text" placeholder="Your name">
                    </div>
                    <div class="field">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="tel" placeholder="Best contact number">
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="you@example.com">
                    </div>
                    <div class="field">
                        <label for="interest">I’m Interested In</label>
                        <select id="interest" name="interest">
                            <option>Client intake</option>
                            <option>Worker onboarding</option>
                            <option>Personal care support</option>
                            <option>Community participation</option>
                            <option>Transport services</option>
                            <option>General enquiry</option>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Tell us what support or information you need."></textarea>
                    </div>
                    <div class="field field--full">
                        <button class="button" type="submit">Send Enquiry</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container info-grid">
            <article class="info-card">
                <h3>For Clients</h3>
                <p>Tell us about your routines, support goals, preferred services, and availability so we can guide intake clearly.</p>
            </article>
            <article class="info-card">
                <h3>For Workers</h3>
                <p>Ask about onboarding, required profile details, qualifications, service areas, and how to join the Our Care network.</p>
            </article>
            <article class="info-card">
                <h3>Response Time</h3>
                <p>We aim to respond promptly during business hours and help direct your enquiry to the right next step.</p>
            </article>
        </div>
    </section>
@endsection
