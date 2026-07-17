@extends('layouts.user')
@section('title', 'CISO Education')
@section('content')
    <div>
        <x-table.action-wrapper title="CISO Education List">
            <x-action.button label="Add Education" route_name="admin.ciso-education.create" />
        </x-table.action-wrapper>

        <x-table.table-sticky>
            <x-table.thead-sticky>
                <x-table.th label="S.No" />
                <x-table.th label="Title" />
                <x-table.th label="Action" />
            </x-table.thead-sticky>

            <x-table.tbody>
                @foreach ($cisoEducations as $row)
                    <tr>
                        <x-table.td><x-table.serial :loop="$loop" :paginator="$cisoEducations" /></x-table.td>
                        <x-table.td>{{ $row->title }}</x-table.td>
                        <x-table.td action_col="true">
                            <x-action.edit route_name="admin.ciso-education.edit" param="{{ $row->id }}" />
                            <x-action.delete route_name="admin.ciso-education.destroy" param="{{ $row->id }}" />
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table.tbody>
        </x-table.table-sticky>

        <x-pagination>
            {{ $cisoEducations->links() }}
        </x-pagination>
    </div>
@endsection
