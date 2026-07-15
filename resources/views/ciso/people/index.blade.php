@extends('layouts.ciso-full')
@section('title', 'Expert Resources')

@push('css')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #00053C;
            --navy-2: #0A1559;
            --gold: #C9A227;
            --page: #F4F6F8;
            --card: #FFFFFF;
            --border: #E6E9EF;
            --divider: #EEF1F5;
            --ink: #1F2430;
            --muted: #6B7280;
        }

        .kb {
            width: 100%;
            margin: 0 0 1rem;
            padding: 0 1rem;
            animation: kb-fade .5s ease both;
        }

        /* ---- Heading row ---- */
        .kb__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .kb__eyebrow {
            margin: 0 0 .35rem;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--gold);
        }

        .kb__title {
            margin: 0;
            font-family: "Playfair Display", Georgia, serif;
            font-weight: 700;
            font-size: clamp(1.7rem, 3.2vw, 2.4rem);
            line-height: 1.1;
            color: var(--navy);
        }

        .kb__rule {
            display: block;
            width: 64px;
            height: 3px;
            margin-top: .7rem;
            background: var(--gold);
            border-radius: 2px;
        }

        .kb__pill {
            flex: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .5rem .95rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
            color: var(--navy);
            box-shadow: 0 1px 2px rgba(0, 5, 60, .04);
        }

        .kb__pill i {
            color: var(--gold);
            font-size: 1rem;
        }

        .kb-empty {
            text-align: center;
            color: var(--muted);
            padding: 2.5rem 1.5rem;
            margin: 0;
            grid-column: 1 / -1;
        }

        /* ---- Filter card ---- */
        .kb-people__filter {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 18px 40px -20px rgba(0, 5, 60, .28);
            padding: 1.5rem;
            margin-bottom: 1.75rem;
            animation: kb-rise .55s ease both;
        }

        .kb-btn-gold {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 1.5rem;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--gold), #f0cf3a);
            color: #201800;
            font-weight: 700;
            font-size: .9rem;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(201, 162, 39, .32);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .kb-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(201, 162, 39, .42);
        }

        .kb-btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 1.5rem;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: transparent;
            color: var(--navy);
            font-weight: 700;
            font-size: .9rem;
            text-decoration: none;
            transition: transform .2s ease, border-color .2s ease, color .2s ease;
        }

        .kb-btn-ghost:hover {
            transform: translateY(-2px);
            border-color: rgba(201, 162, 39, .5);
            color: var(--gold);
        }

        .kb-filter-hint {
            margin: .35rem 0 0;
            font-size: .72rem;
            line-height: 1.3;
            color: var(--muted);
        }

        .kb-filter-loading {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            color: var(--gold);
            font-weight: 600;
        }

        .kb-filter-loading[hidden] {
            display: none;
        }

        /* ---- People table ---- */
        .kb-table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 18px 40px -20px rgba(0, 5, 60, .28);
            animation: kb-rise .55s ease both;
        }

        .kb-table-scroll {
            max-height: 640px;
            overflow: auto;
        }

        .kb-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kb-table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: linear-gradient(90deg, var(--navy), var(--navy-2));
            color: #fff;
            text-align: left;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .85rem 1rem;
            white-space: nowrap;
            border-bottom: 2px solid rgba(201, 162, 39, .55);
        }

        .kb-table tbody td {
            padding: .85rem 1rem;
            font-size: .85rem;
            color: var(--ink);
            vertical-align: top;
            border-bottom: 1px solid var(--divider);
            white-space: nowrap;
        }

        .kb-table tbody tr:last-child td {
            border-bottom: none;
        }

        .kb-table tbody tr {
            transition: background .2s ease;
        }

        .kb-table tbody tr:hover {
            background: #F8FAFC;
        }

        .kb-table__name {
            font-weight: 700;
            color: var(--navy);
        }

        a.kb-table__name {
            text-decoration: underline;
            text-decoration-color: currentColor;
            text-underline-offset: 2px;
        }

        a.kb-table__name:hover {
            color: var(--gold);
        }

        .kb-table td.kb-wrap {
            white-space: normal;
            min-width: 200px;
            max-width: 260px;
            line-height: 1.7;
            padding-top: 1.1rem;
            padding-bottom: 1.1rem;
        }

        .kb-table td.kb-wrap--certs {
            white-space: nowrap;
            max-width: none;
        }

        .kb-line {
            display: block;
            padding: .35rem 0;
            border-bottom: 1px dashed var(--divider);
        }

        .kb-line:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .kb-line:first-child {
            padding-top: 0;
        }

        .kb-table__linkedin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--border);
            color: var(--navy);
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }

        a.kb-table__linkedin:hover {
            background: var(--navy);
            border-color: var(--navy);
            color: #fff;
        }

        .kb-table__linkedin i {
            font-size: 1.05rem;
        }

        .kb-table__linkedin--disabled {
            color: var(--muted);
            cursor: default;
        }

        .kb-table .kb-empty {
            padding: 2.5rem 1.5rem;
            white-space: normal;
        }

        /* ---- Motion ---- */
        @keyframes kb-fade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes kb-rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes kb-up {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .kb {
                padding: 0 0.25rem;
            }

            .kb__head {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 1.25rem;
            }

            .kb-table thead th,
            .kb-table tbody td {
                padding: .65rem .75rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .kb, .kb-people__filter, .kb-table-card {
                animation: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="kb">
        <div class="kb__head">
            <div>
                <p class="kb__eyebrow">Expert Resources</p>
                <h1 class="kb__title">Browse Expert Resources</h1>
                <span class="kb__rule"></span>
            </div>
        </div>

        <form action="{{ route('people.index') }}" method="GET">
            <div class="kb-people__filter">
                <x-form.grid-3-col>
                    <div>
                        <x-form.multiselect label="Nationality" name="nationality[]" :value="$nationality" :custom_data="$nationalities" />
                    </div>
                    <div>
                        <x-form.multiselect label="Industry" name="industry_name[]" :value="$industry" :data="$industries"
                            id_key="industry_id" value_key="industry_name"  />
                        <p class="kb-filter-hint">
                            <span class="kb-filter-hint__text">Narrows the Organization list to matching organizations.</span>
                            <span class="kb-filter-loading" data-loading-for="industry" hidden>
                                <i class='bx bx-loader-alt bx-spin'></i> Updating options&hellip;
                            </span>
                        </p>
                    </div>
                    <div>
                        <x-form.multiselect label="Organization" name="organization_name[]" :value="$organization"
                            :data="$organizations" id_key="organization_id" value_key="organization_name" hide_keys="true" />
                        <p class="kb-filter-hint">
                            <span class="kb-filter-hint__text">Narrows the Industry list to its matching industry.</span>
                            <span class="kb-filter-loading" data-loading-for="organization" hidden>
                                <i class='bx bx-loader-alt bx-spin'></i> Updating options&hellip;
                            </span>
                        </p>
                    </div>
                    <div>
                        <x-form.multiselect label="Certification" name="certification_title[]" :value="$certification"
                            :data="$certifications" id_key="certification_id" value_key="certification_title" show_key="true" />
                    </div>
                    <div>
                        <x-form.multiselect label="Expertise" name="expertise_title[]" :value="$expertise" :data="$experties"
                            id_key="expertise_id" value_key="expertise_title"  />
                    </div>
                    <div>
                        <x-form.multiselect label="Designation" name="designation[]" :value="$designation"
                            :custom_data="$designations" />
                    </div>
                    <div>
                        <x-form.multiselect label="Experience" name="experience[]" :value="$experience" :custom_data="$experienceRanges" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="kb-btn-gold w-full">Filter Resources</button>
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('people.index') }}" class="kb-btn-ghost w-full">Reset</a>
                    </div>
                </x-form.grid-3-col>
            </div>
        </form>

        <div class="kb-table-card">
            <div class="kb-table-scroll">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Expert</th>
                            <th>Nationality</th>
                            <th>Industry</th>
                            <th>Organization</th>
                            <th>Certifications</th>
                            <th>Expertise</th>
                            <th>Designation</th>
                            <th>Experience</th>
                            <th>LinkedIn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($humanResource as $row)
                            <tr>
                                <td>{{ ($humanResource->currentPage() - 1) * $humanResource->perPage() + $loop->index + 1 }}</td>
                                <td>
                                    @if (!empty($row->linkedin_profile))
                                        <a class="kb-table__name" href="{{ $row->linkedin_profile }}" target="_blank" rel="noopener">{{ $row->name }}</a>
                                    @else
                                        <div class="kb-table__name">{{ $row->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    {{ isset($row->nationality) && !is_string($row->nationality) ? $row->nationality->name : $row->nationality }}
                                </td>
                                <td>{{ $row->organization?->industry?->industry_name }}</td>
                                <td>{{ $row->organization?->organization_name }}</td>
                                <td class="kb-wrap kb-wrap--certs">{!! $row->certifications->filter(fn ($cert) => $cert->certification_title)->map(fn ($cert) => '<span class="kb-line">'.e($cert->certification_id.' - '.$cert->certification_title).'</span>')->implode('') ?: '-' !!}</td>
                                <td class="kb-wrap">{!! $row->experties->pluck('expertise_title')->filter()->map(fn ($title) => '<span class="kb-line">'.e($title).'</span>')->implode('') ?: '-' !!}</td>
                                <td>{{ $row->designation }}</td>
                                <td>{{ $row->experience }}</td>
                                <td>
                                    @if (!empty($row->linkedin_profile))
                                        <a class="kb-table__linkedin" href="{{ $row->linkedin_profile }}" target="_blank" rel="noopener" title="LinkedIn Profile">
                                            <i class='bx bxl-linkedin-square'></i>
                                        </a>
                                    @else
                                        <span class="kb-table__linkedin kb-table__linkedin--disabled" title="Not provided">
                                            <i class='bx bxl-linkedin-square'></i>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="kb-empty">No expert resources found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-pagination>
            {{ $humanResource->links() }}
        </x-pagination>
    </div>
@endsection

@push('scripts')
    <script>
        window.peopleCascadeData = {
            organizationIndustry: @json($organizations->pluck('industry_id', 'organization_id')),
        };
    </script>
    <script>
        $(function () {
            var orgIndustryMap = window.peopleCascadeData.organizationIndustry || {};
            var $industrySelect = $('select[name="industry_name[]"]');
            var $orgSelect = $('select[name="organization_name[]"]');

            var originalIndustryOptions = $industrySelect.find('option[value!=""]').clone();
            var originalOrgOptions = $orgSelect.find('option[value!=""]').clone();

            function selectedValues($select) {
                return ($select.val() || []).map(String);
            }

            function rebuildOrgOptions() {
                var selectedIndustries = selectedValues($industrySelect);
                var currentOrgSelection = selectedValues($orgSelect);

                $orgSelect.empty().append('<option value=""></option>');
                originalOrgOptions.each(function () {
                    var $opt = $(this);
                    var orgId = String($opt.val());
                    var orgIndustryId = orgIndustryMap[orgId] != null ? String(orgIndustryMap[orgId]) : null;
                    var matches = selectedIndustries.length === 0
                        || (orgIndustryId !== null && selectedIndustries.indexOf(orgIndustryId) !== -1)
                        || currentOrgSelection.indexOf(orgId) !== -1;

                    if (matches) {
                        $orgSelect.append($opt.clone());
                    }
                });

                $orgSelect.val(currentOrgSelection).trigger('change.select2');
            }

            function rebuildIndustryOptions() {
                var selectedOrgs = selectedValues($orgSelect);
                var currentIndustrySelection = selectedValues($industrySelect);

                var relevantIndustryIds = selectedOrgs
                    .map(function (orgId) {
                        return orgIndustryMap[orgId] != null ? String(orgIndustryMap[orgId]) : null;
                    })
                    .filter(function (industryId) {
                        return industryId !== null;
                    });

                $industrySelect.empty().append('<option value=""></option>');
                originalIndustryOptions.each(function () {
                    var $opt = $(this);
                    var industryId = String($opt.val());
                    var matches = relevantIndustryIds.length === 0
                        || relevantIndustryIds.indexOf(industryId) !== -1
                        || currentIndustrySelection.indexOf(industryId) !== -1;

                    if (matches) {
                        $industrySelect.append($opt.clone());
                    }
                });

                $industrySelect.val(currentIndustrySelection).trigger('change.select2');
            }

            function withLoadingIndicator(target, rebuildFn) {
                var $indicator = $('[data-loading-for="' + target + '"]');
                $indicator.removeAttr('hidden');
                // Deferred so the indicator actually paints before the (synchronous) rebuild runs.
                setTimeout(function () {
                    rebuildFn();
                    $indicator.attr('hidden', true);
                }, 0);
            }

            $industrySelect.on('change', function () {
                withLoadingIndicator('organization', rebuildOrgOptions);
            });
            $orgSelect.on('change', function () {
                withLoadingIndicator('industry', rebuildIndustryOptions);
            });

            rebuildOrgOptions();
            rebuildIndustryOptions();
        });
    </script>
@endpush
