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
    public $overall_progress = null; // Overall cycle progress
    
    public ?int $active_cycle_id = null;
    public ?int $active_cycle_week = null;

    public ?int $selected_week = null;
    public $week_details = [];

    #[On('open-student-progress-modal')]
    public function openModal(Student $student): void
    {
        $this->student = $student;
        $this->cycles = AcademicCycle::orderBy('id', 'desc')->get();
        
        $activeCycle = $this->cycles->where('is_active', true)->first();
        if ($activeCycle) {
            $this->active_cycle_id = $activeCycle->id;
            $this->active_cycle_week = $activeCycle->active_week;
        }

        $defaultCycle = $activeCycle ?? $this->cycles->first();
        if ($defaultCycle) {
            $this->selected_cycle_id = $defaultCycle->id;
            $this->loadProgress();
        }
        
        $this->selected_week = null;
        $this->show_modal = true;
    }

    public function updatedSelectedCycleId(): void
    {
        $this->selected_week = null;
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
            
        // Calculate Overall Progress
        $totalAll = $answers->count();
        if ($totalAll > 0) {
            $correctAll = $answers->where('is_correct', true)->count();
            $percentageAll = round(($correctAll / $totalAll) * 100);
            
            $overallBadge = null;
            foreach ($allBadges as $badge) {
                if ($percentageAll >= $badge->min_percentage && $percentageAll <= $badge->max_percentage) {
                    $overallBadge = [
                        'name' => $badge->name,
                        'color_hex' => $badge->color_hex,
                        'image' => $badge->getFirstMediaUrl('image')
                    ];
                    break;
                }
            }
            
            $this->overall_progress = [
                'percentage' => $percentageAll,
                'badge' => $overallBadge,
                'total_answered' => $totalAll,
                'correct_answers' => $correctAll
            ];
        } else {
            $this->overall_progress = null;
        }

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
                        'image' => $badge->getFirstMediaUrl('image')
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

    public function showWeekDetails(int $week): void
    {
        if ($this->selected_cycle_id !== $this->active_cycle_id) {
            return;
        }

        if ($week >= $this->active_cycle_week) {
            return;
        }

        $this->selected_week = $week;
        
        $answers = StudentAnswer::where('student_id', $this->student->id)
            ->where('cycle_id', $this->selected_cycle_id)
            ->where('week', $week)
            ->with('question')
            ->get();

        $this->week_details = $answers;
    }

    public function backToWeeks(): void
    {
        $this->selected_week = null;
    }

    public function render(): View
    {
        return view('livewire.dashboard.student.student-progress');
    }
}
