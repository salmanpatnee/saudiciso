@extends('layouts.user')
@section('title', 'CISO Education')
@section('content')
    <div>
        <x-table.action-wrapper title="{{ $item?->id ? 'Update' : 'New' }} CISO Education">
            <x-action.button label="View" route_name="admin.ciso-education.index" />
        </x-table.action-wrapper>

        <form action="{{ isset($item) ? route('admin.ciso-education.update', $item->id) : route('admin.ciso-education.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($item))
                @method('PUT')
            @endif
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6">
                <x-form.grid-col-full>
                    <x-form.field label="Title" name="title" required="true" placeholder="Enter Title"
                        :value="html_entity_decode($item?->title ?? '')" />
                </x-form.grid-col-full>

                <x-form.grid-col-full>
                    <x-form.upload-field label="Featured Image" name="featured_image" />
                    @if ($item?->featured_image_path)
                        <img src="{{ asset('storage/' . $item->featured_image_path) }}" alt="{{ $item->title }}"
                            class="mt-2 h-16 w-16 rounded object-cover">
                    @endif
                </x-form.grid-col-full>

                <x-form.grid-col-full>
                    <x-form.textarea-field label="Body" name="body" placeholder="Enter body content"
                        :value="$item?->body" html="true" />
                </x-form.grid-col-full>

                <div class="flex justify-end">
                    <x-form.submit label="CISO Education" :isUpdate="$item?->id" />
                </div>
            </div>
        </form>
    </div>

    @vite('resources/js/hot-topics-editor.js')
@endsection
