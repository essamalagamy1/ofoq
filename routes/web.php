<?php

use App\Http\Controllers\LanguageController;
use App\Livewire\Dashboard\Badge\BadgeData;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Dashboard\ParentDashboard\ChildrenList;
use App\Livewire\Dashboard\ParentSuggestion\SuggestionData;
use App\Livewire\Dashboard\ProjectorMode\ProjectorBoard;
use App\Livewire\Dashboard\Question\QuestionData;
use App\Livewire\Dashboard\ShareOpinion\CreateSuggestion as ParentCreateSuggestion;
use App\Livewire\Dashboard\ShareOpinion\SuggestionList as ParentSuggestionList;
use App\Livewire\Dashboard\SiteSetting\UpdateSiteSetting;
use App\Livewire\Dashboard\Student\StudentData;
use App\Livewire\Dashboard\SystemCycles\CycleData;
use App\Livewire\Dashboard\Teacher\TeacherData;
use Illuminate\Support\Facades\Route;

Route::middleware(['web-language'])->group(function () {
    Route::get('web-language/{lang}', LanguageController::class)->name('web-language');
    Route::redirect('/', 'login')->name('home');

    // Shared Auth Routes
    Route::middleware(['auth'])->group(function () {
        Route::livewire('profile', \App\Livewire\Dashboard\Profile\Profile::class)->name('profile');
    });

    // Admin Routes
    Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::livewire('dashboard', Dashboard::class)->name('dashboard');
        Route::livewire('students', StudentData::class)->name('students');
        Route::livewire('teachers', TeacherData::class)->name('teachers');
        Route::livewire('badges', BadgeData::class)->name('badges');
        Route::livewire('questions', QuestionData::class)->name('questions');
        Route::livewire('questions/{question}/record', \App\Livewire\Dashboard\Question\RecordStudentAnswers::class)->name('questions.record');
        Route::livewire('suggestions', SuggestionData::class)->name('suggestions');
        Route::livewire('cycles', CycleData::class)->name('cycles');
        Route::livewire('site-settings', UpdateSiteSetting::class)->name('site-settings');
    });

    // Teacher Routes
    Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::livewire('dashboard', Dashboard::class)->name('dashboard');
        Route::livewire('questions', QuestionData::class)->name('questions');
        Route::livewire('questions/{question}/record', \App\Livewire\Dashboard\Question\RecordStudentAnswers::class)->name('questions.record');
        Route::livewire('suggestions', SuggestionData::class)->name('suggestions');
    });

    // Parent Routes
    Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
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
