# Expert Resources (People) Data Audit

Audit of `hr_expert_master_table` and related lookup tables backing `resources/views/ciso/people/index.blade.php`, performed 2026-07-09.

**Scope**: 315 rows in `hr_expert_master_table`, plus lookup tables `nationalities` (7 rows), `hr_industry_table` (38 rows), `hr_organization_table` (137 rows), `hr_certification_table` (~140 rows), `hr_expertise_table` (~100 rows).

## Summary

Most categories are clean. The dataset has clearly been through a prior cleanup pass (the HTML-escaped-entities bug fixed in commits `705dfe2`/`c1dec65` has not recurred). Real, verifiable issues found:

1. Duplicate lookup entries in `hr_expertise_table` (same title, different IDs) — 9 rows across 6 titles
2. Inconsistent name casing (ALL CAPS / all lowercase) — 6 rows
3. Trailing whitespace/newline characters embedded in `name` — 3 rows
4. One certification title with an embedded non-breaking space — 1 row
5. A duplicate certification title reused across two different IDs — 1 pair
6. Minor ID-formatting inconsistency (stray space inside a certification_id) — 1 row

No orphaned foreign keys, no null/empty required fields, no exact-duplicate expert names, no malformed LinkedIn URLs, no experience-field anomalies, and no true mojibake were found.

---

## 1. Orphaned/invalid foreign keys — CLEAN

```sql
SELECT COUNT(*) FROM hr_expert_master_table h LEFT JOIN nationalities n ON h.nationality_id = n.id WHERE h.nationality_id IS NOT NULL AND n.id IS NULL;
SELECT COUNT(*) FROM hr_expert_master_table h LEFT JOIN hr_industry_table i ON h.industry_id = i.industry_id WHERE h.industry_id IS NOT NULL AND i.industry_id IS NULL;
SELECT COUNT(*) FROM hr_expert_master_table h LEFT JOIN hr_organization_table o ON h.organization_id = o.organization_id WHERE h.organization_id IS NOT NULL AND o.organization_id IS NULL;
```

Result: 0 orphans in all three. Every `nationality_id` in the 315 rows resolves to one of the 7 rows in `nationalities` (307 Saudi Arabia, 2 Pakistan, 2 India, 1 each Egypt/Jordan/Palestine/Sudan). No use of the missing ids 1/2.

## 2. Null/empty required fields — CLEAN

```sql
SELECT SUM(nationality_id IS NULL), SUM(industry_id IS NULL), SUM(organization_id IS NULL),
       SUM(name IS NULL OR TRIM(name)=''), SUM(designation IS NULL OR TRIM(designation)=''),
       SUM(experience IS NULL OR TRIM(experience)=''), SUM(linkedin_profile IS NULL OR TRIM(linkedin_profile)='')
FROM hr_expert_master_table;
```

All counts = 0.

## 3. Duplicate names — CLEAN (exact); note on expertise lookup duplicates below

```sql
SELECT name, COUNT(*) c FROM hr_expert_master_table GROUP BY name HAVING c > 1;          -- []
SELECT LOWER(TRIM(name)) n, COUNT(*) c FROM hr_expert_master_table GROUP BY n HAVING c>1; -- []
```

No exact or case/whitespace-normalized duplicate expert names.

## 4. HTML-escaped entities — CLEAN

Checked `name`, `designation` in `hr_expert_master_table` and title/name columns in all four lookup tables for `&amp;`, `&#039;`, `&quot;`, `&lt;`, `&gt;`, `&nbsp;`, curly-quote entities:

```sql
SELECT COUNT(*) FROM hr_expert_master_table WHERE name REGEXP '&(amp|#039|quot|lt|gt|nbsp|rsquo|lsquo|ldquo|rdquo);'; -- 0
-- same pattern against designation, industry_name, organization_name, certification_title, expertise_title -- all 0
```

This bug (fixed in commits `705dfe2`/`c1dec65`) does not appear to have recurred.

## 5. Malformed LinkedIn URLs — CLEAN

