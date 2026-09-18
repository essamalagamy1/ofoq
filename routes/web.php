<?php

use App\Http\Controllers\LanguageController;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\Student\StudentData;
use App\Livewire\Dashboard\Teacher\TeacherData;
use App\Livewire\Dashboard\BadgeSetting\BadgeSettingData;
use App\Livewire\Dashboard\SystemCycles\CycleData;
use App\Livewire\Dashboard\QuestionBank\QuestionData;
use App\Livewire\Dashboard\ProjectorMode\ProjectorBoard;
use App\Livewire\Dashboard\ParentSuggestion\SuggestionData;
use App\Livewire\Dashboard\ParentDashboard\ChildrenList;
use App\Livewire\Dashboard\ShareOpinion\SuggestionList as ParentSuggestionList;
use App\Livewire\Dashboard\ShareOpinion\CreateSuggestion as ParentCreateSuggestion;
use Illuminate\Support\Facades\Route;

Route::middleware(['web-language'])->group(function () {
    Route::get('web-language/{lang}', LanguageController::class)->name('web-language');
    Route::redirect('/', 'login')->name('home');

    // Admin Routes
    Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::livewire('dashboard', Dashboard::class)->name('dashboard');
        Route::livewire('students', StudentData::class)->name('students');
        Route::livewire('teachers', TeacherData::class)->name('teachers');
        Route::livewire('badges', BadgeSettingData::class)->name('badges');
        Route::livewire('cycles', CycleData::class)->name('cycles');
    });

    // Teacher Routes
    Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::livewire('dashboard', Dashboard::class)->name('dashboard');
        Route::livewire('questions', QuestionData::class)->name('questions');
        Route::livewire('projector', ProjectorBoard::class)->name('projector');
        Route::livewire('suggestions', SuggestionData::class)->name('suggestions');
    });

    // Parent Routes
    Route::middleware(['auth', 'verified', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
        Route::livewire('dashboard', ChildrenList::class)->name('dashboard');
        
        // Custom middleware to check if any child can share opinion
        Route::middleware(['check.parent.eligibility'])->group(function () {
            Route::livewire('opinion', ParentSuggestionList::class)->name('opinion');
            Route::livewire('opinion/create', ParentCreateSuggestion::class)->name('opinion.create');
        });
    });

    // Guest routes
    require __DIR__.'/auth.php';
});
