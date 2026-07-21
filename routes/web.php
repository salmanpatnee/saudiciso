<?php

use App\Http\Controllers\ArtifactAttachmentController;
use App\Http\Controllers\ArtifactController;
use App\Http\Controllers\BestPracticeController;
use App\Http\Controllers\CisoEducationAdminController;
use App\Http\Controllers\CisoEducationController;
use App\Http\Controllers\CisoToolkitController;
use App\Http\Controllers\CMS_ISO_27001Controller;
use App\Http\Controllers\CMSController;
use App\Http\Controllers\ControlAssessmentController;
use App\Http\Controllers\ControlAssessmentFindingController;
use App\Http\Controllers\ControlAuditFindingController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\ControlEvidenceController;
use App\Http\Controllers\ControlSmartSearch;
use App\Http\Controllers\ControlTypeController;
use App\Http\Controllers\DataUploaderController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\ExpertiseController;
use App\Http\Controllers\HotTopicController;
use App\Http\Controllers\HotTopicsController;
use App\Http\Controllers\HRCertificationController;
use App\Http\Controllers\HROrganizationController;
use App\Http\Controllers\HumanResourceController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\ISO27001Controller;
use App\Http\Controllers\ISO27001ResourceController;
use App\Http\Controllers\KPIStandardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainDomainController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\PeoplesController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProcessResourceController;
use App\Http\Controllers\ProductsAdminController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SubDomainController;
use App\Http\Controllers\TempFileUploadController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return response()->json(['message' => 'All caches cleared successfully.']);
})->name('clear-cache');

Route::view('/', 'welcome')->name('welcome');
Route::middleware(['guest'])->group(function () {
    Route::post('/contact-inquiry', [LeadController::class, 'store'])->name('contact.store');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('login.destroy');
    Route::resource('best-practices', BestPracticeController::class);
    Route::resource('domains', MainDomainController::class);
    Route::resource('sub-domains', SubDomainController::class);
    Route::resource('controls', ControlController::class);
    Route::resource('control-types', ControlTypeController::class);
    Route::resource('kpi-standards', KPIStandardController::class);

    // ------------------- ISO-27001 -------------------
    Route::get('/iso-27001', [ISO27001Controller::class, 'index'])->name('iso-27001.index');
    Route::get('/iso-27001/{section:section_id}', [ISO27001Controller::class, 'show'])->name('iso-27001.show');

    // ------------------- CONTROL SMART SEARCH -------------------

    Route::get('/control-smart-search', ControlSmartSearch::class)->name('control-smart-search.index');

    // ------------------- EVIDENCE TRACKING -------------------

    Route::resource('artifacts', ArtifactController::class);

    // Artifact Data Uploader
    Route::get('/upload-artifacts', [DataUploaderController::class, 'createArtifact'])->name('upload.artifact.create');
    Route::post('/upload-artifacts', [DataUploaderController::class, 'uploadArtifact'])->name('upload.artifact.store');

    Route::controller(TempFileUploadController::class)->group(function () {
        Route::post('/uploads', 'store')->name('temp.upload.store');
        Route::delete('/tmp/delete', 'destroy')->name('temp.upload.destroy');
    });

    Route::controller(ArtifactAttachmentController::class)->group(function () {
        Route::get('/attachments/{attachment}', 'show')->name('artifacts.attachments.show');
        Route::delete('/attachments/{attachment}', 'destroy')->name('artifacts.attachments.destroy');
    });

    // ------------------- EVIDENCE MANAGEMENT -------------------

    Route::resource('evidences', EvidenceController::class);
    Route::controller(EvidenceController::class)->group(function () {
        Route::get('/evidence-list/view/{evidence:evidence_id}', 'viewevilist')->name('evidence.view');
        Route::patch('/evidence-list/update_attachment', 'update_attachment')->name('evidence.update.attachment');
        Route::post('/evidence-list/delete-attachment', 'delete_attachment')->name('evidence.delete.attachment');
    });

    Route::controller(ControlEvidenceController::class)->group(function () {
        Route::get('/control-vs-evidence', 'controlVsEvidence')->name('control-vs-evidence.index');
        Route::get('/evidence-vs-control', 'evidenceVsControl')->name('evidence-vs-control.index');
        Route::get('/ajax/domains-by-best-practice/{bestPracticeId}', 'getDomainsByBestPractice')->name('ajax.domains.by.best.practice');
        Route::get('/ajax/subdomains-by-domain/{domainId}', 'getSubDomainsByDomain')->name('ajax.subdomains.by.domain');
    });

    Route::controller(ControlAuditFindingController::class)->group(function () {
        Route::get('/control-vs-audit-finding', 'controlVsAuditFinding')->name('control-vs-audit.index');
        Route::get('/audit-finding-vs-control', 'auditFindingVsControl')->name('audit-vs-control.index');
    });

    // ------------------- CONTROL ASSESSMENT -------------------

    Route::resource('control-assessments', ControlAssessmentController::class);
    Route::resource('control-assessment-findings', ControlAssessmentFindingController::class)->except(['index', 'create', 'store']);
    Route::controller(ControlAssessmentFindingController::class)->group(function () {
        Route::get('/control-assessment-findings/create/{controlAssessment}', 'create')->name('control-assessment-findings.create');
        Route::post('/control-assessment-findings/{controlAssessment}', 'store')->name('control-assessment-findings.store');
        Route::post('/evidence-conroller/', 'get_evidence_by_conroller');
    });

});