```sql
SELECT COUNT(*) FROM hr_expert_master_table WHERE linkedin_profile NOT LIKE 'http%';        -- 0
SELECT COUNT(*) FROM hr_expert_master_table WHERE linkedin_profile NOT LIKE '%linkedin.com%'; -- 0
SELECT COUNT(*) FROM hr_expert_master_table WHERE linkedin_profile LIKE '% %' OR linkedin_profile LIKE '%@%'; -- 0
SELECT COUNT(*) FROM hr_expert_master_table WHERE linkedin_profile != TRIM(linkedin_profile); -- 0
```

Every `linkedin_profile` starts with `http`, contains `linkedin.com`, has no embedded spaces/@ signs, and has no leading/trailing whitespace.

## 6. Experience field anomalies — CLEAN

```sql
SELECT COUNT(*) FROM hr_expert_master_table WHERE experience REGEXP '[^0-9]'; -- 0
SELECT experience, CAST(experience AS UNSIGNED) FROM hr_expert_master_table WHERE CAST(experience AS UNSIGNED)=0; -- []
```

All 315 `experience` values are clean integer strings ranging 1–29, no leading/trailing whitespace, no non-numeric text, no ranges-as-text, no 0 or absurd (100+) values.

## 7. Typos/casing/whitespace inconsistencies — ISSUES FOUND

### 7a. Inconsistent name casing (ALL CAPS / all lowercase) — 6 rows

```sql
SELECT expert_id, name FROM hr_expert_master_table WHERE BINARY name = BINARY UPPER(name);
SELECT expert_id, name FROM hr_expert_master_table WHERE BINARY name = BINARY LOWER(name);
```

ALL CAPS:
- `EXP-149` — "ABDULLAH O. AL ASMARI"
- `EXP-40` — "ABDULAZIZ AL-SHUTAYL"
- `EXP-33` — "ABDULAZIZ ALQAHTANI"

all lowercase:
- `EXP-214` — "abdulwahab alshadokhi"
- `EXP-175` — "abdulrahman aldaghfaq"
- `EXP-201` — "abdulrahman alshalan"

(Rest of the table is properly Title Case, so these read as data-entry inconsistencies rather than intentional style.)

### 7b. Trailing whitespace/newline embedded in `name` — 3 rows

```sql
SELECT expert_id, name FROM hr_expert_master_table WHERE name != TRIM(BOTH FROM name) OR name LIKE '%\n%' OR name LIKE '%\r%';
```

- `EXP-6` — `"Abdulaziz Al Jariyan\n"` (trailing newline)
- `EXP-31` — `"Abdulaziz Almutairi\n "` (trailing newline + space)
- `EXP-123` — `"Abdullah Alsaif\n"` (trailing newline)

These will render as-is or with a stray line break in the UI table depending on CSS `white-space` handling, and will break exact-match lookups/exports.

### 7c. Duplicate `hr_expertise_table` rows — same title, different IDs (9 rows / 6 titles)

```sql
SELECT expertise_title, COUNT(*) c, GROUP_CONCAT(expertise_id) ids
FROM hr_expertise_table GROUP BY expertise_title HAVING c > 1;
```

| expertise_title | count | ids |
|---|---|---|
| Engineer | 3 | EXP-056, EXP-204, EXP-245 |
| Information Technology | 3 | EXP-184, EXP-255, EXP-364 |
| Analyst | 2 | EXP-001, EXP-019 |
| Information Officer | 2 | EXP-074, EXP-375 |
| IT Security | 2 | EXP-162, EXP-311 |
| Officer | 2 | EXP-097, EXP-271 |

These are genuine redundant lookup rows — since `experties()` is a many-to-many pivot, an expert could theoretically be tagged with two IDs that display identically ("Engineer" three times) in the Expertise column, or filtering by one ID silently misses experts tagged with the "duplicate" ID.

### 7d. Duplicate certification title across two IDs

```sql
SELECT certification_title, COUNT(*) c, GROUP_CONCAT(certification_id) FROM hr_certification_table GROUP BY certification_title HAVING c>1;
```

- "Fortinet Certified Fundamentals" used by both `NSE-1` and `NSE-2` — these are normally distinct Fortinet certification levels (NSE 1 vs NSE 2), so one of the two titles is very likely wrong/copy-pasted.

