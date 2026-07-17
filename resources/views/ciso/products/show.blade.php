@extends('layouts.ciso-full')
@section('title', $product->title)
@section('title_ar', '')

@push('css')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/product-detail.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="sm:px-7 kb-product-detail">
        {!! $product->body !!}
    </div>
@endsection
