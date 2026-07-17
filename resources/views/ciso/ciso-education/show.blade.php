@extends('layouts.ciso-full')
@section('title', $cisoEducation->title)
@section('title_ar', '')

@push('css')
    <link href="{{ asset('css/product-detail.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="sm:px-7 kb-product-detail">
        <h1 class="kb-heading">{{ $cisoEducation->title }}</h1>
        @if ($cisoEducation->featured_image_path)
            <img src="{{ asset('storage/' . $cisoEducation->featured_image_path) }}"
                alt="{{ $cisoEducation->title }}" class="mb-6 rounded-lg max-h-64 mx-auto">
        @endif
        {!! $cisoEducation->body !!}
    </div>
@endsection
