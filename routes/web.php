<?php

use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MembreController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\DiagnosticentrepriseController;
use App\Http\Controllers\DiagnosticentrepriseQualificationController;
use App\Http\Controllers\EspaceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\ExpertPlanController;
use App\Http\Controllers\PropositionController;
use App\Http\Controllers\DisponibiliteController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\SujetController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\CotisationController;
use App\Http\Controllers\CotisationressourceController;
use App\Http\Controllers\ConseillerController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\AccompagnementController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\BonController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\PrestationrealiseeController;
use App\Http\Controllers\BonutiliseController;
use App\Http\Controllers\RessourcecompteController;
use App\Http\Controllers\PrestationressourceController;
use App\Http\Controllers\FormationressourceController;
use App\Http\Controllers\EvenementressourceController;
use App\Http\Controllers\EspaceressourceController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\ParrainageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizquestionController;
use App\Http\Controllers\QuizreponseController;
use App\Http\Controllers\RecompenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PropositionMembreController;

use App\Http\Controllers\RessourceSyncController;

Route::post('/ressourcecomptes/sync', [RessourceSyncController::class, 'syncToSupabase'])
    ->name('ressourcecomptes.sync')
    ->middleware('auth');
    
use App\Http\Controllers\SyncController;

Route::middleware(['auth'])->group(function () {
    Route::post('/sync/supabase', [SyncController::class, 'triggerSync'])
        ->name('sync.supabase');
});


// Callback SEMOA (public, pas de CSRF)
Route::post('bons/ressourcecompte/{id}/callback', 
    [\App\Http\Controllers\RessourcecompteController::class, 'callback']
)->name('ressourcecompte.callback');


/*use Illuminate\Support\Facades\Mail;

Route::get('/test-mail', function () {
    try {
        Mail::raw('Ceci est un test depuis Laravel local.', function ($message) {
            $message->to('yokamly@gmail.com')
                    ->subject('Test Mail CIJES Africa');
        });

        return '✅ Mail envoyé avec succès !';
    } catch (\Exception $e) {
        return '❌ Erreur : ' . $e->getMessage();
    }
});*/


/*Route::get('/test-mail-recompense', function () {
    // Récupérer un membre pour le test (remplacez l'ID par un vrai membre)
    $membre = App\Models\Membre::first(); // ou Membre::find(1);
    if (!$membre) {
        return "Aucun membre trouvé pour le test.";
    }

    // Créer un objet Action fictif
    $action = new App\Models\Action([
        'titre' => 'Test Action',
        'point' => 50,
    ]);

    // Créer un objet Recompense fictif
    $recompense = new App\Models\Recompense([
        'updated_at' => Carbon\Carbon::now(),
    ]);

    try {
        Mail::send('emails.recompense', [
            'membre' => $membre,
            'action' => $action,
            'recompense' => $recompense,
        ], function ($message) use ($membre) {
            $message->to($membre->email ?? 'yokamly@gmail.com')
                    ->subject('🎁 Nouvelle récompense obtenue - CIJES Africa');
        });

        return "Mail envoyé avec succès à : " . ($membre->email ?? 'yokamly@gmail.com');

    } catch (\Exception $e) {
        \Log::error('Erreur envoi mail test : ' . $e->getMessage());
        return "Erreur lors de l'envoi du mail : " . $e->getMessage();
    }
});*/


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'guest'])->group(function () {
//Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'loginView'])->name('loginView');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'registerView'])->name('registerView');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');

    // Routes plateforme et plateforme provider
    Route::get('/plateforme', [\App\Http\Controllers\AuthController::class, 'plateformeView'])->name('plateforme');
    Route::get('/plateforme/provider', [\App\Http\Controllers\AuthController::class, 'plateformeProviderView'])->name('plateforme.provider');

    Route::get('/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPasswordView'])->name('forgotPasswordView');
    Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPassword'])->name('forgotPassword');

    Route::get('/reset-password', [\App\Http\Controllers\AuthController::class, 'resetPasswordView'])->name('resetPasswordView');
    Route::post('/reset-password', [\App\Http\Controllers\AuthController::class, 'resetPassword'])->name('resetPassword');
    
});

//Route::match(['get', 'post'], 'bons/ressourcecompte/{transactionId}/callback', [RessourcecompteController::class, 'callback'])
//    ->name('ressourcecompte.callback');



Route::get('/emails/verify', function () {
    return view('auth.verify-success');
})->name('emails.verify');


Route::get('/test-supabase-register', function () {
    $supabase = new App\Services\SupabaseService(); // selon ton chemin
    $result = $supabase->signUp('test@example.com', 'Password123', ['full_name' => 'Test User'], 'http://localhost:8000/emails/verify');
    dd($result);
});


