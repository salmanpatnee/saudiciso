@extends('layouts.process')
@push('css')
    <style>
        #process_banner {
            display: none;
        }
    </style>
@endpush
@section('title', $processWithChecklist->title)
@section('content')
    @php
        $process_id = html_entity_decode($processWithChecklist->process_id);
    @endphp
@endsection

@section('additional_content')
    <div class="bg-white my-6 p-5 rounded-2xl">
        @if (session('error'))
            <x-alert-error>
                {{ session('error') }}
            </x-alert-error>
        @endif
        @if (session('success'))
            <x-alert-success>
                {{ session('success') }}
            </x-alert-success>
        @endif

        @include('ciso/process/resources/resource-table', [
            'resources' => $processWithChecklist->resources,
        ])
    </div>
@endsection
