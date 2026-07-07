<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <title>CISO 360 Survival Lifeline</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=3.5">
    <link rel="stylesheet" href="{{ asset('css/ciso-lifeline.css') }}?v=2.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="vciso-page">
        <nav class="vciso-nav" aria-label="CISO 360 navigation">
            <div class="container vciso-nav__inner">
                <a class="vciso-nav__brand" href="{{ route('welcome') }}" aria-label="SaudiCISO.net home">
                    <img src="{{ asset('Images/SaudiCISOLogo.png') }}" alt="SaudiCISO.net">
                </a>

                @auth
                    <div class="vciso-nav__actions">
                        @if (auth()->user()->role_id == 1)
                            <a href="{{ route('users.index') }}" class="btn btn--ghost vciso-nav__link">
                                Admin Portal
                            </a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="btn btn--ghost vciso-nav__link">
                                Update Profile
                            </a>
                        @endif

                        <form action="{{ route('login.destroy') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn--ghost vciso-nav__link">
                                Sign out
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </nav>

        <main class="vciso-main">
            <section class="vciso-hero" aria-labelledby="vciso-title">
                <div class="container vciso-hero__grid">
                    <div class="vciso-hero__content">
                        <p class="eyebrow">
                            <span class="eyebrow__dot" aria-hidden="true"></span>
                            SaudiCISO member hub
                        </p>
                        <h1 id="vciso-title">CISO 360 <span class="text-accent">Survival Lifeline</span></h1>
                        <p class="vciso-hero__lead">
                            Move quickly into the resources CISOs use most: practical toolkits, current threat context,
                            education, people intelligence, process guidance, and product insight.
                        </p>
                    </div>

                    <div class="vciso-hero__panel" aria-label="Platform focus">
                        <span>Executive-ready resources</span>
                        <strong>Six focused pathways for cyber leadership decisions.</strong>
                    </div>
                </div>
            </section>

            <section class="vciso-section" aria-labelledby="primary-modules-title">
                <div class="container">
                    <div class="vciso-section__head">
                        <p class="eyebrow">Primary modules</p>
                        <h2 id="primary-modules-title">Start with the workstream you need today.</h2>
                    </div>

                    <div class="vciso-card-grid vciso-card-grid--primary">
                        <a href="{{ route('ciso-toolkit.index') }}" class="vciso-card">
                            <span class="icon-box vciso-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M14.7 6.3a4 4 0 0 0-5 5L3 18v3h3l6.7-6.7a4 4 0 0 0 5-5l-2.4 2.4-2-2 2.4-2.4Z"></path>
                                </svg>
                            </span>
                            <span class="vciso-card__meta">Templates and aids</span>
                            <h3>CISO Toolkit</h3>
                            <p>Access practical checklists, governance assets, and reusable security leadership tools.</p>
                        </a>

                        <a href="{{ route('ciso-education.index') }}" class="vciso-card">
                            <span class="icon-box vciso-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M22 10 12 5 2 10l10 5 10-5Z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </span>
                            <span class="vciso-card__meta">Learning paths</span>
                            <h3>CISO Education</h3>
                            <p>Build applied leadership depth across certifications, frameworks, and cyber practice.</p>
                        </a>

                        <a href="{{ route('hot-topics.index') }}" class="vciso-card">
                            <span class="icon-box vciso-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 22c4-2 7-5 7-10a7 7 0 0 0-4-6c0 3-2 4-3 5 0-3-2-6-5-7 1 4-2 6-2 10a7 7 0 0 0 7 8Z"></path>
                                </svg>
                            </span>
                            <span class="vciso-card__meta">Current priorities</span>
                            <h3>Hot Topics for CISO</h3>
                            <p>Review timely cyber leadership issues, emerging risks, and decision-ready perspectives.</p>
                        </a>
                    </div>
                </div>
            </section>

            <section class="vciso-section vciso-section--compact" aria-labelledby="operating-areas-title">
                <div class="container">
                    <div class="vciso-section__head">
                        <p class="eyebrow">Operating areas</p>
                        <h2 id="operating-areas-title">Navigate the core of a resilient security office.</h2>
                    </div>

                    <div class="vciso-card-grid">
                        <a href="{{ route('people.index') }}" class="vciso-card vciso-card--compact">
                            <span class="icon-box vciso-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </span>
                            <h3>People</h3>
                            <p>Find Saudi cybersecurity talent and human-resource intelligence.</p>
                        </a>

                        <a href="{{ route('ciso-process.index') }}" class="vciso-card vciso-card--compact">
                            <span class="icon-box vciso-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1A2 2 0 1 1 4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3h.1a1.7 1.7 0 0 0 .9-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6.9h.1a2 2 0 1 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z"></path>
                                </svg>
                            </span>
                            <h3>Processes</h3>
                            <p>Work through governance, controls, evidence, and repeatable operating practices.</p>
                        </a>

                        <a href="{{ route('ciso-products.index') }}" class="vciso-card vciso-card--compact">
                            <span class="icon-box vciso-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                    <path d="M8 20h8"></path>
                                    <path d="M12 16v4"></path>
                                </svg>
                            </span>
                            <h3>Products</h3>
                            <p>Explore security product categories and vendor-neutral evaluation context.</p>
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Elfsight AI Chatbot | Saudi Ciso -->
    <script src="https://elfsightcdn.com/platform.js" async></script>
    <div class="elfsight-app-50a59065-4154-49f7-a375-961a269cf1c2" data-elfsight-app-lazy></div>
</body>

</html>