### 7e. Non-breaking space / embedded whitespace inside a certification title

```sql
SELECT certification_id, certification_title, HEX(certification_title) FROM hr_certification_table WHERE certification_title LIKE '%CISCO Certified Network Associate%';
```

- `CCNA` — `"CISCO Certified Network Associate- Routing and Switching "` — ends in a non-breaking space (hex `C2A0`), invisible but will affect trimming/exact-match/export.

### 7f. Minor ID formatting inconsistency

- `hr_certification_table` id `"ISO-27701 -LA"` has an internal space before `-LA` (inconsistent with the sibling pattern `ISO-22301-LA`, `ISO-27001-LA` which have no space). Title itself (`"ISO27701 LEAD AUDITOR"`) is also in a different case/format than its peers (Title Case elsewhere, e.g. "ISO-22301 Lead Auditor").
- Several certification titles embed the acronym in parentheses inconsistently with the rest of the table, e.g. `GCED` → "GIAC Certified Enterprise Defender (GCED)", `GPEN` → "GIAC Penetration Tester (GPEN)", `GWAPT` → "GIAC Web Application Penetration Testing (GWAPT)", `CND` → "Certified Network Defender (CND)" — most other GIAC/certification rows do not repeat the ID in the title. Cosmetic, but inconsistent.

Designation field itself: no leading/trailing whitespace, no ALL-CAPS/all-lowercase outliers, and no case/whitespace-only near-duplicates were found (`LOWER(TRIM(designation))` grouping produced no multi-variant groups).

Industry (`hr_industry_table`, 38 rows) and Organization (`hr_organization_table`, 137 rows) lookup tables: all singleton names, no case/whitespace duplicates, no leading/trailing whitespace across all four lookup tables (verified via a `UNION` `TRIM()` check).

## 8. Encoding issues / mojibake — CLEAN

Initial broad `LIKE '%Ã%'` / `'%â€%'` scans produced false positives because MySQL's default accent-insensitive collation (`utf8mb4_general_ci`/`unicode_ci`) matches `Ã` against plain `A`. Re-ran with `BINARY` comparison to force byte-exact matching:

```sql
SELECT expert_id, name FROM hr_expert_master_table WHERE BINARY name LIKE '%\xC3%' OR BINARY name LIKE '%\xE2\x82%' OR BINARY name LIKE '%\xC2%'; -- []
SELECT expert_id, designation FROM hr_expert_master_table WHERE BINARY designation LIKE '%\xC3%' OR BINARY designation LIKE '%\xE2\x82%' OR BINARY designation LIKE '%\xC2%'; -- []
```

No mojibake (`Ã©`, `â€™`, etc.) found in `name` or `designation`. (The only non-ASCII byte sequence found anywhere was the legitimate non-breaking space in the `CCNA` certification title noted in 7e — that one used `\xC2\xA0`, a valid UTF-8 NBSP, not corruption.)

---

## Key file references

- Model: `app/Models/HumanResource.php`
- Lookup models: `Industry.php` (table `hr_industry_table`), `HROrganization.php` (`hr_organization_table`), `HRCertification.php` (`hr_certification_table`), `Experties.php` (`hr_expertise_table`)
- View: `resources/views/ciso/people/index.blade.php`
- Controller: `app/Http/Controllers/PeoplesController.php`

## Recommended fixes (not applied — audit only)

- Trim/strip newline from `name` for EXP-6, EXP-31, EXP-123.
- Normalize casing for EXP-149, EXP-40, EXP-33 (uppercase) and EXP-214, EXP-175, EXP-201 (lowercase) to Title Case.
- Consolidate the 6 duplicate `hr_expertise_table` titles onto a single canonical ID each, repointing pivot rows in `hr_expert_master_vs_expertise_table`, then drop the redundant rows.
- Verify and correct NSE-1 vs NSE-2 certification titles (should not both be "Fortinet Certified Fundamentals").
- Strip the trailing non-breaking space from the `CCNA` certification title.
- Fix the stray space in certification_id `"ISO-27701 -LA"`.
</content>
