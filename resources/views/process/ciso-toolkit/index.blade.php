@extends('layouts.user')
@section('title', 'CISO Toolkit')

@section('content')
    <div>
        <x-table.action-wrapper title="CISO Toolkit">
            <x-action.button label="Add Toolkit" route_name="admin.ciso-toolkit.create" />
        </x-table.action-wrapper>

        <x-table.table>
            <x-table.thead>
                <x-table.th label="S.No" />
                <x-table.th label="Title" />
                <x-table.th label="Category" />
                <x-table.th label="File" />
                <x-table.th label="Action" />
            </x-table.thead>
            <x-table.tbody>
                @foreach ($toolkits as $toolkit)
                    <tr>
                        <x-table.td> <x-table.serial :loop="$loop" :paginator="$toolkits" /></x-table.td>
                        <x-table.td>{{ $toolkit->title }}</x-table.td>
                        <x-table.td>{{ $toolkit->category?->value }}</x-table.td>
                        <x-table.td>
                            <a href="{{ asset('storage/' . $toolkit->file_path) }}" target="_blank"
                                class="text-blue-600 hover:underline">{{ $toolkit->file_name }}</a>
                        </x-table.td>
                        <x-table.td action_col="true">
                            <x-action.edit route_name="admin.ciso-toolkit.edit" param="{{ $toolkit->id }}" />
                            <x-action.delete route_name="admin.ciso-toolkit.destroy" param="{{ $toolkit->id }}" />
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table.tbody>
        </x-table.table>

        <x-pagination>
            {{ $toolkits->links() }}
        </x-pagination>
    </div>
@endsection
