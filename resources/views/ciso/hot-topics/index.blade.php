<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Hot Topics</title>
    <link rel="stylesheet" href="{{ asset('css/ciso-lifeline.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --navy: #00053C;
            --navy-2: #0A1559;
            --gold: #C9A227;
            --page: #F4F6F8;
            --card: #FFFFFF;
            --border: #E6E9EF;
            --divider: #EEF1F5;
            --ink: #1F2430;
            --muted: #6B7280;
        }

        body {
            background: var(--page);
        }

        body::before {
            display: none;
        }

        .kb {
            width: 100%;
            margin: 2.5rem 0 4rem;
            padding: 0 2.5rem;
            animation: kb-fade .5s ease both;
        }

        /* ---- Heading row ---- */
        .kb__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .kb__eyebrow {
            margin: 0 0 .35rem;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--gold);
        }

        .kb__title {
            margin: 0;
            font-family: "Playfair Display", Georgia, serif;
            font-weight: 700;
            font-size: clamp(1.7rem, 3.2vw, 2.4rem);
            line-height: 1.1;
            color: var(--navy);
        }

        .kb__rule {
            display: block;
            width: 64px;
            height: 3px;
            margin-top: .7rem;
            background: var(--gold);
            border-radius: 2px;
        }

        .kb__pill {
            flex: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .5rem .95rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--navy);
            box-shadow: 0 1px 2px rgba(0, 5, 60, .04);
        }

        .kb__pill i {
            color: var(--gold);
            font-size: 1rem;
        }

        /* ---- Card ---- */
        .kb-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 18px 40px -20px rgba(0, 5, 60, .28);
            animation: kb-rise .55s ease both;
        }

        .kb-card__bar {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1rem 1.5rem;
            background: linear-gradient(90deg, var(--navy), var(--navy-2));
            color: #fff;
        }

        .kb-card__bar::after {
            content: "";
            position: absolute;
            top: -60%;
            right: -40px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle at center, rgba(255, 255, 255, .08), transparent 70%);
            pointer-events: none;
        }

        .kb-card__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(201, 162, 39, .18);
            color: var(--gold);
            font-size: 1.25rem;
        }

        .kb-card__title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .kb-card__body {
            display: flex;
            flex-direction: column;
        }

        /* ---- Rows ---- */
        .kb-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .95rem 1.5rem;
            border-bottom: 1px solid var(--divider);
            color: var(--navy);
            transition: background .2s ease, transform .2s ease;
            animation: kb-up .45s ease both;
        }

        .kb-row:last-child {
            border-bottom: 0;
        }

        .kb-row__left {
            display: flex;
            align-items: center;
            gap: .8rem;
            min-width: 0;
        }

        .kb-row__dot {
            flex: none;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--gold);
            transition: transform .2s ease;
        }

        .kb-row__title {
            font-weight: 600;
            font-size: .96rem;
            color: var(--navy);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: color .2s ease;
        }

        .kb-row__right {
            flex: none;
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            color: var(--muted);
        }

        .kb-row__right i {
            font-size: 1.3rem;
            color: var(--navy);
            transition: color .2s ease, transform .2s ease;
        }

        .kb-row:hover {
            background: #F8FAFC;
            transform: translateX(4px);
        }

        .kb-row:hover .kb-row__title {
            color: var(--gold);
        }

        .kb-row:hover .kb-row__dot {
            transform: scale(1.4);
        }

        .kb-row:hover .kb-row__right i {
            color: var(--gold);
            transform: translateX(2px);
        }

        .kb-empty {
            text-align: center;
            color: var(--muted);
            padding: 2.5rem 1.5rem;
            margin: 0;
        }

        /* ---- Motion ---- */
        @keyframes kb-fade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes kb-rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes kb-up {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .kb__head {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .kb, .kb-card, .kb-row {
                animation: none;
            }

            .kb-row:hover {
                transform: none;
            }
        }
    </style>

</head>

<body>
    <header class="header">
        <nav class="nav">
            <div class="nav-left">
                <a href="{{ route('vciso') }}">
                    <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
                        <span class="flex items-center space-x-2">
                            <img src="/Images/SaudiCISOLogo.png" alt="Logo" style="height: 80px; width: auto;">
                        </span>
                    </span>
                </a>
            </div>

            @auth
                <div class="nav-right">
                    <a href="{{ route('vciso') }}" class="admin-portal-btn" title="Back">
                        <i class='bx bx-arrow-back'></i>
                        <span>Back</span>
                    </a>
                    @if (auth()->user()->role_id == 1)
                        <a href="{{ route('users.index') }}" class="admin-portal-btn" title="Admin Portal">
                            <i class='bx bx-cog'></i>
                            <span>Admin Portal</span>
                        </a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="admin-portal-btn" title="Update Profile">
                            <i class='bx bx-cog'></i>
                            <span>Update Profile</span>
                        </a>
                    @endif

                    <form id="logout-form" action="{{ route('login.destroy') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn" title="Logout">
                            <i class='bx bx-log-out'></i>
                            <span>Sign out</span>
                        </button>
                    </form>
                </div>
            @endauth
        </nav>
    </header>

    <main class="main">
        <div class="kb">
            <div class="kb__head">
                <div>
                    <p class="kb__eyebrow">Hot Topics</p>
                    <h1 class="kb__title">Browse Hot Topics</h1>
                    <span class="kb__rule"></span>
                </div>
                <span class="kb__pill">
                    <i class='bx bx-grid-alt'></i> {{ $hotTopics->count() }} {{ $hotTopics->count() === 1 ? 'Topic' : 'Topics' }}
                </span>
            </div>

            <section class="kb-card">
                <header class="kb-card__bar">
                    <span class="kb-card__icon"><i class='bx bxs-news'></i></span>
                    <h2 class="kb-card__title">Hot Topics</h2>
                </header>
                <div class="kb-card__body">
                    @forelse ($hotTopics as $item)
                        <a class="kb-row" href="{{ route('hot-topics.show', $item) }}"
                            style="animation-delay: {{ $loop->index * 60 }}ms">
                            <span class="kb-row__left">
                                <span class="kb-row__dot"></span>
                                <span class="kb-row__title">{{ $item->title }}</span>
                            </span>
                            <span class="kb-row__right">
                                <i class='bx bx-right-arrow-alt'></i>
                            </span>
                        </a>
                    @empty
                        <p class="kb-empty">No hot topics available yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
    <!-- Elfsight AI Chatbot | Saudi Ciso -->
    <script src="https://elfsightcdn.com/platform.js" async></script>
    <div class="elfsight-app-50a59065-4154-49f7-a375-961a269cf1c2" data-elfsight-app-lazy></div>
</body>

</html>
