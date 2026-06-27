
@extends('layouts.process')
@section('title', $process->title)
@section('content')
    @php
        $process_id = html_entity_decode($process->process_id);
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-two-column-layout>
            <x-slot:main>
                <img src="{{ $slideImage }}" alt="{{ $process->title }}" loading="lazy"
                    class="block w-full h-auto rounded-2xl" />
            </x-slot:main>

            <x-slot:sidebar>
                <x-resource-sidebar>
                    <x-iso-templates link="{{ route('process.resource.template', $process_id) }}" />
                    <x-iso-checklist link="{{ route('process.resource.checklist', $process_id) }}" />
                    <x-iso-video link="{{ route('process.resource.videos', $process_id) }}" />
                    <x-iso-glossary link="{{ route('process.resource.glossary', $process_id) }}" />
                </x-resource-sidebar>
            </x-slot:sidebar>
        </x-two-column-layout>
    </div>
@endsection

@section('additional_content')
    <div class="bg-white my-6 p-5 rounded-2xl">
        <header class="text-center bg-brand-950 font-bold  mb-3 p-3 rounded-md text-white">
            <h1>{{ $process->title }}</h1>
        </header>
        <div class="process-content">
            @php
                use Illuminate\Support\Facades\View;
            @endphp
            @if (View::exists("process/process/content/{$process->process_id}"))
                @include("process/process/content/{$process->process_id}")
            @endif
        </div>
    </div>
@endsection
