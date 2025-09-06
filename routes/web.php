<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\PsqcController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\GithubController;

use App\Livewire\Home;
use App\Livewire\HomeCertified;
use App\Livewire\HomePsqcCertified;

// Performance routes
use App\Livewire\HomePerformanceSpeed;
use App\Livewire\HomePerformanceLoad;
use App\Livewire\HomePerformanceMobile;

// Security routes
use App\Livewire\HomeSecuritySsl;
use App\Livewire\HomeSecuritySslyze;
use App\Livewire\HomeSecurityHeaders;
use App\Livewire\HomeSecurityScan;
use App\Livewire\HomeSecurityNuclei;

// Quality routes
use App\Livewire\HomeQualityLighthouse;
use App\Livewire\HomeQualityAccessibility;
use App\Livewire\HomeQualityCompatibility;
use App\Livewire\HomeQualityVisual;

// Content routes
use App\Livewire\HomeContentLinks;
use App\Livewire\HomeContentStructure;
use App\Livewire\HomeContentCrawl;
use App\Livewire\HomeContentMeta;

use App\Livewire\HomePricing;
use App\Livewire\HomeCertificate;
use App\Livewire\HomeCertificateCheckout;
use App\Livewire\HomeRanking;

use App\Livewire\HomePsqcCheckout;

/* Auto-HomeController */
use App\Livewire\HomeRequest;
use App\Livewire\HomePrivacy;
use App\Livewire\HomeTerms;

use App\Livewire\ClientPsqc;
use App\Livewire\ClientCertificate;
use App\Livewire\ClientPlan;
use App\Livewire\ClientPurchase;
use App\Livewire\ClientProfile;
use App\Livewire\ClientPassword;

use App\Livewire\AdminDashboard;
use App\Livewire\AdminAccountPassword;
use App\Livewire\AdminAccountProfile;
use App\Livewire\AdminSettingBranding;
use App\Livewire\AdminSettingInformation;
use App\Livewire\AdminSettingSeo;
use App\Livewire\AdminSettingApi;
use App\Livewire\AdminSettingPrivacy;
use App\Livewire\AdminSettingTerms;
use App\Livewire\AdminDevelopmentNavbar;
use App\Livewire\AdminDevelopmentMenu;
use App\Livewire\AdminDevelopmentDatabase;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Livewire\AdminDevelopmentPhp;
use App\Livewire\AdminDevelopmentBackup;
use App\Livewire\AdminUser;

/* Auto-Controller */

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::prefix('google')->name('google.')->group( function(){
    Route::get('login', [GoogleController::class, 'loginWithGoogle'])->name('login');
    Route::any('callback', [GoogleController::class, 'callbackFromGoogle'])->name('callback');
});

Route::prefix('github')->name('github.')->group( function(){
    Route::get('login', [GithubController::class, 'loginWithGithub'])->name('login');
    Route::any('callback', [GithubController::class, 'callbackFromGithub'])->name('callback');
});

// Visitor
Route::get('/', Home::class)->name('home');

Route::get('/{code}/certified', HomeCertified::class)->name('certified');
Route::get('/{code}/pdf', [IndexController::class, 'pdf'])->name('certificates.pdf');

Route::get('/certificates/{code}.pdf', function (string $code) {
    $rel = "certification/{$code}.pdf";
    abort_unless(Storage::disk('local')->exists($rel), 404, 'PDF not found');
    // 권한 체크 등 넣고 싶으면 여기서 수행
    return response()->file(Storage::disk('local')->path($rel), [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"{$code}.pdf\"",
    ]);
})->name('cert.pdf.download');

Route::get('/{code}/psqc/certified', HomePsqcCertified::class)->name('psqc.certified');
Route::get('/{code}/psqc/pdf', [IndexController::class, 'psqc_pdf'])->name('psqc.pdf');

Route::get('/psqc/{code}.pdf', function (string $code) {
    $rel = "psqc-certification/{$code}.pdf";
    abort_unless(Storage::disk('local')->exists($rel), 404, 'PDF not found');
    // 권한 체크 등 넣고 싶으면 여기서 수행
    return response()->file(Storage::disk('local')->path($rel), [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => "inline; filename=\"{$code}.pdf\"",
    ]);
})->name('cert.psqc.download');

// Performance routes
Route::get('/performance/speed', HomePerformanceSpeed::class)->name('performance.speed');
Route::get('/performance/load', HomePerformanceLoad::class)->name('performance.load');
Route::get('/performance/mobile', HomePerformanceMobile::class)->name('performance.mobile');

