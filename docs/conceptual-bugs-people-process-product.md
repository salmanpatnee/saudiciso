# Conceptual Bugs — People, Process, Product Modules

Audit date: 2026-07-14. Scope: `People` (HR Experts, Organizations, Industries, Certifications, Expertise, Designations, Nationalities), `Process` (CMS process pages + resources), `Product` (static marketing pages).

These are **not** crashes or failing tests — every code path below runs exactly as written. The bugs are gaps where the schema/validation/import layer lets data exist that silently violates a relationship the business domain implies. Findings are ordered by blast radius (widest silent data corruption first).

---

## [1] Expert industry and Organization industry are two disagreeing, mostly-disconnected sources of truth

- **Where:**
  - `app/Models/HumanResource.php:8-63` — no relation/accessor for `industry_id` at all
  - `app/Models/HROrganization.php:18-21` — `industry()` belongsTo relation exists here instead
  - `app/Http/Controllers/HumanResourceController.php:161-175, 177-189, 238-285` — `store()`/`update()` validation and mass-assignment never reference `industry_id`
  - `resources/views/process/hr/experts/create.blade.php:55-59` — Organization select present, no Industry field anywhere in the form
  - `resources/views/ciso/people/index.blade.php:387` and `resources/views/process/hr/experts/index.blade.php:68` — industry is displayed via `$row->organization?->industry?->industry_name`, i.e. always derived through Organization, never from the expert's own column
