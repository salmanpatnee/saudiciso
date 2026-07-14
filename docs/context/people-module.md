# People Module

Reference doc for the "People" module — what it is, how it's wired together, and where the sharp edges are. Written 2026-07-14. Use this as context instead of re-reading the whole module from scratch; update it when the module's structure changes.

## What it is

"People" is this app's HR/expert directory. Internally the domain term is **HR Expert** (model `HumanResource`, table `hr_expert_master_table`) — there is no `Person`/`Employee`/`Staff` class anywhere. The module has two faces:

1. **Public directory** — `/ciso/peoples` (route name `people.index`), a read-only, filterable "Browse Expert Resources" page. Any authenticated user can view it (no `superadmin` requirement).
2. **Admin CRUD** — `/hr-experts` plus six lookup-table CRUDs (`organizations`, `industries`, `certifications`, `expertises`, `designations`, `nationalities`), all gated behind the `superadmin` middleware. This is where experts and the master/lookup data behind them are actually created and edited.

Both faces query the same `HumanResource` model, but through two independently-written controllers (see [Duplication](#duplication-between-the-two-controllers) below).

## Data model

| Table | Model | Key columns | Rows (as of 2026-07-14) |
|---|---|---|---|
| `hr_expert_master_table` | `HumanResource` | `id`, `expert_id` (business key), `name`, `nationality_id`, `linkedin_profile`, `organization_id`, `designation` (free text), `experience` (numeric string), `industry_id` (populated but unused — see gotchas) | 357 |
| `hr_organization_table` | `HROrganization` | `id`, `organization_id`, `organization_name`, `organization_address`, `contact_number`, `website_link`, `industry_id` | 137 |
| `hr_industry_table` | `Industry` | `id`, `industry_id`, `industry_name`, `sector` | 36 |
| `hr_certification_table` | `HRCertification` | `id`, `certification_id`, `certification_title`, `institute` | 219 |
| `hr_expertise_table` | `Experties` (yes, misspelled — this is the real class, see gotchas) | `id`, `expertise_id`, `expertise_title`, `skills_include`, `expertise_description` | 205 |
| `hr_roles_table` | `HRRole` | `id`, `role_id`, `role_title`, `role_description` | 429 |
| `nationalities` | `Nationality` | `id`, `name` (soft-deletes) | 7 |
| `hr_designation_table` | `Designation` | `id`, `designation_id`, `designation_name` (soft-deletes) | **0 — table is empty, unused** |

Pivots (all simple `id`/`expert_id`/`<other>_id`, no extra columns):
- `hr_expert_master_vs_certification_table`
- `hr_expert_master_vs_expertise_table`
- `hr_expert_master_vs_roles_table`

**None of the tables above have a `CREATE TABLE` migration in this repo** — they're pre-existing/legacy tables. Only incremental `ALTER`-style migrations touch them (e.g. `2025_12_30_110000_add_nationality_id_to_hr_expert_master_table_table.php`). The only real DB-level foreign key in the whole schema is `hr_expert_master_table.nationality_id → nationalities.id`; everything else (`organization_id`, `certification_id`, `expertise_id`, etc.) is a plain string column with no DB constraint — validated only when going through the interactive admin forms.

### Relations (`app/Models/HumanResource.php`)

```
HumanResource
├── belongsTo   organization   → HROrganization (organization_id)
├── belongsTo   nationality    → Nationality (nationality_id → id)
├── belongsToMany certifications → HRCertification (via hr_expert_master_vs_certification_table)
├── belongsToMany experties      → Experties (via hr_expert_master_vs_expertise_table)
└── belongsToMany roles          → HRRole (via hr_expert_master_vs_roles_table)
```

`HROrganization` additionally has `industry()` belongsTo `Industry`. **Industry is derived through Organization**, not read from `HumanResource.industry_id` directly, anywhere in the app — see gotchas.

`designation` on `HumanResource` is a **plain free-text string**, not a foreign key to `hr_designation_table`/`Designation`, even though that lookup table/CRUD exists.

## Routes

| Route | Name | Controller | Middleware |
|---|---|---|---|
| `GET /ciso/peoples` | `people.index` | `PeoplesController` (invokable) | `auth`, `must.change.password` |
| `GET,POST,... /hr-experts...` | `hr-experts.*` | `HumanResourceController` | `auth`, `must.change.password`, `superadmin` |
| `GET,POST,... /organizations...` | `organizations.*` | `HROrganizationController` | same + `superadmin` |
| `GET,POST,... /industries...` | `industries.*` | `IndustryController` | same + `superadmin` |
| `GET,POST,... /certifications...` | `certifications.*` | `HRCertificationController` | same + `superadmin` |
| `GET,POST,... /expertises...` | `expertises.*` | `ExpertiseController` | same + `superadmin` |
| `GET,POST,... /designations...` | `designations.*` | `DesignationController` | same + `superadmin` |
| `GET,POST,... /nationalities...` | `nationalities.*` | `NationalityController` | same + `superadmin` |
| `GET /import-hr-certifications` | `import.hr-certifications` | closure in `routes/web.php:285-343` | `auth`, `must.change.password` **(not `superadmin`)** |

All defined in `routes/web.php`. The lookup-CRUD routes are standard `Route::resource(...)` inside the `superadmin` middleware group (`routes/web.php:139-153`). `people.index` lives separately inside `Route::prefix('ciso')` (`routes/web.php:190-224`).

Two routes for a bulk Excel/CSV uploader (`DataUploaderController::createHr`/`UploadHr`) are **commented out** (`routes/web.php:223-224`) — dead but present in the controller.

## Controllers

### `PeoplesController` (`app/Http/Controllers/PeoplesController.php`)
Single `__invoke()` action powering the public directory. Reads filter inputs from the query string (`nationality`, `industry_name`, `organization_name`, `certification_title`, `expertise_title`, `designation`, `experience`, each optionally an array from a multiselect), builds lookup-option lists for the filter dropdowns, then runs one big `HumanResource` query with a chain of `->when(...)` clauses — one per filter — eager-loading `certifications`, `organization.industry`, `roles`, `experties`, `nationality`. Paginates 50/page, renders `ciso.people.index`.

### `HumanResourceController` (`app/Http/Controllers/HumanResourceController.php`)
Full resource controller (`index`, `create`, `store`, `show`, `edit`, `update`, `destroy`) for the admin side, renders `process.hr.experts.*`. `index()` re-implements almost the same filter-and-paginate logic as `PeoplesController` (see [Duplication](#duplication-between-the-two-controllers)). `store()`/`update()` validate inline (no Form Request) and `sync()` the `certifications`/`experties` pivots; `roles` and education are not exposed in this form at all.

### Lookup-table controllers
`HROrganizationController`, `IndustryController`, `HRCertificationController`, `ExpertiseController`, `DesignationController`, `NationalityController` — six near-identical resource controllers, each: validate inline (`required`/`unique`/`exists` rules), `create()`/`update()`, `destroy()` wrapped in try/catch. Only `NationalityController::destroy()` actually pre-checks usage (`$nationality->hrExperts()->count() > 0`) before deleting; the others just try the delete and catch whatever the DB throws (which, since there's no real FK, is usually nothing — see gotchas).

## Views

| View | Purpose |
|---|---|
| `resources/views/ciso/people/index.blade.php` | Public directory page, custom navy/gold design, own `<style>` block, filter form + table |
| `resources/views/process/hr/experts/{index,create,show}.blade.php` | Admin CRUD for experts |
| `resources/views/process/hr/organizations/{index,create,show}.blade.php` | Admin CRUD for organizations |
| `resources/views/process/hr/experties/{index,create,show}.blade.php` | Admin CRUD for expertise |
| `resources/views/process/hr/certifications/{index,create,show}.blade.php` | Admin CRUD for certifications |
| `resources/views/process/hr/designations/{index,create,show}.blade.php` | Admin CRUD for designations |
| `resources/views/process/hr/nationalities/{index,create,show}.blade.php` | Admin CRUD for nationalities |
| `resources/views/process/hr/industries/{index,create,show}.blade.php` | Admin CRUD for industries |
| `resources/views/components/form/multiselect.blade.php` | Shared multi-select component used by every filter/pivot field. Props: `label`, `name`, `value`, `data` (Eloquent rows) or `custom_data` (plain array), `id_key`, `value_key`, `show_key` (prefixes the option with its key, e.g. `"CCNA - Cisco Certified..."`) |

Admin nav links live in `resources/views/partials/sidebar-menus/{hr-experts,organizations,expertises,designations,nationalities,certifications,industries}.blade.php`.

## How the filter query works (both controllers)

Each filter field is optional and can arrive as a scalar or an array (multiselect posts `name[]`). The pattern repeated for every filter:

```php
->when($filterValue, function ($query, $filterValue) {
    if (is_array($filterValue)) {
        $query->whereIn(...);
    } else {
        $query->where(...);
    }
})
```

- `designation`, `organization_name` filter directly on `hr_expert_master_table` columns.
- `industry_name` filters via `whereHas('organization', ...)` (industry is always reached *through* Organization).
- `certification_title`, `expertise_title` filter via `whereHas()` on the pivot relations.
- `nationality` is filtered two ways at once (matches either `nationality_id` directly or the related `Nationality.name`) — this is because the filter dropdown's option *values* are nationality **names**, not IDs, so only the `whereHas('nationality', ...)` branch ever actually matches.
- `experience` (public page only) buckets a numeric-string column into ranges (`0-5`, `6-10`, ... `20+`) via `CAST(experience AS UNSIGNED)` in a raw expression.

## Duplication between the two controllers

`PeoplesController::__invoke()` and `HumanResourceController::index()` independently implement ~120 lines of nearly-identical filter/query/paginate logic against `HumanResource`. They've already drifted:

- Public page adds an `experience`-range filter; admin page doesn't have it.
- Admin page selects `id` explicitly (needed for edit/delete links); public page doesn't (it has no edit/delete links).
- Page sizes differ (50 vs 100).

If you change filtering behavior, **check both places**. A shared query scope/trait on `HumanResource` (e.g. `scopeFilter($query, array $filters)`) would remove this duplication if it's worth doing.

## Known gotchas / things that look wrong but are "working as coded"

These are **not bugs that crash anything** — every path below executes exactly as written. Full detail and remediation options are in [`docs/conceptual-bugs-people-process-product.md`](conceptual-bugs-people-process-product.md); data-cleanliness specifics (typos, casing, dupes) are in [`docs/people-data-audit.md`](people-data-audit.md). Summary, so you don't have to re-derive these:

- **Industry has two disagreeing sources.** `hr_expert_master_table.industry_id` is fully populated (357/357) but read by nothing in the app. Every UI derives "industry" through `organization → industry`. `hr_organization_table.industry_id` was NULL for 136/138 orgs as of the original audit but has since been backfilled (137/137 populated as of 2026-07-14, re-verified via `php artisan tinker`) — the two columns still disagree on ~99% of rows (only 4/357 experts match), because they were populated independently rather than reconciled.
- **`hr_designation_table` is empty (0 rows) and structurally disconnected** from the free-text `designation` column experts actually use. The designation filter dropdown is built from `HumanResource::select('designation')->distinct()`, not from the (empty) master table.
- **Bulk import (`DataUploaderController::UploadHr`, currently dead/commented-out route) skips every FK-existence check** the interactive form enforces. Treat re-enabling it as unsafe without adding validation first.
- **No DB-level FK constraints** except `nationality_id`. Deleting an Organization/Industry/Certification/Expertise/Designation that's still referenced by an expert succeeds silently and leaves orphaned string references. Only `NationalityController` pre-checks usage before delete.
- **A full legacy parallel schema exists** (`experts_table`, `expert_organization_table`, `expert_certification_table`, etc., ~104 rows) with zero references anywhere in `app/`. Unreconciled leftover from a prior migration, not used by this module.
- **`roles()` and education relations are effectively unreachable** from the admin UI — `roles` pivot has no form field (only the dead bulk-import path could ever have written to it), and `hr_education_table`/`expert_education` have no model wiring at all beyond a broken one (next section).

## Known code-hygiene issues (from a full code review, 2026-07-14)

- `app/Models/HRExperties.php` (class `HrExperties`) is an **exact duplicate** of `app/Models/Experties.php` — same table, unused anywhere else. `Experties` is the one actually used by controllers/relations.
- `app/Models/ExpertEducation.php` defines class `ExpertIndustry` (filename/class mismatch) whose `education()` relation points at `Education::class`, which **does not exist** in `app/Models`. Currently unreferenced anywhere, so dormant, but would throw `Class "App\Models\Education" not found` if ever called.
- `app/Http/Requests/{DesignationRequest,ExpertiseRequest,IndustryRequest}.php` exist but are **unused** — their controllers validate inline via `$request->validate()` instead, contrary to this project's own CLAUDE.md convention of using Form Requests.
- `database/migrations/2025_12_31_112212_create_hr_designation_table.php` creates an empty, unrelated table literally named `hr_designation` (no `_table` suffix) — a stray duplicate of the real `hr_designation_table` migration (`2025_12_31_110454_...`). Dead/harmless but confusing.
- `GET /import-hr-certifications` (`routes/web.php:285-343`) is a "TEMPORARY" one-off CSV importer left live in the routes file, gated only by `auth`+`must.change.password` — **not** `superadmin` like every other write path in this module. Reads `data/Certifications_UNIQUE.csv` from disk and inserts rows with no idempotency check.
- `resources/views/components/form/multiselect.blade.php` only accepts a `show_key` prop; the Organization filter in `ciso/people/index.blade.php` passes a nonexistent `hide_keys="true"` prop instead, which silently does nothing.

## Quick file index

```
Routes:       routes/web.php (search "People", "hr-experts")
Controllers:  app/Http/Controllers/PeoplesController.php
              app/Http/Controllers/HumanResourceController.php
              app/Http/Controllers/{HROrganization,Industry,HRCertification,Expertise,Designation,Nationality}Controller.php
              app/Http/Controllers/DataUploaderController.php (dead bulk-import code)
Models:       app/Models/HumanResource.php
              app/Models/{HROrganization,Industry,HRCertification,Experties,HRRole,Designation,Nationality}.php
Views:        resources/views/ciso/people/index.blade.php (public)
              resources/views/process/hr/{experts,organizations,industries,certifications,experties,designations,nationalities}/*.blade.php (admin)
              resources/views/components/form/multiselect.blade.php (shared filter widget)
Docs:         docs/people-module.md (this file)
              docs/conceptual-bugs-people-process-product.md (data-integrity/design gaps)
              docs/people-data-audit.md (raw data-quality audit)
```
