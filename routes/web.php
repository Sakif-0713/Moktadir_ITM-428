<?php

use App\Http\Controllers\Auth\GuestAccessController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PromptTemplateController;
use App\Http\Controllers\RefactorLogController;
use App\Http\Controllers\TestRunController;
use App\Http\Controllers\WorkspaceController;
use App\Models\Workspace;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $workspaces = Workspace::withCount(['documents', 'promptTemplates', 'testRuns'])->latest()->get();
        $demoWorkspace = $workspaces->firstWhere('is_public_demo', true) ?? $workspaces->first();
    } catch (\Throwable) {
        $workspaces = collect();
        $demoWorkspace = null;
    }

    return view('welcome', compact('workspaces', 'demoWorkspace'));
})->name('home');
Route::post('/demo/guest-login', [GuestAccessController::class, 'loginAsGuest'])->name('demo.guest-login');
Route::get('/demo/guest-login', [GuestAccessController::class, 'loginAsGuest'])->name('demo.guest-login.get');

Route::resource('workspaces', WorkspaceController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

Route::prefix('workspaces/{workspace}')->group(function () {
    // Documents & Chunks
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::post('/documents/{document}/rechunk', [DocumentController::class, 'rechunk'])->name('documents.rechunk');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Prompt Templates & Memento Versioning
    Route::post('/prompts', [PromptTemplateController::class, 'store'])->name('prompts.store');
    Route::get('/prompts/{prompt}', [PromptTemplateController::class, 'show'])->name('prompts.show');
    Route::put('/prompts/{prompt}', [PromptTemplateController::class, 'update'])->name('prompts.update');
    Route::post('/prompts/{prompt}/rollback/{versionNumber}', [PromptTemplateController::class, 'rollback'])->name('prompts.rollback');
    Route::delete('/prompts/{prompt}', [PromptTemplateController::class, 'destroy'])->name('prompts.destroy');

    // TestRuns & Side-by-Side Comparison
    Route::post('/test-runs', [TestRunController::class, 'store'])->name('test-runs.store');
    Route::get('/test-runs/{testRun}', [TestRunController::class, 'show'])->name('test-runs.show');

    // Evaluation Metrics & Regression Inspector
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');

    // Refactoring & Bug-Fix Log
    Route::get('/refactor-logs', [RefactorLogController::class, 'index'])->name('refactor-logs.index');
    Route::post('/refactor-logs', [RefactorLogController::class, 'store'])->name('refactor-logs.store');
});
