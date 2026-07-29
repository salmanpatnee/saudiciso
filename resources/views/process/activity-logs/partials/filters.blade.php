{{-- Included with an $action route. Shared by the index and timeline screens. --}}
<div class="px-2 mb-4" x-data="{ period: '{{ $period }}' }">
    <form method="GET" action="{{ $action }}">
        <x-form.grid-4-col>
            <x-form.input label="Search" name="search" value="{{ $search }}"
                placeholder="Name, email, URL or description" />

            <x-form.select label="Period" name="period"
                :custom_data="['Today', 'This Week', 'This Month', 'Last 7 Days', 'Last 30 Days', 'Custom Range']" :value="$period" x-model="period"
                onchange="this.form.submit()" />

            <x-form.select label="Authentication" name="audience" :custom_data="['All', 'Authenticated', 'Guests']" :value="$audience"
                onchange="this.form.submit()" />

            <x-form.select label="Activity Type" name="type" :data="$types" id_key="value" value_key="label"
                :hide_keys="true" :value="$type" onchange="this.form.submit()" />
        </x-form.grid-4-col>

        <div class="mt-4">
            <x-form.grid-4-col>
                <x-form.select label="Module" name="module" :custom_data="$modules->all()" :value="$module"
                    onchange="this.form.submit()" />

                <x-form.select label="HTTP Method" name="method" :custom_data="['GET', 'POST', 'PUT', 'PATCH', 'DELETE']" :value="$method"
                    onchange="this.form.submit()" />

                <x-form.select label="HTTP Status" name="status_group"
                    :custom_data="['All', 'Success', 'Redirect', 'Client Error', 'Server Error']" :value="$status_group" onchange="this.form.submit()" />

                <x-form.select label="Device" name="device_type" :custom_data="['Desktop', 'Mobile', 'Tablet']"
                    :value="$device_type" onchange="this.form.submit()" />
            </x-form.grid-4-col>
        </div>

        <div class="mt-4">
            <x-form.grid-4-col>
                <x-form.select label="Role" name="role_id" :data="$roles" id_key="id" value_key="role_name"
                    :hide_keys="true" :value="$role_id" onchange="this.form.submit()" />

                <x-form.input label="IP Address" name="ip" value="{{ $ip }}" placeholder="e.g. 203.0.113.9" />

                <x-form.input label="Country Code" name="country_code" value="{{ $country_code }}"
                    placeholder="e.g. SA" />

                <x-form.input label="Session ID" name="session_id" value="{{ $session_id }}"
                    placeholder="Hashed session id" />
            </x-form.grid-4-col>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mt-4" x-show="period === 'Custom Range'" x-cloak>
            <div>
                <x-form.label label="From" for="date_from" />
                <input type="date" id="date_from" name="date_from" value="{{ $date_from }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
            </div>

            <div>
                <x-form.label label="To" for="date_to" />
                <input type="date" id="date_to" name="date_to" value="{{ $date_to }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-4">
            <button type="submit" class="submit-btn">
                <span class="inline mx-2">Apply Filters</span>
            </button>

            <a href="{{ $action }}"
                class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Reset
            </a>
        </div>
    </form>
</div>
