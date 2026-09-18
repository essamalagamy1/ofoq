<?php

namespace App\Livewire\Dashboard\Student;

use App\Models\AcademicCycle;
use App\Models\BadgeSetting;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class StudentProgress extends Component
{
    public bool $show_modal = false;
    public ?Student $student = null;
    public $cycles = [];
    public $selected_cycle_id = null;
    
    public $weekly_progress = []; // Array of weeks 1 to 12

    #[On('open-student-progress-modal')]
    public function openModal(Student $student): void
    {
        $this->student = $student;
        $this->cycles = AcademicCycle::orderBy('id', 'desc')->get();
        
        $activeCycle = $this->cycles->where('is_active', true)->first() ?? $this->cycles->first();
        if ($activeCycle) {
            $this->selected_cycle_id = $activeCycle->id;
            $this->loadProgress();
        }
        
        $this->show_modal = true;
    }

    public function updatedSelectedCycleId(): void
    {
        $this->loadProgress();
    }

    private function loadProgress(): void
    {
        $this->weekly_progress = [];
        
        if (!$this->student || !$this->selected_cycle_id) {
            return;
        }

        $allBadges = BadgeSetting::all();
        
        // Fetch all answers for this student in this cycle
        $answers = StudentAnswer::where('student_id', $this->student->id)
            ->where('cycle_id', $this->selected_cycle_id)
            ->get();

        // Loop over weeks 1 to 12
        for ($week = 1; $week <= 12; $week++) {
            $weekAnswers = $answers->where('week', $week);
            $total = $weekAnswers->count();
            
            if ($total == 0) {
                $this->weekly_progress[$week] = [
                    'percentage' => null,
                    'badge' => null,
                    'total_answered' => 0
                ];
                continue;
            }

            $correct = $weekAnswers->where('is_correct', true)->count();
            $percentage = round(($correct / $total) * 100);

            // Determine badge
            $earnedBadge = null;
            foreach ($allBadges as $badge) {
                if ($percentage >= $badge->min_percentage && $percentage <= $badge->max_percentage) {
                    $earnedBadge = [
                        'name' => $badge->name,
                        'color_hex' => $badge->color_hex,
                    ];
                    break;
                }
            }

            $this->weekly_progress[$week] = [
                'percentage' => $percentage,
                'badge' => $earnedBadge,
                'total_answered' => $total,
                'correct_answers' => $correct
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.dashboard.student.student-progress');
    }
}
