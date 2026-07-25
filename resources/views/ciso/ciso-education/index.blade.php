@extends('layouts.ciso-full')
@section('title', 'CISO Education')
@section('title_ar', '')

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

        .kb__lead {
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.6;
            max-width: 62ch;
            margin: -.75rem 0 1.75rem;
        }

        /* ---- Education grid ---- */
        .kb-products__grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .kb-product {
            position: relative;
            display: block;
            border-radius: 24px;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: 0 18px 40px -20px rgba(0, 5, 60, .28), inset 0 1px 0 rgba(255, 255, 255, .4);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            animation: kb-rise .5s ease both;
        }

        .kb-product:hover {
            transform: translateY(-7px);
            border-color: rgba(201, 162, 39, .45);
            box-shadow: 0 24px 48px -18px rgba(0, 5, 60, .35), 0 0 0 1px rgba(201, 162, 39, .25);
        }

        .kb-product__frame {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
        }

        .kb-product__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .5s ease;
        }

        .kb-product:hover .kb-product__img {
            transform: scale(1.06);
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

        @media (max-width: 900px) {
            .kb-products__grid {
                grid-template-columns: repeat(2, 1fr);
            }
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

            .kb-products__grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .kb, .kb-product {
                animation: none;
            }

            .kb-product:hover {
                transform: none;
            }

            .kb-product:hover .kb-product__img {
                transform: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="kb">
        <div class="kb__head">
            <div>
                <p class="kb__eyebrow">CISO Education</p>
                <h1 class="kb__title">Education &amp; Training</h1>
                <span class="kb__rule"></span>
            </div>
        </div>

        <p class="kb__lead">Enhance your knowledge and skills in information security leadership. Explore comprehensive educational resources, training materials, and best practices for CISO excellence.</p>

        <div class="kb-products__grid">
            @foreach ($data as $index => $item)
                <a href="{{ route('ciso-education.show', $item) }}" class="kb-product" style="animation-delay: {{ $index * 60 }}ms">
                    @if ($item->featured_image_path)
                        <div class="kb-product__frame">
                            <img src="{{ asset('storage/' . $item->featured_image_path) }}" alt="{{ $item->title }}" loading="lazy" class="kb-product__img">
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endsection
