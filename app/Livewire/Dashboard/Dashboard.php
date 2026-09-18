<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\AcademicCycle;
use App\Models\Question;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('home')]
class Dashboard extends Component
{
    public ?int $selected_cycle_id = null;

    public function mount(): void
    {
        view()->share('breadcrumbs', $this->breadcrumbs());

        $activeCycle = AcademicCycle::where('is_active', true)->first() ?? AcademicCycle::first();
        if ($activeCycle) {
            $this->selected_cycle_id = $activeCycle->id;
        }
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.home'),
                'icon' => 'o-home',
            ],
        ];
    }

    #[Computed]
    public function cycles()
    {
        return AcademicCycle::orderByDesc('id')->get();
    }

    #[Computed]
    public function totalStudents(): int
    {
        return Student::count();
    }

    #[Computed]
    public function totalTeachers(): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->count();
    }



    #[Computed]
    public function totalQuestions(): int
    {
        if (! $this->selected_cycle_id) {
            return 0;
        }
        return Question::where('cycle_id', $this->selected_cycle_id)->count();
    }

    #[Computed]
    public function recentQuestions()
    {
        if (! $this->selected_cycle_id) {
            return collect();
        }
        return Question::where('cycle_id', $this->selected_cycle_id)
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render(): Factory|View
    {
        return view('livewire.dashboard.dashboard');
    }
}
