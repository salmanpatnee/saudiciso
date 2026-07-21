@push('css')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=3.5">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        .login-page {
            --login-navy: #050739;
            --login-copy: #4e5870;
            min-height: 100vh;
            background:
                radial-gradient(900px 520px at 82% -10%, rgba(240, 207, 58, .18), transparent 58%),
                radial-gradient(760px 420px at 4% 6%, rgba(5, 7, 57, .08), transparent 56%),
                linear-gradient(180deg, #f8f9ff 0%, #eef1fb 48%, #f8f9ff 100%);
            color: var(--login-copy);
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .login-shell {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
            min-height: 100vh;
        }

        .login-brand {
            position: relative;
            display: flex;
            min-height: 100%;
            overflow: hidden;
            background:
                radial-gradient(760px 420px at 88% 18%, rgba(240, 207, 58, .18), transparent 62%),
                linear-gradient(160deg, #00053c 0%, #080b4a 100%);
            color: #ffffff;
        }

        .login-brand::before,
        .login-brand::after {
            position: absolute;
            z-index: 0;
            content: "";
            border: 1px solid rgba(240, 207, 58, .18);
            border-radius: 999px;
            opacity: .62;
        }

        .login-brand::before {
            top: 9%;
            right: -190px;
            width: 420px;
            height: 420px;
        }

        .login-brand::after {
            bottom: -150px;
            left: -130px;
            width: 360px;
            height: 360px;
        }

        .login-brand__grid {
            position: absolute;
            z-index: 0;
            width: min(44vw, 420px);
            opacity: .32;
        }

        .login-brand__grid--top {
            top: 0;
            right: 0;
        }

        .login-brand__grid--bottom {
            bottom: 0;
            left: 0;
            transform: rotate(180deg);
        }

        .login-brand__content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: min(100% - 64px, 560px);
            min-height: 100vh;
            margin: 0 auto;
            padding: 48px 0;
        }

        .login-brand__logo {
            width: min(52vw, 260px);
        }

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .login-card {
            position: relative;
            width: min(100%, 520px);
            overflow: hidden;
            padding: clamp(30px, 4vw, 46px);
            border: 1px solid rgba(5, 7, 57, .1);
            border-radius: 28px;
            background:
                radial-gradient(420px 240px at 95% 0%, rgba(240, 207, 58, .16), transparent 64%),
                linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(255, 255, 255, .9));
            box-shadow: 0 30px 80px rgba(5, 7, 57, .12);
        }

        .login-card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            content: "";
            background: linear-gradient(90deg, var(--gold), var(--gold-bright), var(--gold));
        }

        .login-card__mobile-logo {
            display: none;
            width: 142px;
            margin: 0 auto 28px;
        }

        .login-card__header {
            margin: 0 0 28px;
        }

        .login-card__header h2 {
            margin: 0;
            color: var(--login-navy);
            font-size: clamp(2.1rem, 4vw, 2.65rem);
            font-weight: 900;
            line-height: 1.04;
            letter-spacing: 0;
        }

        .login-card__header p {
            margin: 12px 0 0;
            color: var(--login-copy);
            font-size: 1rem;
            line-height: 1.75;
        }

        .login-form {
            display: grid;
            gap: 20px;
        }

        .login-form label {
            margin-bottom: 8px;
            color: #283252;
        }

        .login-form label span {
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .login-form input {
            height: 54px;
            border-color: rgba(5, 7, 57, .13);
            border-radius: 999px;
            background: rgba(255, 255, 255, .78);
            color: var(--login-navy);
            font-size: .95rem;
            box-shadow: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .login-form input:focus {
            border-color: rgba(202, 164, 27, .58);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(240, 207, 58, .16);
            outline: none;
        }

        .login-form__password-toggle {
            right: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            transition: background .2s ease;
        }

        .login-form__password-toggle:hover {
            background: rgba(5, 7, 57, .06);
        }

        .login-form__submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 56px;
            padding: 0 22px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--gold-bright), #fff1a1);
            box-shadow: 0 16px 34px rgba(202, 164, 27, .28);
            color: #04130e;
            font-size: .95rem;
            font-weight: 900;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .login-form__submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 42px rgba(202, 164, 27, .34);
        }

        .login-form .text-error-500 {
            color: #b42318;
            font-weight: 700;
        }

        @media (max-width: 1100px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .login-brand {
                display: none;
            }

            .login-panel {
                min-height: 100vh;
                padding: 32px 22px;
            }

            .login-card__mobile-logo {
                display: block;
            }
        }

        @media (max-width: 520px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 22px;
            }

            .login-card__header {
                margin: 26px 0 24px;
            }
        }
    </style>
@endpush

@include('partials.header')

<main class="login-page">
    <div class="login-shell">
        <section class="login-brand" aria-label="SaudiCISO platform access">
            <img class="login-brand__grid login-brand__grid--top" src="{{ asset('Images/shape/grid-01.svg') }}" alt="">
            <img class="login-brand__grid login-brand__grid--bottom" src="{{ asset('Images/shape/grid-01.svg') }}" alt="">

            <div class="login-brand__content">
                <a href="{{ route('welcome') }}" aria-label="SaudiCISO.net home">
                    <img class="login-brand__logo" src="{{ asset('Images/SaudiCISOLogo-trans.png') }}" alt="SaudiCISO.net">
                </a>
            </div>
        </section>

        <section class="login-panel" aria-labelledby="login-title">
            <div class="login-card">
                <a href="{{ route('welcome') }}" aria-label="SaudiCISO.net home">
                    <img class="login-card__mobile-logo" src="{{ asset('Images/SaudiCISOLogo.png') }}" alt="SaudiCISO.net">
                </a>

                <div class="login-card__header">
                    <h2 id="login-title">Access <span class="text-accent">Saudi CISO</span></h2>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="login-form"
                    x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf

                    <div>
                        <x-form.label for="username" label="Username" required="true" />
                        <x-form.input name="username" required="true" autocomplete="username" />
                        <x-form.error name="username" />
                    </div>

                    <div>
                        <x-form.label for="password" label="Password" required="true" />

                        <div x-data="{ showPassword: false }" class="relative">
                            <x-form.input name="password" x-bind:type="showPassword ? 'text' : 'password'" autocomplete="current-password" />

                            <button type="button" @click="showPassword = !showPassword"
                                class="login-form__password-toggle absolute z-30 text-gray-500 -translate-y-1/2 top-1/2"
                                aria-label="Toggle password visibility">
                                <svg x-show="!showPassword" class="fill-current" width="20"
                                    height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z"
                                        fill="#98A2B3" />
                                </svg>
                                <svg x-show="showPassword" class="fill-current" width="20"
                                    height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                        fill="#98A2B3" />
                                </svg>
                            </button>

                            <x-form.error name="password" />
                        </div>
                    </div>

                    <button type="submit" class="login-form__submit" :disabled="submitting"
                        x-text="submitting ? 'Signing in…' : 'Enter Platform'">
                        Enter Platform
                    </button>
                </form>

            </div>
        </section>
    </div>
</main>

@include('partials.footer')
