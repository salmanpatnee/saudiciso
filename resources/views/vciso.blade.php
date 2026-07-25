<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <title>CISO 360 Survival Lifeline</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=5.0">
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
                        <h1 id="vciso-title">CISO All-in-One <span class="text-accent">Requirements are Here!</span></h1>
                    </div>

                    <div class="vciso-hero__panel" aria-label="Platform focus">
                        <span>Executive-ready resources</span>
                        <strong>Focused pathways for cyber leadership decisions.</strong>
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
                        <a href="{{ route('people.index') }}" class="vciso-card vciso-card--compact vciso-card--image">
                            <img src="{{ asset('Images/admin/People.JPG') }}" alt="People">
                        </a>

                        <a href="{{ route('ciso-process.index') }}" class="vciso-card vciso-card--compact vciso-card--image">
                            <img src="{{ asset('Images/admin/Process.JPG') }}" alt="Processes">
                        </a>

                        <a href="{{ route('ciso-products.index') }}" class="vciso-card vciso-card--compact vciso-card--image">
                            <img src="{{ asset('Images/admin/Products.JPG') }}" alt="Products">
                        </a>
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
                        <a href="{{ route('ciso-toolkit.index') }}" class="vciso-card vciso-card--image">
                            <img src="{{ asset('Images/admin/Toolkit.JPG') }}" alt="CISO Toolkit">
                        </a>

                        <a href="{{ route('ciso-education.index') }}" class="vciso-card vciso-card--image">
                            <img src="{{ asset('Images/admin/Education.JPG') }}" alt="CISO Education">
                        </a>

                        <a href="{{ route('hot-topics.index') }}" class="vciso-card vciso-card--image">
                            <img src="{{ asset('Images/admin/Hot-Topics.JPG') }}" alt="Hot Topics for CISO">
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
