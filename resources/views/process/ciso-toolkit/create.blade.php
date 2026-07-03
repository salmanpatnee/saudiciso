@extends('layouts/user')
@section('title', 'CISO Toolkit')
@section('content')
    <div>
        <x-table.action-wrapper title="{{ $item?->id ? 'Update' : 'New' }} Toolkit">
            <x-action.button label="View" route_name="admin.ciso-toolkit.index" />
        </x-table.action-wrapper>

        <form action="{{ isset($item) ? route('admin.ciso-toolkit.update', $item->id) : route('admin.ciso-toolkit.store') }}"
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
                        <x-form.select label="Category" name="category" required="true" :custom_data="$categories"
                            :value="$item?->category?->value" />
                    </div>
                </x-form.grid-col>

                <x-form.grid-col>
                    <div>
                        <x-form.upload-field label="File (PDF, DOC, DOCX, TXT)" name="file" :required="!$item" />
                        @if ($item?->file_name)
                            <p class="mt-1 text-sm text-gray-500">Current file: {{ $item->file_name }}</p>
                        @endif
                    </div>
                </x-form.grid-col>

                <div class="flex justify-end">
                    <x-form.submit label="Toolkit" :isUpdate="$item?->id" />
                </div>
            </div>
        </form>
    </div>
@endsection