Route::middleware(['auth', 'must.change.password'])->group(function () {
    Route::view('/compliance', 'process/compliance')->name('compliance');
    Route::view('/vciso', 'vciso')->name('vciso');

    // ------------------- USERS -------------------

    // Profile update routes for non-admin users
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    Route::middleware('superadmin')->group(function () {
        Route::get('/user-activity', [UserActivityController::class, 'index'])->name('user-activity.index');

        Route::resource('users', UserController::class);
        Route::resource('hr-experts', HumanResourceController::class);

        // Industry Management Routes
        Route::resource('industries', IndustryController::class);

        // Expertise Management Routes
        Route::resource('expertises', ExpertiseController::class);

        // Designation Management Routes
        Route::resource('designations', DesignationController::class);
        Route::resource('nationalities', NationalityController::class);
        Route::resource('organizations', HROrganizationController::class);
        Route::resource('certifications', HRCertificationController::class);
        // Route::get('/options', [OptionsController::class, 'create'])->name('options.create');
        // Route::patch('/options', [OptionsController::class, 'update'])->name('options.update');
    });

    Route::view('/frameworks', 'process/framework')->name('frameworks');

    // ------------MANAGE GRC DOMAIN RESOURCES CONTENT--------------

    Route::resource('cms', CMSController::class);
    Route::get('/cms/create-resource/{process}', [ResourceController::class, 'create'])->name('resource.create');
    Route::post('/upload-resource', [ResourceController::class, 'store'])->name('resource.store');

    // ------------MANAGE CISO TOOLKIT--------------

    Route::resource('ciso-toolkit', CisoToolkitController::class)->except(['show'])->names('admin.ciso-toolkit');

    // ------------MANAGE HOT TOPICS--------------

    Route::resource('hot-topics', HotTopicController::class)->except(['show'])->names('admin.hot-topics');
    Route::delete('hot-topics-resource/{resource}', [HotTopicController::class, 'destroyResource'])->name('admin.hot-topics.resource.destroy');

    // ------------MANAGE CISO EDUCATION--------------

    Route::resource('ciso-education-content', CisoEducationAdminController::class)
        ->except(['show'])
        ->names('admin.ciso-education')
        ->parameters(['ciso-education-content' => 'cisoEducation']);

    // ------------MANAGE PRODUCTS--------------

    Route::resource('products-content', ProductsAdminController::class)
        ->except(['show'])
        ->names('admin.products')
        ->parameters(['products-content' => 'product']);

    // ------------MANAGE ISO-27001 CONTENT--------------

    Route::resource('iso27001', CMS_ISO_27001Controller::class);
    Route::get('/iso27001/create-resource/{section}', [ISO27001ResourceController::class, 'create'])->name('iso27001.resource.create');
    Route::post('/iso27001/upload-resource', [ISO27001ResourceController::class, 'store'])->name('iso27001.resource.store');

    // ------------ISO-27001 RESOURCES--------------

    Route::get('/iso27001/resource/{section:section_id}/checklist/', [ISO27001ResourceController::class, 'checklist'])->name('iso27001.resource.checklist');
    Route::get('/iso27001/resource/{section:section_id}/videos/', [ISO27001ResourceController::class, 'videos'])->name('iso27001.resource.videos');
    Route::get('/iso27001/resource/{section:section_id}/template/', [ISO27001ResourceController::class, 'template'])->name('iso27001.resource.template');
    Route::get('/iso27001/resource/{section:section_id}/glossary/', [ISO27001ResourceController::class, 'glossary'])->name('iso27001.resource.glossary');

    // ------------------CISO 360-------------------------

    Route::prefix('ciso')->group(function () {

        // ------------------CISO Toolkit-------------------------

        Route::get('/toolkit', function () {
            $toolkits = \App\Models\CisoToolkit::latest()->get();
            $toolkitsByCategory = $toolkits->groupBy(fn ($item) => $item->category?->value);
            $categories = \App\Enums\Category::cases();

            return view('ciso/ciso-toolkit/index', compact('toolkits', 'toolkitsByCategory', 'categories'));
        })->name('ciso-toolkit.index');

        // ------------------CISO Education-------------------------

        Route::get('/education', [CisoEducationController::class, 'index'])->name('ciso-education.index');
        Route::get('/education/{cisoEducation:slug}', [CisoEducationController::class, 'show'])->name('ciso-education.show');

        // ------------------Hot Topics-------------------------

        Route::get('/hot-topics', [HotTopicsController::class, 'index'])->name('hot-topics.index');

        Route::get('/hot-topics/{hotTopic}', [HotTopicsController::class, 'show'])->name('hot-topics.show');

        // ------------------People-------------------------

        Route::get('/peoples', PeoplesController::class)->name('people.index');
        // Route::get('/hr-experts/upload', [DataUploaderController::class, 'createHr'])->name('hr.upload');
        // Route::post('/hr-experts/upload', [DataUploaderController::class, 'UploadHr'])->name('hr.upload.store');

        // ------------------Process-------------------------

        Route::get('/process', [ProcessController::class, 'index'])->name('ciso-process.index');
        Route::get('/process/{process:slug}', [ProcessController::class, 'show'])->name('process.view.show');

        // ------------------Process Resources-------------------------

        Route::get('/resource/{process:slug}/checklist/', [ProcessResourceController::class, 'checklist'])->name('process.resource.checklist');

        Route::get('/resource/{process:slug}/videos/', [ProcessResourceController::class, 'videos'])->name('process.resource.videos');
        Route::get('/video/stream/{resource}', [ProcessResourceController::class, 'stream'])->name('secure.video.stream');
        Route::get('/resource/{process:slug}/template/', [ProcessResourceController::class, 'template'])->name('process.resource.template');
        Route::get('/resource/template/{resource}', [ProcessResourceController::class, 'pdfTemplate'])->name('process.resource.template.pdf');
        Route::get('/resource/{process:slug}/glossary/', [ProcessResourceController::class, 'glossary'])->name('process.resource.glossary');
        Route::get('/resource/download/{resource}', [ProcessResourceController::class, 'download'])->name('process.resource.download');
        Route::delete('/resources/{resource}', [ProcessResourceController::class, 'destroy'])->name('process.resource.destroy');

        // ------------------Products-------------------------

        Route::get('/products', [ProductsController::class, 'index'])->name('ciso-products.index');
        Route::get('/products/{product:slug}', [ProductsController::class, 'show'])->name('products.show');
    });

    // ------------------PITSTOP-------------------------

    // ------------------- TEMPORARY HR CERTIFICATION IMPORT -------------------

    Route::get('/import-hr-certifications', function () {
        try {
            $csvFilePath = base_path('data/Certifications_UNIQUE.csv');

            if (! file_exists($csvFilePath)) {
                return response()->json(['error' => 'CSV file not found'], 404);
            }

            $imported = 0;
            $errors = [];
            $rowNum = 0;

            if (($handle = fopen($csvFilePath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $rowNum++;

                    // Skip header row
                    if ($rowNum === 1) {
                        continue;
                    }

                    // Validate and prepare data
                    $certificationId = trim($data[0] ?? '');
                    $certificationTitle = trim($data[1] ?? '');
                    $institute = trim($data[2] ?? '');

                    if (empty($certificationId) || empty($certificationTitle)) {
                        $errors[] = "Row {$rowNum}: Missing certification_id or certification_title";

                        continue;
                    }

                    try {
                        \App\Models\HRCertification::create([
                            'certification_id' => $certificationId,
                            'certification_title' => $certificationTitle,
                            'institute' => empty($institute) ? null : $institute,
                        ]);
                        $imported++;
                    } catch (\Exception $e) {
                        $errors[] = "Row {$rowNum}: ".$e->getMessage();
                    }
                }

                fclose($handle);

                return response()->json([
                    'success' => true,
                    'imported' => $imported,
                    'errors' => $errors,
                    'total_errors' => count($errors),
                ]);
            } else {
                return response()->json(['error' => 'Unable to open file'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('import.hr-certifications');

});
