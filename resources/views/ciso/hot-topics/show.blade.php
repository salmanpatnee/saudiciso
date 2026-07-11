@extends('layouts.ciso-full')
@section('title', html_entity_decode($hotTopic->title))

@push('css')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

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

        .kb {
            width: 100%;
            margin: 0 0 1rem;
            padding: 0 1rem;
            animation: kb-fade .5s ease both;
        }

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
            text-decoration: none;
        }

        .kb__pill i {
            color: var(--gold);
            font-size: 1rem;
        }

        .kb-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 18px 40px -20px rgba(0, 5, 60, .28);
            animation: kb-rise .55s ease both;
            margin-bottom: 1.5rem;
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

        .kb-card__content {
            padding: 1.5rem 1.1rem;
            color: var(--ink);
            line-height: 1.7;
        }

        /* Body is admin-authored rich text (CKEditor) that often carries roomy
           Tailwind utility classes (p-3, my-6, px-3 py-3, etc). Tighten it here so
           pasted content matches the rest of the page regardless of what classes
           the editor happened to save. */
        .kb-card__content :where(h1, h2, h3, h4) {
            padding: .6rem .7rem;
            margin: 1.5rem 0 .75rem;
        }

        .kb-card__content :where(h1, h2, h3, h4):first-child {
            margin-top: 0;
        }

        .kb-card__content p {
            margin: 0 0 .85rem;
        }

        .kb-card__content table th,
        .kb-card__content table td {
            padding: .55rem .55rem;
        }

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

        .kb-row__ext {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .06em;
            color: var(--muted);
            background: #F1F3F7;
            border: 1px solid var(--border);
            padding: .15rem .45rem;
            border-radius: 5px;
        }

        .kb-row__right i {
            font-size: 1.2rem;
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
            transform: translateY(1px);
        }

        .kb-empty {
            text-align: center;
            color: var(--muted);
            padding: 2.5rem 1.5rem;
            margin: 0;
        }

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
            .kb {
                padding: 0 0.25rem;
            }

            .kb__head {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 1.25rem;
            }

            .kb-card__bar {
                padding: 0.85rem 1rem;
            }

            .kb-row {
                padding: 0.85rem 1rem;
            }

            .kb-row__ext {
                display: none;
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
@endpush

@section('content')
    <div class="kb">
        <div class="kb__head">
            <div>
                <p class="kb__eyebrow">{{ $hotTopic->category?->value ?? 'Hot Topics' }}</p>
                <h1 class="kb__title">{!! html_entity_decode($hotTopic->title) !!}</h1>
                <span class="kb__rule"></span>
            </div>
            <a class="kb__pill" href="{{ route('hot-topics.index') }}">
                <i class='bx bx-arrow-back'></i> Back to Hot Topics
            </a>
        </div>

        @if ($hotTopic->body)
            <section class="kb-card">
                <div class="kb-card__content">
                    {!! $hotTopic->body !!}
                </div>
            </section>
        @endif

        <section class="kb-card">
            <header class="kb-card__bar">
                <span class="kb-card__icon"><i class='bx bxs-folder'></i></span>
                <h2 class="kb-card__title">Downloadable Resources</h2>
            </header>
            <div class="kb-card__body">
                @forelse ($hotTopic->resources as $resource)
                    <a class="kb-row" href="{{ asset('storage/' . $resource->file_path) }}"
                        download="{{ $resource->file_name }}" style="animation-delay: {{ $loop->index * 60 }}ms">
                        <span class="kb-row__left">
                            <span class="kb-row__dot"></span>
                            <span class="kb-row__title">{{ $resource->file_name }}</span>
                        </span>
                        <span class="kb-row__right">
                            <span class="kb-row__ext">{{ strtoupper(pathinfo($resource->file_name, PATHINFO_EXTENSION)) }}</span>
                            <i class='bx bx-download'></i>
                        </span>
                    </a>
                @empty
                    <p class="kb-empty">No resources attached to this hot topic yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
