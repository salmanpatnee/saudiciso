@extends('layouts.ciso-full')
@section('title', $hotTopic->title)
@section('content')
    <article class="px-5 py-6 sm:px-7">
        <a href="{{ route('hot-topics.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-900">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1 0 1.06L9.06 10l3.73 3.71a.75.75 0 1 1-1.06 1.06l-4.25-4.24a.75.75 0 0 1 0-1.06l4.25-4.24a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
            </svg>
            Back to Hot Topics
        </a>

        @if ($hotTopic->featured_image_path)
            <img src="{{ asset('storage/' . $hotTopic->featured_image_path) }}" alt="{{ $hotTopic->title }}"
                class="mt-5 block h-auto w-full rounded-2xl object-cover">
        @endif

        <header class="mt-6">
            <h1 class="text-2xl font-bold leading-tight text-gray-900 sm:text-3xl">{{ $hotTopic->title }}</h1>
            <p class="mt-2 flex items-center gap-1.5 text-sm text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                </svg>
                Published {{ $hotTopic->created_at->format('F j, Y') }}
            </p>
        </header>

        @if ($hotTopic->body)
            <div class="prose mt-6 max-w-none text-gray-800">
                {!! $hotTopic->body !!}
            </div>
        @endif

        @if ($hotTopic->resources->isNotEmpty())
            <section class="mt-8 border-t border-gray-100 pt-6">
                <h2 class="mb-3 text-lg font-bold text-gray-900">Downloadable Resources</h2>
                <ul class="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200">
                    @foreach ($hotTopic->resources as $resource)
                        <li>
                        <a href="{{ asset('storage/' . $resource->file_path) }}" download="{{ $resource->file_name }}"
                            class="flex items-center justify-between gap-3 bg-white px-4 py-3 transition-colors hover:bg-gray-50">
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                        <path d="M3 3.5A1.5 1.5 0 0 1 4.5 2h6.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 16 6.622V16.5a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 3 16.5v-13Z" />
                                    </svg>
                                </span>
                                <span class="truncate text-sm font-medium text-gray-800">{{ $resource->file_name }}</span>
                            </span>
                            <span class="flex flex-none items-center gap-2 text-gray-400">
                                <span class="rounded border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-gray-500">
                                    {{ strtoupper(pathinfo($resource->file_name, PATHINFO_EXTENSION)) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v8.69l2.97-2.97a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 10.53a.75.75 0 1 1 1.06-1.06l2.97 2.97V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd" />
                                    <path d="M3.5 14.75a.75.75 0 0 0-1.5 0v.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-.5a.75.75 0 0 0-1.5 0v.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-.5Z" />
                                </svg>
                            </span>
                        </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </article>
@endsection
