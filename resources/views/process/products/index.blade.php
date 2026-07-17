@extends('layouts.user')
@section('title', 'Products')
@section('content')
    <div>
        <x-table.action-wrapper title="Products List">
            <x-action.button label="Add Product" route_name="admin.products.create" />
        </x-table.action-wrapper>

        <x-table.table-sticky>
            <x-table.thead-sticky>
                <x-table.th label="S.No" />
                <x-table.th label="Title" />
                <x-table.th label="Action" />
            </x-table.thead-sticky>

            <x-table.tbody>
                @foreach ($products as $row)
                    <tr>
                        <x-table.td><x-table.serial :loop="$loop" :paginator="$products" /></x-table.td>
                        <x-table.td>{{ $row->title }}</x-table.td>
                        <x-table.td action_col="true">
                            <x-action.edit route_name="admin.products.edit" param="{{ $row->id }}" />
                            <x-action.delete route_name="admin.products.destroy" param="{{ $row->id }}" />
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table.tbody>
        </x-table.table-sticky>

        <x-pagination>
            {{ $products->links() }}
        </x-pagination>
    </div>
@endsection