- **Gap:** The `hr_expert_master_table` has a fully-populated `industry_id` column (357/357 rows set, real `IND-xxx` values) that no model relation, controller, or view ever reads or writes. Meanwhile every UI that shows "this expert's industry" derives it transitively through `Organization → Industry`. As of 2026-07-14, `hr_organization_table.industry_id` has since been backfilled and is now fully populated (137/137 organizations), but the two sources still disagree on the vast majority of rows (only 4/357 experts have `industry_id` matching their organization's `industry_id`) — they were evidently populated independently, not reconciled with each other.
- **Evidence (re-verified 2026-07-14 via `php artisan tinker`):** `hr_expert_master_table.industry_id` populated for all 357 rows; `hr_organization_table.industry_id` populated for all 137 rows. Joining the two via `organization_id` and comparing `industry_id` yields only 4 matching rows out of 357. *(Note: an earlier version of this finding stated `hr_organization_table.industry_id` was NULL for 136/138 rows — that has since been backfilled and is no longer accurate; the underlying two-sources-of-truth gap remains.)*
- **Why it's conceptual, not a code bug:** Nothing throws. The app was simply built to derive industry through Organization and never wired the expert's own `industry_id` column into any code path — it's dead-but-populated data sitting next to a separately-populated alternate path that was never reconciled.
- **Real-world plausibility:** An expert's industry and their employer's industry can legitimately differ in edge cases (a cybersecurity consultant embedded in a healthcare org), but a ~99% mismatch rate across the board indicates this isn't intentional divergence — the two columns were filled in independently, not derived from one another.
- **Remediation options:**
  1. Pick one source of truth: either backfill `hr_organization_table.industry_id` from the expert-level data (since it's actually complete) and drop `hr_expert_master_table.industry_id`, or promote the expert-level column into the model/forms and stop deriving through Organization.
  2. If both are meant to coexist (expert's personal industry vs. employer's industry), expose both explicitly in the UI/forms with distinct labels and add a soft "mismatch" indicator rather than silently picking one.
  3. At minimum, add a data-reconciliation report/command so someone can see the divergence rather than it being invisible.

---

## [2] ~~Organization → Industry is required going forward but NULL for 98.5% of existing organizations~~ (resolved — backfilled)

- **Status as of 2026-07-14 (re-verified via `php artisan tinker`):** `hr_organization_table.industry_id` is now populated for all 137 organizations (0 NULL). This finding originally reported 136/138 rows NULL; that backlog has since been backfilled, so the gap described below no longer exists. Left here for history — see Finding 1 for the still-open issue (the backfilled values don't reconcile with `hr_expert_master_table.industry_id`).
- **Where (as originally filed):**
  - `app/Http/Controllers/HROrganizationController.php:42-49` — `store()`: `'industry_id' => 'required|exists:hr_industry_table,industry_id'`
  - `app/Http/Controllers/HROrganizationController.php:80-95` — `update()` — same required rule
  - `resources/views/process/hr/organizations/create.blade.php:30-33` — Industry select marked `required="true"`
  - `resources/views/process/hr/organizations/index.blade.php:24` — `{{ $organization->industry?->industry_name }}`
- **Original gap:** The form and validation treated "organization has an industry" as a mandatory invariant, but at the time 136 of 138 existing organization rows had `industry_id = NULL`, with no migration/backfill step and no distinction in the UI between "industry legitimately unset" and "industry never captured."
- **Why it's conceptual, not a code bug:** The required-on-create rule worked correctly for new organizations; it was added after most of the data already existed, and the backlog has since been reconciled by a backfill.

---

## [3] Bulk HR import bypasses every FK-existence check the interactive form enforces

- **Where:**
  - `app/Http/Controllers/DataUploaderController.php:190-192` — only validation is `'excel_file' => 'required|mimes:xlsx,xls'`
  - `app/Http/Controllers/DataUploaderController.php:201-236` — `HumanResource::updateOrCreate(['expert_id' => ...], $data)` with `$data` being the raw spreadsheet row; `experties`/`roles`/`certifications` pivots `sync()`'d from raw comma-split cells
  - Compare to `app/Http/Controllers/HumanResourceController.php:161-175` — interactive `store()` validates `organization_id`, `nationality_id`, `certifications.*`, `experties.*` all with `exists:` rules
- **Gap:** The only interactive/manual entry point (`HumanResourceController`) enforces FK-existence for organization, nationality, certifications, and expertise. The bulk-import entry point (`DataUploaderController::UploadHr`) enforces none of that — any spreadsheet value for `organization_id`, `nationality_id`, `industry_id`, `designation`, or comma-separated certification/expertise/role IDs is written or synced as-is, with no check it corresponds to a real row.
- **Evidence:** `$expert = HumanResource::updateOrCreate(['expert_id' => $data['expert_id']], $data);` then `$expert->experties()->sync($expertIds)` etc., with `$expertIds` coming straight from `explode(',', $experties)` — no `exists:` check anywhere in this method, and `HumanResource::$guarded = []` (`app/Models/HumanResource.php:14`) means there's no fillable allowlist as a second line of defense either.
- **Why it's conceptual, not a code bug:** `mimes:xlsx,xls` validation is exactly what was written and it works — the gap is the *absence* of field-level rules that exist one file away in the sibling controller.
- **Real-world plausibility:** Always a real risk when this path is used — a typo'd organization ID or a stray certification code in a spreadsheet silently creates orphaned references with no error to the uploader.
- **Remediation options:**
  1. Extract the `HumanResourceController::store()` validation rules into a shared Form Request/rule set and apply per-row inside the import loop, skipping/reporting invalid rows instead of blind `updateOrCreate`.
  2. Add a post-import reconciliation report listing rows whose FK values don't match any master table, without blocking the import.
  3. If this route is intentionally dead (it's currently commented out at `routes/web.php:223-224`), leave it disabled, but treat it as unsafe-to-re-enable-as-is if anyone considers turning it back on.

---

## [4] Designation "master data" and actual expert designations are two unconnected data sets

- **Where:**
  - `app/Models/Designation.php:8-21` + `app/Http/Controllers/DesignationController.php` — full CRUD against `hr_designation_table` (`designation_id`, `designation_name`)
  - `hr_expert_master_table.designation` — plain free-text `varchar(200)` column, no FK, no relation
  - `app/Http/Controllers/PeoplesController.php:28-31` — designation filter dropdown built from `HumanResource::select('designation')->distinct()`, i.e. from the free-text values actually typed on experts, **not** from `hr_designation_table`
  - `resources/views/process/hr/experts/create.blade.php:81` — Designation is a plain text field (`x-form.field`), not a select tied to the Designation master list
  - `app/Http/Controllers/DesignationController.php:125-126` — comment: *"Check if designation is being used by any human resources before deletion — This would require checking the relationship with hr_expert_master_table"* — not implemented, and structurally can't be meaningfully implemented while `designation` stays free text
- **Gap:** There's a whole CRUD module for managing a canonical list of designations, but the actual field on experts is unconstrained free text with no dropdown, no validation against the master list, and no way to reconcile the two. The People-index designation filter derives its options from the free-text field instead of the master table, so the master table and the filter are two more independent lists that happen to look similar.
- **Evidence:** `Designation` model has zero relation to `HumanResource`; grep confirms `designation_id` never appears alongside `HumanResource`. The create/edit expert form has no select for designation.
- **Why it's conceptual, not a code bug:** The `DesignationController` CRUD works fine in isolation, and the free-text expert field works fine in isolation — nothing errors. They were simply never wired together.
- **Real-world plausibility:** Guaranteed drift — free text with no canonical constraint will accumulate case variants, typos, and near-duplicates ("CISO" vs "Chief Information Security Officer" vs "ciso") that the Designation master list can never catch or normalize.
- **Remediation options:**
  1. Convert the expert `designation` field to a `designation_id` FK/select sourced from `hr_designation_table`, with a one-time migration mapping existing free-text values to canonical designation rows (flagging unmatched ones for manual review).
  2. Keep free text but add a background job/report that diffs distinct `designation` values against the master list and surfaces new/unmatched ones for an admin to canonicalize.
  3. If free text is intentional (designations vary too much to canonicalize), retire the `hr_designation_table` CRUD or clearly document it as aspirational/unused.

---

## [5] Legacy parallel "expert" schema (104 experts) never reconciled with the live `hr_expert_master_table` (357 experts)

- **Where:** `experts_table` (104 rows), `expert_organization_table` (26), `expert_industry_table` (18), `expert_role_table` (58), `expert_certification_table` (116), `expert_education_table` (43), `expert_experties_table` (40), plus `expert_master_vs_expert_*` pivots (268 rows each) — none referenced by any current `app/Models` class or controller (confirmed via grep for their table names — no matches in `app/`)
- **Gap:** A full second copy of "expert" data with its own certifications/education/expertise/roles pivots exists live in the database, structurally parallel to `hr_expert_master_table`, but disconnected from every current code path. There's no migration, merge script, or flag indicating whether this data was superseded, is still authoritative for some historical purpose, or was simply abandoned mid-migration.
- **Evidence:** Grep across `app/` for `experts_table|expert_organization_table|expert_industry_table|expert_role_table|expert_certification_table|expert_education_table|expert_experties_table` returns no matches — these tables are pure orphans from the application's point of view, yet still hold real rows.
- **Why it's conceptual, not a code bug:** Nothing in the running app touches these tables, so nothing is "broken" — the risk is entirely that this data looks abandoned but might still be someone's source of truth, or might contain experts never carried over to `hr_expert_master_table`.
- **Real-world plausibility:** With 104 vs. 357 rows and non-overlapping table names/shapes, this reads as an incomplete migration rather than an intentional dual-store design.
- **Remediation options:**
  1. Diff the legacy `experts_table` against `hr_expert_master_table` (by name/email/expert_id) to confirm all 104 legacy experts were carried forward; migrate any gaps, then drop the legacy tables.
  2. If the legacy tables are known-obsolete, archive/export them and drop them to remove the ambiguity.
  3. If they're intentionally retained for historical/audit reasons, document that explicitly (e.g., a README or migration comment) so future readers don't have to rediscover this via grep.

---

## [6] Expert↔Role and Expert↔Education pivots are unreachable from the interactive CRUD

- **Where:**
  - `app/Models/HumanResource.php:52-62` — `roles()` relation exists, but `app/Http/Controllers/HumanResourceController.php` never reads/writes it in `store()` (159-200), `update()` (238-285), `create()` (143-152), or `edit()` (221-230) — only referenced inside the (currently dead/commented-out) `DataUploaderController::UploadHr` import path
  - `hr_expert_master_vs_education_table` and `hr_education_table` exist in the DB with no corresponding relation on `HumanResource` at all — no model references them
- **Gap:** Any expert's `roles` pivot data can only have been written by the disabled bulk-import route, and once written, there is no UI path to view, edit, or clear it. Education data has no application-layer representation whatsoever — the table and pivot are pure orphans, exactly like Finding 5 but for a still-referenced master table (`hr_education_table`).
- **Evidence:** `roles()` belongsToMany defined at `app/Models/HumanResource.php:52-62` but grep of `HumanResourceController.php` shows no `roles` key handled in validation, create, or sync logic — compare directly to `certifications`/`experties`, which are both validated and synced in `store()`/`update()`.
- **Why it's conceptual, not a code bug:** The `roles()` relation itself works fine when called; it's simply never invoked from any live, reachable code path today.
- **Real-world plausibility:** If any expert rows currently have role assignments (from a past run of the now-disabled import), that data is silently frozen — administrators would reasonably expect the Roles they see in a database export to be editable through the app, and they aren't.
- **Remediation options:**
  1. Add a Roles multiselect to the expert create/edit form, mirroring the existing Certifications/Expertise pattern, and include `roles`/`roles.*` in the validation + sync logic.
  2. If Roles are intentionally import-only, document that and hide/disable any admin expectation of editing them inline.
  3. For Education: either build out the model relation + form field, or drop the orphaned table/pivot if it's confirmed unused.

---

## [7] No DB-level FK constraints anywhere except `nationality_id` — deletes of Organization/Industry/Certification/Expertise silently orphan references

- **Where:**
  - Only real FK in the schema: `hr_expert_master_table.nationality_id → nationalities.id` (`database/migrations/2025_12_30_110000_add_nationality_id_to_hr_expert_master_table_table.php:18`)
  - `app/Http/Controllers/HROrganizationController.php:100-111` (`destroy()`), `app/Http/Controllers/IndustryController.php:90-101` (`destroy()`), `app/Http/Controllers/HRCertificationController.php:88-99` (`destroy()`) — all wrap `$model->delete()` in a generic try/catch with no pre-check
  - Compare: `app/Http/Controllers/NationalityController.php:100-102` — the *only* destroy action with a real guard, `if ($nationality->hrExperts()->count() > 0) { ...error... }`, made possible only because `nationality_id` has a real FK/relation
  - `app/Http/Controllers/ExpertiseController.php:122-125` — comment: *"Check if expertise is being used by any human resources before deletion... This would require checking the pivot table relationship"* — not implemented; proceeds straight to `delete()`
- **Gap:** Deleting an Organization, Industry, Certification, or Expertise that's still referenced by experts succeeds silently — there is no DB constraint to reject it and no application-level pre-check (unlike Nationality, which does check, and unlike `CMSController::destroy()` for Process which also pre-checks `resources()->count()`). This leaves `hr_expert_master_table.organization_id` / `hr_organization_table.industry_id` / pivot rows pointing at IDs that no longer exist anywhere.
- **Evidence:** Two of the destroy actions (`ExpertiseController`, `DesignationController`) even contain explicit code comments acknowledging the check should exist and stating it wasn't built, while `NationalityController` proves the team knows how to write this check when a relation is available.
- **Why it's conceptual, not a code bug:** Each `destroy()` does exactly what was coded — delete the row. Nothing errors; the row you deleted is just still "in use" by orphaned string references elsewhere with no way to detect it after the fact.
- **Real-world plausibility:** Always a real risk in an admin CRUD — someone cleaning up "unused-looking" master data (e.g., merging two similarly-named certifications) can silently orphan every expert referencing the deleted one, with no warning.
- **Remediation options:**
  1. Add the same in-use pre-check pattern used by `NationalityController`/`CMSController` to `HROrganizationController`, `IndustryController`, `HRCertificationController`, `ExpertiseController`, and `DesignationController` (the last one would first need Finding 4 resolved, since `designation` isn't a real FK today).
  2. Add real DB-level FK constraints (with `RESTRICT` or `SET NULL` as appropriate) now that the varchar-based "FK" columns are consistently named — this also closes Finding 3's gap for bulk import, since the DB would reject invalid values it doesn't already.
  3. At minimum, add a scheduled data-integrity check that reports orphaned references after any master-data deletion, even without blocking the delete itself.

---

## Product module — no findings

`Product` has no database table, model, migration, or persistence layer at all — `ProductsController.php:16-52` is a hard-coded, in-memory list of 29 title→route pairs rendering static Blade pages (`resources/views/ciso/products/*.blade.php`). There's no data relationship to violate, so no conceptual bug applies here under this audit's criteria. (If Product is meant to become a real CRUD-backed module later, that's a feature gap, not a conceptual bug.)