//Route::middleware(['web', 'auth'])->group(function () {
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    //Route::get('/dashboard', [PagesController::class, 'dashboardsCrmAnalytics'])->name('index');
    
    Route::get('/membres/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('/geographie/pays', [PagesController::class, 'geographiePays'])->name('geographie/pays');
    
    Route::get('/elements/avatar', [PagesController::class, 'elementsAvatar'])->name('elements/avatar');
    Route::get('/elements/alert', [PagesController::class, 'elementsAlert'])->name('elements/alert');
    Route::get('/elements/button', [PagesController::class, 'elementsButton'])->name('elements/button');
    Route::get('/elements/button-group', [PagesController::class, 'elementsButtonGroup'])->name('elements/button-group');
    Route::get('/elements/badge', [PagesController::class, 'elementsBadge'])->name('elements/badge');
    Route::get('/elements/breadcrumb', [PagesController::class, 'elementsBreadcrumb'])->name('elements/breadcrumb');
    Route::get('/elements/card', [PagesController::class, 'elementsCard'])->name('elements/card');
    Route::get('/elements/divider', [PagesController::class, 'elementsDivider'])->name('elements/divider');
    Route::get('/elements/mask', [PagesController::class, 'elementsMask'])->name('elements/mask');
    Route::get('/elements/progress', [PagesController::class, 'elementsProgress'])->name('elements/progress');
    Route::get('/elements/skeleton', [PagesController::class, 'elementsSkeleton'])->name('elements/skeleton');
    Route::get('/elements/spinner', [PagesController::class, 'elementsSpinner'])->name('elements/spinner');
    Route::get('/elements/tag', [PagesController::class, 'elementsTag'])->name('elements/tag');
    Route::get('/elements/tooltip', [PagesController::class, 'elementsTooltip'])->name('elements/tooltip');
    Route::get('/elements/typography', [PagesController::class, 'elementsTypography'])->name('elements/typography');

    Route::get('/components/accordion', [PagesController::class, 'componentsAccordion'])->name('components/accordion');
    Route::get('/components/collapse', [PagesController::class, 'componentsCollapse'])->name('components/collapse');
    Route::get('/components/tab', [PagesController::class, 'componentsTab'])->name('components/tab');
    Route::get('/components/dropdown', [PagesController::class, 'componentsDropdown'])->name('components/dropdown');
    Route::get('/components/popover', [PagesController::class, 'componentsPopover'])->name('components/popover');
    Route::get('/components/modal', [PagesController::class, 'componentsModal'])->name('components/modal');
    Route::get('/components/drawer', [PagesController::class, 'componentsDrawer'])->name('components/drawer');
    Route::get('/components/steps', [PagesController::class, 'componentsSteps'])->name('components/steps');
    Route::get('/components/timeline', [PagesController::class, 'componentsTimeline'])->name('components/timeline');
    Route::get('/components/pagination', [PagesController::class, 'componentsPagination'])->name('components/pagination');
    Route::get('/components/menu-list', [PagesController::class, 'componentsMenuList'])->name('components/menu-list');
    Route::get('/components/treeview', [PagesController::class, 'componentsTreeview'])->name('components/treeview');
    Route::get('/components/table', [PagesController::class, 'componentsTable'])->name('components/table');
    Route::get('/components/table-advanced', [PagesController::class, 'componentsTableAdvanced'])->name('components/table-advanced');
    Route::get('/components/table-gridjs', [PagesController::class, 'componentsTableGridjs'])->name('components/gridjs');
    Route::get('/components/apexchart', [PagesController::class, 'componentsApexchart'])->name('components/apexchart');
    Route::get('/components/carousel', [PagesController::class, 'componentsCarousel'])->name('components/carousel');
    Route::get('/components/notification', [PagesController::class, 'componentsNotification'])->name('components/notification');
    Route::get('/components/extension-clipboard', [PagesController::class, 'componentsExtensionClipboard'])->name('components/extension-clipboard');
    Route::get('/components/extension-persist', [PagesController::class, 'componentsExtensionPersist'])->name('components/extension-persist');
    Route::get('/components/extension-monochrome', [PagesController::class, 'componentsExtensionMonochrome'])->name('components/extension-monochrome');

    Route::get('/forms/layout-v1', [PagesController::class, 'formsLayoutV1'])->name('forms/layout-v1');
    Route::get('/forms/layout-v2', [PagesController::class, 'formsLayoutV2'])->name('forms/layout-v2');
    Route::get('/forms/layout-v3', [PagesController::class, 'formsLayoutV3'])->name('forms/layout-v3');
    Route::get('/forms/layout-v4', [PagesController::class, 'formsLayoutV4'])->name('forms/layout-v4');
    Route::get('/forms/layout-v5', [PagesController::class, 'formsLayoutV5'])->name('forms/layout-v5');
    Route::get('/forms/input-text', [PagesController::class, 'formsInputText'])->name('forms/input-text');
    Route::get('/forms/input-group', [PagesController::class, 'formsInputGroup'])->name('forms/input-group');
    Route::get('/forms/input-mask', [PagesController::class, 'formsInputMask'])->name('forms/input-mask');
    Route::get('/forms/checkbox', [PagesController::class, 'formsCheckbox'])->name('forms/checkbox');
    Route::get('/forms/radio', [PagesController::class, 'formsRadio'])->name('forms/radio');
    Route::get('/forms/switch', [PagesController::class, 'formsSwitch'])->name('forms/switch');
    Route::get('/forms/select', [PagesController::class, 'formsSelect'])->name('forms/select');
    Route::get('/forms/tom-select', [PagesController::class, 'formsTomSelect'])->name('forms/tom-select');
    Route::get('/forms/textarea', [PagesController::class, 'formsTextarea'])->name('forms/textarea');
    Route::get('/forms/range', [PagesController::class, 'formsRange'])->name('forms/range');
    Route::get('/forms/datepicker', [PagesController::class, 'formsDatepicker'])->name('forms/datepicker');
    Route::get('/forms/timepicker', [PagesController::class, 'formsTimepicker'])->name('forms/timepicker');
    Route::get('/forms/datetimepicker', [PagesController::class, 'formsDatetimepicker'])->name('forms/datetimepicker');
    Route::get('/forms/text-editor', [PagesController::class, 'formsTextEditor'])->name('forms/text-editor');
    Route::get('/forms/upload', [PagesController::class, 'formsUpload'])->name('forms/upload');
    Route::get('/forms/validation', [PagesController::class, 'formsValidation'])->name('forms/validation');

    Route::get('/layouts/onboarding-1', [PagesController::class, 'layoutsOnboarding1'])->name('layouts/onboarding-1');
    Route::get('/layouts/onboarding-2', [PagesController::class, 'layoutsOnboarding2'])->name('layouts/onboarding-2');
    Route::get('/layouts/user-card-1', [PagesController::class, 'layoutsUserCard1'])->name('layouts/user-card-1');
    Route::get('/layouts/user-card-2', [PagesController::class, 'layoutsUserCard2'])->name('layouts/user-card-2');
    Route::get('/layouts/user-card-3', [PagesController::class, 'layoutsUserCard3'])->name('layouts/user-card-3');
    Route::get('/layouts/user-card-4', [PagesController::class, 'layoutsUserCard4'])->name('layouts/user-card-4');
    Route::get('/layouts/user-card-5', [PagesController::class, 'layoutsUserCard5'])->name('layouts/user-card-5');
    Route::get('/layouts/user-card-6', [PagesController::class, 'layoutsUserCard6'])->name('layouts/user-card-6');
    Route::get('/layouts/user-card-7', [PagesController::class, 'layoutsUserCard7'])->name('layouts/user-card-7');
    Route::get('/layouts/blog-card-1', [PagesController::class, 'layoutsBlogCard1'])->name('layouts/blog-card-1');
    Route::get('/layouts/blog-card-2', [PagesController::class, 'layoutsBlogCard2'])->name('layouts/blog-card-2');
    Route::get('/layouts/blog-card-3', [PagesController::class, 'layoutsBlogCard3'])->name('layouts/blog-card-3');
    Route::get('/layouts/blog-card-4', [PagesController::class, 'layoutsBlogCard4'])->name('layouts/blog-card-4');
    Route::get('/layouts/blog-card-5', [PagesController::class, 'layoutsBlogCard5'])->name('layouts/blog-card-5');
    Route::get('/layouts/blog-card-6', [PagesController::class, 'layoutsBlogCard6'])->name('layouts/blog-card-6');
    Route::get('/layouts/blog-card-7', [PagesController::class, 'layoutsBlogCard7'])->name('layouts/blog-card-7');
    Route::get('/layouts/blog-card-8', [PagesController::class, 'layoutsBlogCard8'])->name('layouts/blog-card-8');
    Route::get('/layouts/blog-details', [PagesController::class, 'layoutsBlogDetails'])->name('layouts/blog-details');
    Route::get('/layouts/help-1', [PagesController::class, 'layoutsHelp1'])->name('layouts/help-1');
    Route::get('/layouts/help-2', [PagesController::class, 'layoutsHelp2'])->name('layouts/help-2');
    Route::get('/layouts/help-3', [PagesController::class, 'layoutsHelp3'])->name('layouts/help-3');
    Route::get('/layouts/price-list-1', [PagesController::class, 'layoutsPriceList1'])->name('layouts/price-list-1');
    Route::get('/layouts/price-list-2', [PagesController::class, 'layoutsPriceList2'])->name('layouts/price-list-2');
    Route::get('/layouts/price-list-3', [PagesController::class, 'layoutsPriceList3'])->name('layouts/price-list-3');
    Route::get('/layouts/price-list-4', [PagesController::class, 'layoutsPriceList4'])->name('layouts/price-list-4');
    Route::get('/layouts/invoice-1', [PagesController::class, 'layoutsInvoice1'])->name('layouts/invoice-1');
    Route::get('/layouts/invoice-2', [PagesController::class, 'layoutsInvoice2'])->name('layouts/invoice-2');
    Route::get('/layouts/sign-in-1', [PagesController::class, 'layoutsSignIn1'])->name('layouts/sign-in-1');
    Route::get('/layouts/sign-in-2', [PagesController::class, 'layoutsSignIn2'])->name('layouts/sign-in-2');
    Route::get('/layouts/sign-up-1', [PagesController::class, 'layoutsSignUp1'])->name('layouts/sign-up-1');
    Route::get('/layouts/sign-up-2', [PagesController::class, 'layoutsSignUp2'])->name('layouts/sign-up-2');
    Route::get('/layouts/error-404-1', [PagesController::class, 'layoutsError4041'])->name('layouts/error-404-1');
    Route::get('/layouts/error-404-2', [PagesController::class, 'layoutsError4042'])->name('layouts/error-404-2');
    Route::get('/layouts/error-404-3', [PagesController::class, 'layoutsError4043'])->name('layouts/error-404-3');
    Route::get('/layouts/error-404-4', [PagesController::class, 'layoutsError4044'])->name('layouts/error-404-4');
    Route::get('/layouts/error-401', [PagesController::class, 'layoutsError401'])->name('layouts/error-401');
    Route::get('/layouts/error-429', [PagesController::class, 'layoutsError429'])->name('layouts/error-429');
    Route::get('/layouts/error-500', [PagesController::class, 'layoutsError500'])->name('layouts/error-500');
    Route::get('/layouts/starter-blurred-header', [PagesController::class, 'layoutsStarterBlurredHeader'])->name('layouts/starter-blurred-header');
    Route::get('/layouts/starter-unblurred-header', [PagesController::class, 'layoutsStarterUnblurredHeader'])->name('layouts/starter-unblurred-header');
    Route::get('/layouts/starter-centered-link', [PagesController::class, 'layoutsStarterCenteredLink'])->name('layouts/starter-centered-link');
    Route::get('/layouts/starter-minimal-sidebar', [PagesController::class, 'layoutsStarterMinimalSidebar'])->name('layouts/starter-minimal-sidebar');
    Route::get('/layouts/starter-sideblock', [PagesController::class, 'layoutsStarterSideblock'])->name('layouts/starter-sideblock');

    Route::get('/apps/chat', [PagesController::class, 'appsChat'])->name('apps/chat');
    Route::get('/apps/ai-chat', [PagesController::class, 'appsAiChat'])->name('apps/ai-chat');
    Route::get('/apps/filemanager', [PagesController::class, 'appsFilemanager'])->name('apps/filemanager');
    Route::get('/apps/kanban', [PagesController::class, 'appsKanban'])->name('apps/kanban');
    Route::get('/apps/list', [PagesController::class, 'appsList'])->name('apps/list');
    Route::get('/apps/mail', [PagesController::class, 'appsMail'])->name('apps/mail');
    Route::get('/apps/nft-1', [PagesController::class, 'appsNft1'])->name('apps/nft1');
    Route::get('/apps/nft-2', [PagesController::class, 'appsNft2'])->name('apps/nft2');
    Route::get('/apps/pos', [PagesController::class, 'appsPos'])->name('apps/pos');
    Route::get('/apps/todo', [PagesController::class, 'appsTodo'])->name('apps/todo');
    Route::get('/apps/jobs-board', [PagesController::class, 'appsJobsBoard'])->name('apps/jobs-board');
    Route::get('/apps/travel', [PagesController::class, 'appsTravel'])->name('apps/travel');

    Route::get('/dashboards/crm-analytics', [PagesController::class, 'dashboardsCrmAnalytics'])->name('dashboards/crm-analytics');
    Route::get('/dashboards/orders', [PagesController::class, 'dashboardsOrders'])->name('dashboards/orders');
    Route::get('/dashboards/crypto-1', [PagesController::class, 'dashboardsCrypto1'])->name('dashboards/crypto-1');
    Route::get('/dashboards/crypto-2', [PagesController::class, 'dashboardsCrypto2'])->name('dashboards/crypto-2');
    Route::get('/dashboards/banking-1', [PagesController::class, 'dashboardsBanking1'])->name('dashboards/banking-1');
    Route::get('/dashboards/banking-2', [PagesController::class, 'dashboardsBanking2'])->name('dashboards/banking-2');
    Route::get('/dashboards/personal', [PagesController::class, 'dashboardsPersonal'])->name('dashboards/personal');
    Route::get('/dashboards/cms-analytics', [PagesController::class, 'dashboardsCmsAnalytics'])->name('dashboards/cms-analytics');
    Route::get('/dashboards/influencer', [PagesController::class, 'dashboardsInfluencer'])->name('dashboards/influencer');
    Route::get('/dashboards/travel', [PagesController::class, 'dashboardsTravel'])->name('dashboards/travel');
    Route::get('/dashboards/teacher', [PagesController::class, 'dashboardsTeacher'])->name('dashboards/teacher');
    Route::get('/dashboards/education', [PagesController::class, 'dashboardsEducation'])->name('dashboards/education');
    Route::get('/dashboards/authors', [PagesController::class, 'dashboardsAuthors'])->name('dashboards/authors');
    Route::get('/dashboards/doctor', [PagesController::class, 'dashboardsDoctor'])->name('dashboards/doctor');
    Route::get('/dashboards/employees', [PagesController::class, 'dashboardsEmployees'])->name('dashboards/employees');
    Route::get('/dashboards/workspaces', [PagesController::class, 'dashboardsWorkspaces'])->name('dashboards/workspaces');
    Route::get('/dashboards/meetings', [PagesController::class, 'dashboardsMeetings'])->name('dashboards/meetings');
    Route::get('/dashboards/project-boards', [PagesController::class, 'dashboardsProjectBoards'])->name('dashboards/project-boards');
    Route::get('/dashboards/widget-ui', [PagesController::class, 'dashboardsWidgetUi'])->name('dashboards/widget-ui');
    Route::get('/dashboards/widget-contacts', [PagesController::class, 'dashboardsWidgetContacts'])->name('dashboards/widget-contacts');





    Route::get('/membres/mon-profil', [MembreController::class, 'createOrEdit'])->name('membre.createOrEdit');
    Route::post('/membres/mon-profil', [MembreController::class, 'storeOrUpdate'])->name('membre.storeOrUpdate');
    
    Route::get('/membres/mes-documents', [DocumentController::class, 'indexForm'])->name('documents.form');
    Route::post('/membres/mes-documents', [DocumentController::class, 'storeOrUpdateDocuments'])->name('documents.store');
    
    Route::get('/entreprises/mes-pieces', [PieceController::class, 'indexForm'])->name('pieces.form');
    Route::post('/entreprises/mes-pieces', [PieceController::class, 'storeOrUpdatePieces'])->name('pieces.store');

    Route::get('/entreprises/entreprise/create', [EntrepriseController::class, 'create'])->name('entreprise.create');
    Route::get('/entreprises/entreprise/{id}/edit', [EntrepriseController::class, 'edit'])->name('entreprise.edit');
    Route::post('/entreprises/entreprise', [EntrepriseController::class, 'store'])->name('entreprise.store');
    Route::put('/entreprises/entreprise/{id}', [EntrepriseController::class, 'update'])->name('entreprise.update');
    Route::get('/entreprises/entreprises', [EntrepriseController::class, 'index'])->name('entreprise.index');
    Route::delete('/entreprises/entreprise/{id}/destroy', [EntrepriseController::class, 'destroy'])->name('entreprise.destroy');

    Route::get('/diagnostics/diagnostic', [DiagnosticController::class, 'showForm'])->name('diagnostic.form');
    Route::post('/diagnostics/diagnostic', [DiagnosticController::class, 'store'])->name('diagnostic.store');
    Route::get('/diagnostics/diagnostic/success', function () {
    return view('diagnostic.success');
    })->name('diagnostic.success');
    Route::get('/diagnostics/diagnostic/{diagnosticId}/plans', [DiagnosticController::class, 'listePlans'])->name('diagnostic.plans');
   
    Route::get('/diagnostics/diagnosticentreprise', [DiagnosticentrepriseController::class, 'indexForm'])->name('diagnosticentreprise.indexForm');
    Route::get('/diagnostics/diagnosticentreprise/choix-entreprise', [DiagnosticentrepriseController::class, 'choix_entreprise'])->name('diagnosticentreprise.choix_entreprise');
    Route::get('/diagnostics/diagnosticentreprise/{entrepriseId}/form', [DiagnosticentrepriseController::class, 'showForm'])->name('diagnosticentreprise.showForm');
    Route::post('/diagnostics/diagnosticentreprise/store', [DiagnosticentrepriseController::class, 'store'])->name('diagnosticentreprise.store');
    Route::get('/diagnostics/diagnosticentreprise/success', function() {
        return view('diagnosticentreprise.success');
    })->name('diagnosticentreprise.success');
    Route::get('/diagnostics/diagnosticentreprise/{diagnosticId}/plans', [DiagnosticentrepriseController::class, 'listePlans'])->name('diagnosticentreprise.plans');

    // Routes pour le test de qualification (diagnosticmoduletype_id = 3)
    Route::get('/diagnostics/diagnosticentreprise-qualification', [DiagnosticentrepriseQualificationController::class, 'indexForm'])->name('diagnosticentreprisequalification.indexForm');
    Route::get('/diagnostics/diagnosticentreprise-qualification/{entrepriseId}/form', [DiagnosticentrepriseQualificationController::class, 'showForm'])->name('diagnosticentreprisequalification.showForm');
    Route::get('/diagnostics/diagnosticentreprise-qualification/{entrepriseId}/form/{moduleId}', [DiagnosticentrepriseQualificationController::class, 'showForm'])->name('diagnosticentreprisequalification.showModule');
    Route::post('/diagnostics/diagnosticentreprise-qualification/{entrepriseId}/save/{moduleId}', [DiagnosticentrepriseQualificationController::class, 'saveModule'])->name('diagnosticentreprisequalification.saveModule');
    Route::post('/diagnostics/diagnosticentreprise-qualification/{entrepriseId}/store/{moduleId}', [DiagnosticentrepriseQualificationController::class, 'store'])->name('diagnosticentreprisequalification.store');
    Route::get('/diagnostics/diagnosticentreprise-qualification/{entrepriseId}/results', [DiagnosticentrepriseQualificationController::class, 'results'])->name('diagnosticentreprisequalification.results');
    Route::get('/diagnostics/diagnosticentreprise-qualification/success', [DiagnosticentrepriseQualificationController::class, 'success'])->name('diagnosticentreprisequalification.success');


    Route::get('/evenements/espaces', [EspaceController::class, 'index'])->name('espace.index');
    Route::get('/evenements/espace/{id}', [EspaceController::class, 'show'])->name('espace.show');
    Route::get('/evenements/reservation/{espace}/create', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/evenements/reservation/{id}/store', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/evenements/mes-reservations', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/evenements/espaces/{id}/reserver', [EspaceController::class, 'reserverForm'])->name('espace.reserver.form');
    Route::post('/evenements/espaces/{id}/reserver', [EspaceController::class, 'reserverStore'])->name('espace.reserver.store');
    Route::post('/evenements/espaces/{id}/calculer', [EspaceController::class, 'calculerMontant'])->name('espace.calculer.montant');

    Route::get('/experts/expert/devenir', [ExpertController::class, 'create'])->name('expert.form');
    Route::post('/experts/expert/enregistrer', [ExpertController::class, 'store'])->name('expert.store');
    Route::get('/experts/experts', [ExpertController::class, 'index'])->name('expert.index');
    Route::get('/experts/expert/{expert}', [ExpertController::class, 'show'])->name('expert.show');
    Route::get('/experts/{expert}/edit', [ExpertController::class, 'edit'])->name('expert.edit');
    Route::put('/experts/{expert}', [ExpertController::class, 'update'])->name('expert.update');
    Route::delete('/experts/{expert}', [ExpertController::class, 'destroy'])->name('expert.destroy');  
    Route::get('/experts/experts-disponibles', [ExpertController::class, 'liste'])->name('expert.liste');
    
    // Routes pour les plans d'accompagnement (experts)
    Route::get('/experts/plans', [ExpertPlanController::class, 'index'])->name('expert.plans.index');
    Route::get('/experts/plans/{plan}', [ExpertPlanController::class, 'show'])->name('expert.plans.show');

    // Routes pour les propositions (experts)
    Route::get('/experts/propositions', [PropositionController::class, 'index'])->name('proposition.index');
    Route::get('/experts/propositions/{proposition}', [PropositionController::class, 'show'])->name('proposition.show');
    Route::get('/experts/propositions/{proposition}/edit', [PropositionController::class, 'edit'])->name('proposition.edit');
    Route::get('/experts/plans/{plan}/proposer', [PropositionController::class, 'create'])->name('proposition.create');
    Route::post('/experts/propositions', [PropositionController::class, 'store'])->name('proposition.store');
    Route::put('/experts/propositions/{proposition}', [PropositionController::class, 'update'])->name('proposition.update');
    Route::delete('/experts/propositions/{proposition}', [PropositionController::class, 'destroy'])->name('proposition.destroy');

    // Routes pour les propositions reçues (membres)
    Route::get('/experts/propositions-recues', [PropositionMembreController::class, 'index'])->name('proposition.membre.index');
    Route::get('/experts/propositions-recues/{proposition}', [PropositionMembreController::class, 'show'])->name('proposition.membre.show');
    Route::post('/experts/propositions-recues/{proposition}/accepter', [PropositionMembreController::class, 'accepter'])->name('proposition.membre.accepter');
    Route::post('/experts/propositions-recues/{proposition}/refuser', [PropositionMembreController::class, 'refuser'])->name('proposition.membre.refuser');

    Route::get('/experts/disponibilite/ajouter', [DisponibiliteController::class, 'create'])->name('disponibilite.create');
    Route::post('/experts/disponibilite', [DisponibiliteController::class, 'store'])->name('disponibilite.store');
    Route::delete('/experts/disponibilite/{id}', [DisponibiliteController::class, 'destroy'])->name('disponibilite.destroy');
    
    Route::get('/experts/{expert}/evaluation/create', [EvaluationController::class, 'create'])->name('evaluation.create');
    Route::post('/experts/{expert}/evaluation', [EvaluationController::class, 'store'])->name('evaluation.store');


    Route::get('/prestations/prestations', [PrestationController::class, 'index'])->name('prestation.index');
    Route::get('/prestations/prestation/create', [PrestationController::class, 'create'])->name('prestation.create');
    Route::get('/prestations/prestation/{id}/edit', [PrestationController::class, 'edit'])->name('prestation.edit');
    Route::post('/prestations/prestation', [PrestationController::class, 'store'])->name('prestation.store');
    Route::put('/prestations/prestation/{id}', [PrestationController::class, 'update'])->name('prestation.update');
    Route::delete('/prestations/prestation/{id}', [PrestationController::class, 'destroy'])->name('prestation.destroy');
    Route::get('/prestations/les-prestations', [PrestationController::class, 'liste'])->name('prestation.liste');
    Route::get('/prestations/{id}/inscription', [PrestationController::class, 'inscrireForm'])->name('prestation.inscrire.form');
    Route::post('/prestations/{id}/inscription', [PrestationController::class, 'inscrireStore'])->name('prestation.inscrire.store');
    Route::post('/prestations/{id}/calculer', [PrestationController::class, 'calculerMontant'])->name('prestation.calculer.montant');

    // Routes pour la gestion des cotisations (réservé aux entreprises membres CJES)
    Route::get('/entreprises/cotisations', [CotisationController::class, 'index'])->name('cotisation.index');
    Route::get('/entreprises/cotisations/create/{entrepriseId}', [CotisationController::class, 'create'])->name('cotisation.create');
    Route::post('/entreprises/cotisations', [CotisationController::class, 'store'])->name('cotisation.store');
    Route::get('/entreprises/cotisations/{id}/edit', [CotisationController::class, 'edit'])->name('cotisation.edit');
    Route::put('/entreprises/cotisations/{id}', [CotisationController::class, 'update'])->name('cotisation.update');
    Route::delete('/entreprises/cotisations/{id}', [CotisationController::class, 'destroy'])->name('cotisation.destroy');
    Route::post('/entreprises/cotisations/{id}/mark-as-paid', [CotisationController::class, 'markAsPaid'])->name('cotisation.markAsPaid');
    
    Route::get('/forums/forums', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forums/forums/{forumId}/sujets', [SujetController::class, 'index'])->name('sujet.index');
    Route::get('/forums/forums/{forumId}/sujet/create', [SujetController::class, 'create'])->name('sujet.create');
    Route::post('/forums/forums/{forumId}/sujet', [SujetController::class, 'store'])->name('sujet.store');
    Route::get('/forums/sujet/{id}/edit', [SujetController::class, 'edit'])->name('sujet.edit');
    Route::put('/forums/sujet/{id}', [SujetController::class, 'update'])->name('sujet.update');
    Route::delete('/forums/sujet/{id}', [SujetController::class, 'destroy'])->name('sujet.destroy');
    Route::get('/forums/forums/sujets', [SujetController::class, 'liste'])->name('sujet.liste');
    Route::get('/forums/sujet/{id}', [SujetController::class, 'show'])->name('sujet.show');
    Route::post('/forums/sujet/{id}/message', [SujetController::class, 'storeMessage'])->name('sujet.storeMessage');

    Route::get('/experts/mes-conseillers', [ConseillerController::class, 'mesConseillers'])->name('conseiller.mes_conseillers');
    Route::get('/experts/prescriptions/create', [ConseillerController::class, 'create'])->name('conseiller.create');
    Route::post('/experts/prescriptions', [ConseillerController::class, 'store'])->name('conseiller.store');
    Route::get('/experts/mes-prescriptions', [ConseillerController::class, 'index'])->name('conseiller.index');

    Route::get('/prestations/formations', [FormationController::class, 'index'])->name('formation.index');
    Route::get('/prestations/formations/create', [FormationController::class, 'create'])->name('formation.create');
    Route::post('/prestations/formations', [FormationController::class, 'store'])->name('formation.store');
    Route::get('/prestations/formations/{id}/edit', [FormationController::class, 'edit'])->name('formation.edit');
    Route::put('/prestations/formations/{id}', [FormationController::class, 'update'])->name('formation.update');
    Route::delete('/prestations/formations/{id}', [FormationController::class, 'destroy'])->name('formation.destroy');
    Route::get('/prestations/formations/{id}/participants', [FormationController::class, 'participants'])->name('formation.participants');
    Route::get('/prestations/les-formations', [FormationController::class, 'liste'])->name('formation.liste');
    //Route::post('/prestations/formations/{id}/inscription', [FormationController::class, 'inscrire'])->name('formation.inscrire');
    Route::get('/prestations/{formation}/inscrire', [FormationController::class, 'inscrireForm'])->name('formation.inscrire.form');
    Route::post('/prestations/{formation}/inscrire', [FormationController::class, 'inscrireStore'])->name('formation.inscrire.store');
    Route::get('/prestations/formations/{formation}', [FormationController::class, 'show'])->name('formation.show');

    Route::get('/experts/conseiller/prescrire-formation', [ConseillerController::class, 'createFormation'])->name('conseiller.prescrireFormation');
    Route::post('/experts/conseiller/prescriptions/formation', [ConseillerController::class, 'storeFormation'])->name('conseiller.storeFormation');

    Route::get('/accompagnements/mes-accompagnements', [AccompagnementController::class, 'mesAccompagnements'])->name('accompagnement.mes');
    
    Route::get('/accompagnements/plans', [PlanController::class, 'index'])->name('plan.index');
    Route::get('/accompagnements/plans/create', [PlanController::class, 'create'])->name('plan.create');
    Route::post('/accompagnements/plans', [PlanController::class, 'store'])->name('plan.store');
    Route::post('/plans/store', [PlanController::class, 'storeFromModal'])->name('plans.store');
    Route::get('/plans/test', [PlanController::class, 'testStore'])->name('plans.test');
    Route::get('/accompagnements/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plan.edit');
    Route::put('/accompagnements/plans/{plan}', [PlanController::class, 'update'])->name('plan.update');
    Route::delete('/accompagnements/plans/{plan}', [PlanController::class, 'destroy'])->name('plan.destroy');
    Route::get('/accompagnements/accompagnements/{accompagnement}/plans/create', [PlanController::class, 'createFromAccompagnement'])
        ->name('plan.createFromAccompagnement');

    Route::get('/bons/bons', [BonController::class, 'index'])->name('bon.index');

    Route::get('/bons/credits', [CreditController::class, 'index'])->name('credit.index');

    Route::get('/evenements/evenements', [EvenementController::class, 'index'])->name('evenement.index');
    Route::get('/evenements/evenements/{evenement}', [EvenementController::class, 'show'])->name('evenement.show');
    Route::get('/evenements/{id}/inscrire', [EvenementController::class, 'inscrireForm'])->name('evenement.inscrire.form');
    Route::post('/evenements/{id}/inscrire', [EvenementController::class, 'inscrireStore'])->name('evenement.inscrire.store');
    Route::post('/evenements/{id}/calculer', [EvenementController::class, 'calculerMontant'])->name('evenement.calculer.montant');

    // Prestations réalisées
    Route::resource('/prestations/prestationrealisee', PrestationrealiseeController::class)->only([
        'index','create','store','show'
    ]);
    // Utiliser un bon pour régler une prestation réalisée
    Route::post('/prestations/prestationrealisee/{prestationrealisee}/bons', [BonutiliseController::class, 'store'])
        ->name('bon.utiliser');
    // Optionnel : annuler une utilisation de bon
    Route::delete('/bons/bonutilises/{bonutilise}', [BonutiliseController::class, 'destroy'])
        ->name('bon.utiliser.annuler');

    Route::get('/bons/ressourcecomptes/', [RessourcecompteController::class, 'index'])->name('ressourcecompte.index');
    Route::get('/bons/ressourcecomptes/create', [RessourcecompteController::class, 'create'])->name('ressourcecompte.create');
    Route::post('/bons/ressourcecomptes/', [RessourcecompteController::class, 'store'])->name('ressourcecompte.store');

    Route::get('/prestations/prestationressources', [PrestationressourceController::class, 'index'])->name('prestationressource.index');

    Route::get('/prestations/formationressources', [FormationressourceController::class, 'index'])->name('formationressource.index');

    Route::get('/evenements/evenementressources', [EvenementressourceController::class, 'index'])->name('evenementressource.index');

    Route::get('/evenements/espaceressources', [EspaceressourceController::class, 'index'])->name('espaceressource.index');


    Route::get('/bons/conversions', [ConversionController::class, 'index'])->name('conversion.index');
    Route::get('/bons/conversions/create', [ConversionController::class, 'create'])->name('conversion.create');
    Route::post('/bons/conversions', [ConversionController::class, 'store'])->name('conversion.store');


    // Filleul : créer un parrainage
    Route::get('/membres/parrainage/create', [ParrainageController::class, 'create'])->name('parrainage.create');
    Route::post('/membres/parrainage/store', [ParrainageController::class, 'store'])->name('parrainage.store');

    // Parrain : voir parrainages en attente
    Route::get('/membres/parrainage/en-attente', [ParrainageController::class, 'index'])->name('parrainage.index');
    Route::post('/membres/parrainage/activer/{id}', [ParrainageController::class, 'activer'])->name('parrainage.activer');


    Route::get('/prestations/quiz', [QuizController::class, 'index'])->name('quiz.index');
    Route::get('/prestations/quiz/create', [QuizController::class, 'create'])->name('quiz.create');
    Route::post('/prestations/quiz/store', [QuizController::class, 'store'])->name('quiz.store');
    Route::get('/prestations/quiz/{quiz}/edit', [QuizController::class, 'edit'])->name('quiz.edit');
    Route::put('/prestations/quiz/{quiz}', [QuizController::class, 'update'])->name('quiz.update');
    Route::delete('/prestations/quiz/{quiz}', [QuizController::class, 'destroy'])->name('quiz.destroy');

    // Questions d’un quiz
    Route::get('/prestations/quiz/{quiz}/questions', [QuizquestionController::class, 'index'])->name('quizquestion.index');
    Route::get('/prestations/quiz/{quiz}/question/create', [QuizquestionController::class, 'create'])->name('quizquestion.create');
    Route::post('/prestations/quiz/{quiz}/question', [QuizquestionController::class, 'store'])->name('quizquestion.store');
    Route::get('/prestations/quiz/{quiz}/question/{quizquestion}/edit', [QuizquestionController::class, 'edit'])->name('quizquestion.edit');
    Route::put('/prestations/quiz/{quiz}/question/{quizquestion}', [QuizquestionController::class, 'update'])->name('quizquestion.update');
    Route::delete('/prestations/quiz/{quiz}/question/{quizquestion}', [QuizquestionController::class, 'destroy'])->name('quizquestion.destroy');

    // Réponses d’une question
    Route::get('/prestations/questions/{quizquestion}/reponses', [QuizreponseController::class, 'index'])->name('quizreponse.index');
    Route::get('/prestations/question/{quizquestion}/reponse/create', [QuizreponseController::class, 'create'])->name('quizreponse.create');
    Route::post('/prestations/question/{quizquestion}/reponse', [QuizreponseController::class, 'store'])->name('quizreponse.store');
    Route::get('/prestations/question/{quizquestion}/reponse/{quizreponse}/edit', [QuizreponseController::class, 'edit'])->name('quizreponse.edit');
    Route::put('/prestations/question/{quizquestion}/reponse/{quizreponse}', [QuizreponseController::class, 'update'])->name('quizreponse.update');
    Route::delete('/prestations/question/{quizquestion}/reponse/{quizreponse}', [QuizreponseController::class, 'destroy'])->name('quizreponse.destroy');


    Route::get('/prestations/formations/{formation}/quizs', [QuizController::class, 'listByFormation'])->name('quiz');
    Route::get('/prestations/formations/{formation}/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/prestations/formations/{formation}/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');

    Route::get('/prestations/alertes/mes', function () {
    $membre = auth()->user()->membre;
    $alertes = $membre->alertes()->orderByDesc('datealerte')->get();
    $unreadCount = $membre->alertes()->where('lu', 0)->count();
    return response()->json(['alertes' => $alertes, 'unreadCount' => $unreadCount]);
    })->name('alertes.mesAlertes');

    Route::get('/prestations/alertes/{id}/voir', [RecompenseController::class, 'voir'])->name('recompense.voir');



    Route::get('/bons/mes-recompenses', [RecompenseController::class, 'mesRecompenses'])->name('recompense.mesRecompenses');
    //Route::get('/bons/mes-recompenses', [RecompenseController::class, 'index'])->name('recompenses.mesRecompenses');

    Route::get('/forums/message', [ConversationController::class, 'index'])->name('message.index');
    Route::get('/forums/message/{id}', [ConversationController::class, 'show'])->name('message.show');
    Route::get('/forums/message/start/{membreId}', [ConversationController::class, 'start'])->name('message.start');
    Route::post('/forums/message/{conversationId}/message', [MessageController::class, 'store'])->name('message.store');

    Route::get('/forums/conversations/nouveau', [ConversationController::class, 'create'])->name('conversation.create');
    Route::post('/forums/conversations', [ConversationController::class, 'store'])->name('conversation.store');

    // Cotisations payées
    Route::get('/entreprises/cotisations/payees', [CotisationressourceController::class, 'index'])->name('cotisationressource.index');
    Route::get('/entreprises/cotisations/payees/{id}', [CotisationressourceController::class, 'show'])->name('cotisationressource.show');


});
