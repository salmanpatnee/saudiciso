@extends('layouts/user')
@section('title', 'Process')
@section('content')
    <div>
        <x-table.action-wrapper title="{{ $cm?->id ? 'Update' : 'New' }} Process">
            <x-action.button label="View" route_name="cms.index" />
        </x-table.action-wrapper>

        <form action="{{ isset($cm) ? route('cms.update', $cm->id) : route('cms.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @if (isset($cm))
                @method('PUT')
            @endif
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6">
                <x-form.grid-col>
                    <div>
                        <x-form.field label="Process ID" name="process_id" required="true"
                            :readonly="$cm?->process_id" placeholder="Enter Process ID" :value="$cm?->process_id" />
                    </div>
                    <div>
                        <x-form.field label="Process Name" name="title" required="true"
                            placeholder="Enter Process Name" :value="$cm?->title" />
                    </div>
                </x-form.grid-col>

                <x-form.grid-col>
                    <div>
                        <x-form.field label="Process Name Arabic" name="title_ar"
                            placeholder="Enter Process Name Arabic" :value="$cm?->title_ar" />
                    </div>
                    <div>
                        <x-form.upload-field label="Featured Image" name="featured_image" />
                        @if ($cm?->featured_image_path)
                            <img src="{{ asset('storage/' . $cm->featured_image_path) }}" alt="{{ $cm->title }}"
                                class="mt-2 h-16 w-16 rounded object-cover">
                        @endif
                    </div>
                </x-form.grid-col>

                <x-form.grid-col>
                    <div>
                        <x-form.field label="Order" name="order" type="number" placeholder="Enter Order"
                            :value="$cm?->order" />
                    </div>
                </x-form.grid-col>

                <x-form.grid-col-full>
                    <x-form.textarea-field label="Process Description" name="description"
                        placeholder="Enter Process Description" :value="$cm?->description" html="true" />
                </x-form.grid-col-full>



                <div class="flex justify-end">
                    <x-form.submit label="Process" :isUpdate="$cm?->id" />
                </div>
            </div>
        </form>

    </div>

    @vite('resources/js/hot-topics-editor.js')
@endsection
