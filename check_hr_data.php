<?php
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "========== HR EXPERT MASTER TABLE DATA INTEGRITY CHECK ==========\n\n";

// 1. Check for duplicate expert_id
echo "1. CHECKING FOR DUPLICATE EXPERT_ID\n";
$duplicateExpertIds = DB::table('hr_expert_master_table')
    ->select('expert_id', DB::raw('COUNT(*) as count'))
    ->groupBy('expert_id')
    ->having(DB::raw('COUNT(*)'), '>', 1)
    ->get();

if ($duplicateExpertIds->count() > 0) {
    echo "   ⚠️  DUPLICATES FOUND:\n";
    foreach ($duplicateExpertIds as $row) {
        echo "      expert_id: {$row->expert_id} (appears {$row->count} times)\n";
    }
} else {
    echo "   ✅ No duplicates found in expert_id\n";
}

// 2. Check for duplicate names
echo "\n2. CHECKING FOR DUPLICATE NAMES\n";
$duplicateNames = DB::table('hr_expert_master_table')
    ->select('name', DB::raw('COUNT(*) as count'))
    ->groupBy('name')
    ->having(DB::raw('COUNT(*)'), '>', 1)
    ->get();

if ($duplicateNames->count() > 0) {
    echo "   ⚠️  DUPLICATES FOUND:\n";
    foreach ($duplicateNames as $row) {
        echo "      name: '{$row->name}' (appears {$row->count} times)\n";
    }
} else {
    echo "   ✅ No duplicates found in name column\n";
}

// 3. Check for duplicate linkedin_profile
echo "\n3. CHECKING FOR DUPLICATE LINKEDIN_PROFILE\n";
$duplicateLinkedIn = DB::table('hr_expert_master_table')
    ->select('linkedin_profile', DB::raw('COUNT(*) as count'))
    ->where('linkedin_profile', '!=', null)
    ->where('linkedin_profile', '!=', '')
    ->groupBy('linkedin_profile')
    ->having(DB::raw('COUNT(*)'), '>', 1)
    ->get();

if ($duplicateLinkedIn->count() > 0) {
    echo "   ⚠️  DUPLICATES FOUND:\n";
    foreach ($duplicateLinkedIn as $row) {
        echo "      linkedin_profile: '{$row->linkedin_profile}' (appears {$row->count} times)\n";
    }
} else {
    echo "   ✅ No duplicates found in linkedin_profile\n";
}

// 4. Check for orphaned nationality_id (references not in nationalities table)
echo "\n4. CHECKING NATIONALITY_ID REFERENCES\n";
$orphanedNationality = DB::table('hr_expert_master_table')
    ->whereNotNull('nationality_id')
    ->where('nationality_id', '!=', '')
    ->leftJoin('nationalities', 'hr_expert_master_table.nationality_id', '=', 'nationalities.id')
    ->whereNull('nationalities.id')
    ->select('hr_expert_master_table.expert_id', 'hr_expert_master_table.name', 'hr_expert_master_table.nationality_id')
    ->get();

if ($orphanedNationality->count() > 0) {
    echo "   ⚠️  ORPHANED REFERENCES FOUND:\n";
    foreach ($orphanedNationality as $row) {
        echo "      expert_id: {$row->expert_id}, name: '{$row->name}', nationality_id: {$row->nationality_id} (NOT found in nationalities table)\n";
    }
} else {
    echo "   ✅ All nationality_id references are valid\n";
}

// 5. Check for orphaned organization_id
echo "\n5. CHECKING ORGANIZATION_ID REFERENCES\n";
$orphanedOrganization = DB::table('hr_expert_master_table')
    ->whereNotNull('organization_id')
    ->where('organization_id', '!=', '')
    ->leftJoin('hr_organization_table', 'hr_expert_master_table.organization_id', '=', 'hr_organization_table.organization_id')
    ->whereNull('hr_organization_table.organization_id')
    ->select('hr_expert_master_table.expert_id', 'hr_expert_master_table.name', 'hr_expert_master_table.organization_id')
    ->get();

