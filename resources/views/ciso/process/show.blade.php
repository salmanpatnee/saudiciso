
@extends('layouts.process')
@section('title', $process->title)

@push('css')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/product-detail.css') }}" rel="stylesheet">

    <style>
        :root {
            --kb-navy: #00053C;
            --kb-navy-2: #0A1559;
            --kb-gold: #C9A227;
            --kb-card: #FFFFFF;
            --kb-border: #E6E9EF;
        }

        /* Title header (replaces the old bg-brand-950 block) */
        .kb-process-detail__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
            animation: kb-fade .5s ease both;
        }
        .kb-process-detail__eyebrow {
            display: flex;
            align-items: center;
            gap: .4rem;
            margin: 0 0 .35rem;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--kb-gold);
        }
        .kb-process-detail__title {
            margin: 0;
            font-family: "Playfair Display", Georgia, serif;
            font-weight: 700;
            font-size: clamp(1.5rem, 3vw, 2.1rem);
            line-height: 1.2;
            color: var(--kb-navy);
        }
        .kb-process-detail__rule {
            display: block;
            width: 64px;
            height: 3px;
            margin-top: .7rem;
            background: var(--kb-gold);
            border-radius: 2px;
        }
        /* Framed image card for the hero screenshot */
        .kb-process-hero__frame {
            position: relative;
            padding: 14px;
            background: var(--kb-card);
            border: 1px solid var(--kb-border);
            border-radius: 36px;
            box-shadow: 0 24px 56px -20px rgba(0, 5, 60, .30), 0 0 0 1px rgba(201, 162, 39, .22);
            transition: box-shadow .25s ease;
            animation: kb-fade .5s ease both;
        }
        .kb-process-hero__frame:hover {
            box-shadow: 0 30px 64px -18px rgba(0, 5, 60, .36), 0 0 0 1px rgba(201, 162, 39, .35);
        }
        .kb-process-hero__frame img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 22px;
        }

        /* Resource sidebar: heading font only, card colors left as-is */
        .kb-process-sidebar { animation: kb-fade .5s ease .1s both; }
        .kb-process-sidebar h2 { font-family: "Playfair Display", Georgia, serif; color: var(--kb-navy); }

        @media (prefers-reduced-motion: reduce) {
            .kb-process-detail__head,
            .kb-process-hero__frame,
            .kb-process-sidebar { animation: none; }
        }

        /* #process_banner's min-h-screen + items-center (from layouts.process) leaves huge
           empty space above/below the stacked single-column content on small screens. */
        @media (max-width: 1100px) {
            #process_banner {
                min-height: 0;
                align-items: stretch;
            }
        }

        @media (max-width: 640px) {
            .kb-process-hero__frame {
                padding: 8px;
                border-radius: 22px;
            }

            .kb-process-hero__frame img {
                border-radius: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-two-column-layout>
            <x-slot:main>
                <div class="kb-process-hero__frame">
                    <img src="{{ $slideImage }}" alt="{{ $process->title }}" loading="lazy" />
                </div>
            </x-slot:main>

            <x-slot:sidebar>
                <div class="kb-process-sidebar">
                    <x-resource-sidebar>
                        <x-iso-templates link="{{ route('process.resource.template', $process->slug) }}" />
                        <x-iso-checklist link="{{ route('process.resource.checklist', $process->slug) }}" />
                        <x-iso-glossary link="{{ route('process.resource.glossary', $process->slug) }}" />
                    </x-resource-sidebar>
                </div>
            </x-slot:sidebar>
        </x-two-column-layout>
    </div>
@endsection

@section('additional_content')
    <div class="bg-white my-6 p-5 rounded-2xl">
        <div class="kb-process-detail__head">
            <div>
                <p class="kb-process-detail__eyebrow"><i class='bx bx-sitemap'></i> GRC Process</p>
                <h1 class="kb-process-detail__title">{{ $process->title }}</h1>
                <span class="kb-process-detail__rule"></span>
            </div>
        </div>
        <div class="process-content kb-product-detail">
            @php
                use Illuminate\Support\Facades\View;
            @endphp
            @if (View::exists("process/process/content/{$process->process_id}"))
                @include("process/process/content/{$process->process_id}")
            @endif
        </div>
    </div>
@endsection
