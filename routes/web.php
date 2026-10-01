<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\InfoReportController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // 1. Dashboard (Multi-Role: Staff, TL, Asman, MUP)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Modul Pekerjaan / Task (Panel 5 Mockup)
    Route::get('/pekerjaan', [TaskController::class, 'index'])->name('pekerjaan');
    Route::post('/pekerjaan', [TaskController::class, 'store'])->name('pekerjaan.store');
    Route::put('/pekerjaan/{id}', [TaskController::class, 'update'])->name('pekerjaan.update');
    Route::delete('/pekerjaan/{id}', [TaskController::class, 'destroy'])->name('pekerjaan.destroy');

    // Route Resource & Progress Update untuk Task
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.progress');

    // 3. Modul Isu & Kendala (Panel 6, 7, 8 Mockup)
    Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
    Route::get('/issues/create', [IssueController::class, 'create'])->name('issues.create');
    Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
    Route::get('/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
    Route::post('/issues/{id}/discussions', [IssueController::class, 'storeDiscussion'])->name('issues.discussions.store');
    
    // Aksi Eskalasi Berjenjang (TL & ASMAN) - Mendukung POST & PUT/PATCH
    Route::match(['POST', 'PUT', 'PATCH'], '/issues/{id}/escalate', [IssueController::class, 'escalate'])
        ->middleware('role:TL,ASMAN')
        ->name('issues.escalate');

    // Aksi Pengambilan Keputusan (TL, ASMAN, MUP) - Mendukung POST & PUT/PATCH
    Route::match(['POST', 'PUT', 'PATCH'], '/issues/{id}/decisions', [DecisionController::class, 'store'])
        ->middleware('role:TL,ASMAN,MUP')
        ->name('decisions.store');

    // 4. Modul Rapat Mingguan & Workspace (Panel 3, 4, 9 Mockup)
    Route::get('/rapat', [MeetingController::class, 'index'])->name('rapat.index');
    Route::post('/rapat', [MeetingController::class, 'store'])->name('rapat.store');
    Route::get('/rapat/{id}', [MeetingController::class, 'show'])->name('rapat.show');
    Route::get('/rapat/{id}/workspace', [MeetingController::class, 'workspace'])->name('rapat.workspace');
    Route::post('/rapat/{id}/tasks', [MeetingController::class, 'storeWorkspaceTask'])->name('rapat.tasks.store');
    Route::post('/rapat/{id}/finish', [MeetingController::class, 'finishMeeting'])->name('rapat.finish');
    Route::get('/rapat/{id}/cetak', [MeetingController::class, 'cetak'])->name('rapat.cetak');
    Route::post('/rapat/{id}/attach-issue', [MeetingController::class, 'attachIssue'])->name('rapat.attach_issue');
    Route::delete('/rapat/{id}/detach-issue/{issueId}', [MeetingController::class, 'detachIssue'])->name('rapat.detach_issue');

    // 5. Modul Keputusan (Rekap Seluruh Keputusan Atasan)
    Route::get('/keputusan', [DecisionController::class, 'index'])->name('keputusan.index');

    // 6. Modul Informasi & Pengumuman (Cascading Information FR-09)
    Route::get('/informasi', [InfoReportController::class, 'informasi'])->name('informasi.index');
    Route::post('/informasi', [InfoReportController::class, 'storeInformasi'])->name('informasi.store');
    Route::delete('/informasi/{id}', [InfoReportController::class, 'destroyInformasi'])->name('informasi.destroy');

    // 7. Modul Laporan (Mingguan, Bulanan & Export)
    Route::get('/laporan', [InfoReportController::class, 'laporan'])->name('laporan.index');
    Route::get('/laporan/export-csv', [InfoReportController::class, 'exportCsv'])->name('laporan.export');

    // 8. Manajemen Profil & Keamanan Akun
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // 9. Modul Master Data (Dibatasi Khusus MUP & ADMIN)
    Route::middleware(['role:MUP,ADMIN'])->group(function () {
        Route::get('/master-data', [MasterDataController::class, 'index'])->name('master.index');
        Route::post('/master-data/users', [MasterDataController::class, 'storeUser'])->name('master.users.store');
        Route::put('/master-data/users/{id}', [MasterDataController::class, 'updateUser'])->name('master.users.update');
        Route::delete('/master-data/users/{id}', [MasterDataController::class, 'destroyUser'])->name('master.users.destroy');

        // Master Organisasi (Incumbent & Parent)
        Route::post('/master-data/hierarchies', [MasterDataController::class, 'storeHierarchy'])->name('master.hierarchies.store');
        Route::put('/master-data/hierarchies/{id}', [MasterDataController::class, 'updateHierarchy'])->name('master.hierarchies.update');
        Route::delete('/master-data/hierarchies/{id}', [MasterDataController::class, 'destroyHierarchy'])->name('master.hierarchies.destroy');
    });
});