if ($orphanedOrganization->count() > 0) {
    echo "   ⚠️  ORPHANED REFERENCES FOUND:\n";
    foreach ($orphanedOrganization as $row) {
        echo "      expert_id: {$row->expert_id}, name: '{$row->name}', organization_id: {$row->organization_id} (NOT found in hr_organization_table)\n";
    }
} else {
    echo "   ✅ All organization_id references are valid\n";
}

// 6. Check for orphaned industry_id
echo "\n6. CHECKING INDUSTRY_ID REFERENCES\n";
$orphanedIndustry = DB::table('hr_expert_master_table')
    ->whereNotNull('industry_id')
    ->where('industry_id', '!=', '')
    ->leftJoin('hr_industry_table', 'hr_expert_master_table.industry_id', '=', 'hr_industry_table.industry_id')
    ->whereNull('hr_industry_table.industry_id')
    ->select('hr_expert_master_table.expert_id', 'hr_expert_master_table.name', 'hr_expert_master_table.industry_id')
    ->get();

if ($orphanedIndustry->count() > 0) {
    echo "   ⚠️  ORPHANED REFERENCES FOUND:\n";
    foreach ($orphanedIndustry as $row) {
        echo "      expert_id: {$row->expert_id}, name: '{$row->name}', industry_id: {$row->industry_id} (NOT found in hr_industry_table)\n";
    }
} else {
    echo "   ✅ All industry_id references are valid\n";
}

// 7. Check designation column for common spelling mistakes
echo "\n7. CHECKING DESIGNATION COLUMN FOR SPACING/FORMATTING ISSUES\n";
$designations = DB::table('hr_expert_master_table')
    ->select('expert_id', 'name', 'designation')
    ->whereNotNull('designation')
    ->where('designation', '!=', '')
    ->get();

$issues = [];
foreach ($designations as $row) {
    $trimmed = trim($row->designation);
    // Check for leading/trailing spaces
    if ($row->designation !== $trimmed) {
        $issues[] = [
            'expert_id' => $row->expert_id,
            'name' => $row->name,
            'designation' => $row->designation,
            'issue' => 'Leading/trailing spaces'
        ];
    }
    // Check for multiple consecutive spaces
    if (preg_match('/\s{2,}/', $row->designation)) {
        $issues[] = [
            'expert_id' => $row->expert_id,
            'name' => $row->name,
            'designation' => $row->designation,
            'issue' => 'Multiple consecutive spaces'
        ];
    }
}

if (count($issues) > 0) {
    echo "   ⚠️  POTENTIAL ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "      expert_id: {$issue['expert_id']}, name: '{$issue['name']}'\n";
        echo "         designation: '{$issue['designation']}' ({$issue['issue']})\n";
    }
} else {
    echo "   ✅ No spacing/formatting issues found in designation column\n";
}

// 8. Show all unique designations for manual review
echo "\n8. UNIQUE DESIGNATIONS (for manual spelling review):\n";
$uniqueDesignations = DB::table('hr_expert_master_table')
    ->select('designation')
    ->whereNotNull('designation')
    ->where('designation', '!=', '')
    ->distinct()
    ->orderBy('designation')
    ->pluck('designation');

if ($uniqueDesignations->count() > 0) {
    foreach ($uniqueDesignations as $designation) {
        echo "   - $designation\n";
    }
} else {
    echo "   No designations found\n";
}

// 9. Show summary statistics
echo "\n9. SUMMARY STATISTICS\n";
$totalRecords = DB::table('hr_expert_master_table')->count();
$nullNationality = DB::table('hr_expert_master_table')->whereNull('nationality_id')->orWhere('nationality_id', '')->count();
$nullOrganization = DB::table('hr_expert_master_table')->whereNull('organization_id')->orWhere('organization_id', '')->count();
$nullIndustry = DB::table('hr_expert_master_table')->whereNull('industry_id')->orWhere('industry_id', '')->count();

echo "   Total records: $totalRecords\n";
echo "   Records with NULL/empty nationality_id: $nullNationality\n";
echo "   Records with NULL/empty organization_id: $nullOrganization\n";
echo "   Records with NULL/empty industry_id: $nullIndustry\n";

echo "\n========== END OF REPORT ==========\n";
