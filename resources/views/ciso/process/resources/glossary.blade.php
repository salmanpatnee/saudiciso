@extends('layouts.process')
@push('css')
    <style>
        #process_banner {
            min-height: 350px;
        }

        /* Framed process image (same style as the process detail page) */
        .kb-process-hero__frame {
            position: relative;
            padding: 14px;
            background: #ffffff;
            border: 1px solid #e6e9ef;
            border-radius: 36px;
            box-shadow: 0 24px 56px -20px rgba(0, 5, 60, .30), 0 0 0 1px rgba(201, 162, 39, .22);
            transition: box-shadow .25s ease;
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
@section('title', $processWithGlossary->title)
@section('content')
    @php
        $process_id = html_entity_decode($processWithGlossary->process_id);
    @endphp
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="kb-process-hero__frame">
            <img src="{{ $slideImage }}" alt="{{ $processWithGlossary->title }}" loading="lazy" />
        </div>
    </div>
@endsection

@section('additional_content')
    <div class="bg-white my-6 p-5 rounded-2xl">
        @if (session('error'))
            <x-alert-error>{{ session('error') }}</x-alert-error>
        @endif
        @if (session('success'))
            <x-alert-success>{{ session('success') }}</x-alert-success>
        @endif

        <header class="text-center bg-brand-950 font-bold mb-4 p-3 rounded-md text-white">
            <h1>Glossary for {{ $processWithGlossary->title }}</h1>
        </header>

        @if ($processWithGlossary->resources->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-2xl bg-success-500/10">
                    <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">No glossary available yet</h2>
                <p class="mt-1 text-sm text-gray-500">Glossary terms for this process will appear here once they are
                    added.</p>
            </div>
        @else
            @include('ciso/process/resources/resource-table', [
                'resources' => $processWithGlossary->resources,
            ])
        @endif
    </div>
@endsection
