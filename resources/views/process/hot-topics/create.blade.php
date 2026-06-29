@extends('layouts/user')
@section('title', 'Hot Topics')
@section('content')
    <div>
        <x-table.action-wrapper title="{{ $item?->id ? 'Update' : 'New' }} Hot Topic">
            <x-action.button label="View" route_name="admin.hot-topics.index" />
        </x-table.action-wrapper>

        <form action="{{ isset($item) ? route('admin.hot-topics.update', $item->id) : route('admin.hot-topics.store') }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($item))
                @method('PUT')
            @endif
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6">
                <x-form.grid-col>
                    <div>
                        <x-form.field label="Title" name="title" required="true" placeholder="Enter Title"
                            :value="$item?->title" />
                    </div>
                    <div>
                        <x-form.upload-field label="Featured Image (JPG, PNG, WEBP)" name="featured_image" />
                        @if ($item?->featured_image_path)
                            <img src="{{ asset('storage/' . $item->featured_image_path) }}" alt="{{ $item->title }}"
                                class="mt-2 h-24 w-24 rounded object-cover">
                        @endif
                    </div>
                </x-form.grid-col>

                <x-form.grid-col-full>
                    <x-form.textarea-field label="Body" name="body" placeholder="Enter body content"
                        :value="$item?->body" html="true" />
                </x-form.grid-col-full>

                <x-form.grid-col-full>
                    <div>
                        <x-form.label label="Resources (PDF, DOC, DOCX)" for="resources" />
                        <input type="file" id="resources" name="resources[]" multiple accept=".pdf,.doc,.docx"
                            class="bg-transparent border border-gray-300 file:bg-gray-50 file:border-0 file:border-collapse file:border-gray-200 file:border-r file:border-solid file:cursor-pointer file:mr-5 file:pl-3.5 file:pr-3 file:py-3 file:rounded-l-lg file:text-gray-700 file:text-sm focus:border-ring-brand-300 focus:file:ring-brand-300 focus:outline-hidden hover:file:bg-gray-100 overflow-hidden placeholder:text-gray-400 rounded-lg shadow-theme-xs text-gray-500 text-sm transition-colors w-full">
                        <x-form.error name="resources" />
                        <x-form.error name="resources.*" />
                    </div>
                </x-form.grid-col-full>

                <div class="flex justify-end">
                    <x-form.submit label="Hot Topic" :isUpdate="$item?->id" />
                </div>
            </div>
        </form>

        @if ($item?->resources?->isNotEmpty())
            <div class="border-t border-gray-100 p-5 sm:p-6">
                <p class="mb-2 font-bold">Attached Resources</p>
                <ul class="divide-y divide-gray-100 rounded-lg border border-gray-100">
                    @foreach ($item->resources as $resource)
                        <li class="flex items-center justify-between px-4 py-2">
                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank"
                                class="text-blue-600 hover:underline">{{ $resource->file_name }}</a>
                            <x-action.delete route_name="admin.hot-topics.resource.destroy"
                                param="{{ $resource->id }}" />
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        const editorSelectors = [
            '#body',
        ];

        editorSelectors.forEach(selector => {
            ClassicEditor
                .create(document.querySelector(selector))
                .catch(error => {
                    console.error(`Error initializing editor for ${selector}:`, error);
                });
        });
    </script>
@endsection