// Security routes
Route::get('/security/ssl', HomeSecuritySsl::class)->name('security.ssl');
Route::get('/security/sslyze', HomeSecuritySslyze::class)->name('security.sslyze');
Route::get('/security/header', HomeSecurityHeaders::class)->name('security.header');
Route::get('/security/scan', HomeSecurityScan::class)->name('security.scan');
Route::get('/security/nuclei', HomeSecurityNuclei::class)->name('security.nuclei');

// Quality routes
Route::get('/quality/lighthouse', HomeQualityLighthouse::class)->name('quality.lighthouse');
Route::get('/quality/accessibility', HomeQualityAccessibility::class)->name('quality.accessibility');
Route::get('/quality/compatibility', HomeQualityCompatibility::class)->name('quality.compatibility');
Route::get('/quality/visual', HomeQualityVisual::class)->name('quality.visual');

// Content routes
Route::get('/content/links', HomeContentLinks::class)->name('content.links');
Route::get('/content/structure', HomeContentStructure::class)->name('content.structure');
Route::get('/content/crawl', HomeContentCrawl::class)->name('content.crawl');
Route::get('/content/meta', HomeContentMeta::class)->name('content.meta');

Route::get('/pricing', HomePricing::class);
Route::get('/certificate', HomeCertificate::class);
Route::get('/ranking', HomeRanking::class);
/* Auto-HomeMenu */
Route::get('/request', HomeRequest::class);
Route::get('/privacy', HomePrivacy::class);
Route::get('/terms', HomeTerms::class);

Route::get('/logout', [IndexController::class, 'logout']);

// Client
Route::middleware(['auth'])->group(function () {
    Route::get('/client/psqc', ClientPsqc::class)->name('client.psqc');
    Route::get('/client/certificate', ClientCertificate::class)->name('client.certificate');
    Route::get('/client/plan', ClientPlan::class)->name('client.plan');
    Route::get('/client/purchase', ClientPurchase::class)->name('client.purchase');
    Route::get('/client/profile', ClientProfile::class)->name('client.profile');
    Route::get('/client/password', ClientPassword::class)->name('client.password');

    Route::get('/certificate/checkout/{certificate}', HomeCertificateCheckout::class)->name('certificate.checkout');
    Route::get('/certificate/download/{certificate}', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/certificate/payment/success', [CertificateController::class, 'paymentSuccess'])->name('certificate.payment.success');
    Route::get('/certificate/payment/fail', [CertificateController::class, 'paymentFail'])->name('certificate.payment.fail');

    Route::get('/plan/payment/success', [PlanController::class, 'paymentSuccess'])->name('plan.payment.success');
    Route::get('/plan/payment/fail', [PlanController::class, 'paymentFail'])->name('plan.payment.fail');

    Route::get('/psqc/checkout/{certificate}', HomePsqcCheckout::class)->name('psqc.checkout');
    Route::get('/psqc/download/{certificate}', [PsqcController::class, 'download'])->name('psqc.download');
    Route::get('/psqc/payment/success', [PsqcController::class, 'paymentSuccess'])->name('psqc.payment.success');
    Route::get('/psqc/payment/fail', [PsqcController::class, 'paymentFail'])->name('psqc.payment.fail');
});

// Admin
Route::middleware(['auth','check_admin'])->group(function () {

    Route::get('/admin/dashboard', AdminDashboard::class);
    Route::get('/admin/account/profile', AdminAccountProfile::class);
    Route::get('/admin/account/password', AdminAccountPassword::class);
    Route::get('/admin/logout', function () {
        auth()->logout();
        return redirect('/');
    });
    /* Start-Auto-Route */

    /* End-Auto-Route */
    
    /* User */
    Route::get('/admin/user', AdminUser::class);

    /* Settings */
    Route::get('/admin/setting/branding', AdminSettingBranding::class);
    Route::get('/admin/setting/information', AdminSettingInformation::class);
    Route::get('/admin/setting/seo', AdminSettingSeo::class);
    Route::get('/admin/setting/api', AdminSettingApi::class);
    Route::get('/admin/setting/privacy', AdminSettingPrivacy::class);
    Route::get('/admin/setting/terms', AdminSettingTerms::class);

    /* Development */
    Route::get('/admin/development/navbar', AdminDevelopmentNavbar::class);
    Route::get('/admin/development/menu', AdminDevelopmentMenu::class);
    Route::get('/admin/development/database', AdminDevelopmentDatabase::class);
    Route::get('/admin/development/logs', [LogViewerController::class, 'index']);
    Route::get('/admin/development/php', AdminDevelopmentPhp::class);
    Route::get('/admin/phpinfo/raw', function () {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();
        return response($phpinfo)->header('Content-Type', 'text/html');
    });
    Route::get('/admin/development/backup', AdminDevelopmentBackup::class);

    Route::get('/admin/backup/download/{filename}', function ($filename) {
        $path = env('APP_NAME') . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    })->name('admin.backup.download');

});