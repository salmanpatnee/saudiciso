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
                @forelse ($toolkits as $toolkit)
                    <tr>
                        <x-table.td> <x-table.serial :loop="$loop" :paginator="$toolkits" /></x-table.td>
                        <x-table.td>{!! html_entity_decode($toolkit->title) !!}</x-table.td>
                        <x-table.td>
                            @if ($toolkit->category)
                                <span
                                    class="inline-flex items-center rounded-full border border-[#caa41b]/40 bg-[#f0cf3a]/[.16] px-2.5 py-1 text-xs font-bold text-[#7a5f10]">
                                    {{ $toolkit->category->value }}
                                </span>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <a href="{{ asset('storage/' . $toolkit->file_path) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 text-[#4e5870] hover:text-[#caa41b] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M12 3v13.5m0 0-4.5-4.5M12 16.5l4.5-4.5" />
                                </svg>
                                <span class="hover:underline">{{ $toolkit->file_name }}</span>
                            </a>
                        </x-table.td>
                        <x-table.td action_col="true">
                            <x-action.edit route_name="admin.ciso-toolkit.edit" param="{{ $toolkit->id }}" />
                            <x-action.delete route_name="admin.ciso-toolkit.destroy" param="{{ $toolkit->id }}" />
                        </x-table.td>
                    </tr>
                @empty
                    <tr>
                        <x-table.td class="text-center text-[#4e5870]" colspan="5">
                            No toolkit files found.
                        </x-table.td>
                    </tr>
                @endforelse
            </x-table.tbody>
        </x-table.table>

        <x-pagination>
            {{ $toolkits->links() }}
        </x-pagination>
    </div>
@endsection
