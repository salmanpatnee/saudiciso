@extends('layouts.user')
@section('title', 'Hot Topics')

@section('content')
    <div>
        <x-table.action-wrapper title="Hot Topics">
            <x-action.button label="Add Hot Topic" route_name="admin.hot-topics.create" />
        </x-table.action-wrapper>

        <x-table.table>
            <x-table.thead>
                <x-table.th label="S.No" />
                <x-table.th label="Title" />
                <x-table.th label="Category" />
                <x-table.th label="Featured Image" />
                <x-table.th label="Resources" />
                <x-table.th label="Action" />
            </x-table.thead>
            <x-table.tbody>
                @foreach ($hotTopics as $hotTopic)
                    <tr>
                        <x-table.td> <x-table.serial :loop="$loop" :paginator="$hotTopics" /></x-table.td>
                        <x-table.td>{!! $hotTopic->title !!}</x-table.td>
                        <x-table.td>{{ $hotTopic->category?->value }}</x-table.td>
                        <x-table.td>
                            @if ($hotTopic->featured_image_path)
                                <img src="{{ asset('storage/' . $hotTopic->featured_image_path) }}"
                                    alt="{{ $hotTopic->title }}" class="h-12 w-12 rounded object-cover">
                            @else
                                <span class="text-gray-400">&mdash;</span>
                            @endif
                        </x-table.td>
                        <x-table.td>{{ $hotTopic->resources_count }}</x-table.td>
                        <x-table.td action_col="true">
                            <x-action.edit route_name="admin.hot-topics.edit" param="{{ $hotTopic->id }}" />
                            <x-action.delete route_name="admin.hot-topics.destroy" param="{{ $hotTopic->id }}" />
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table.tbody>
        </x-table.table>

        <x-pagination>
            {{ $hotTopics->links() }}
        </x-pagination>
    </div>
@endsection
