<?php

namespace App\Livewire\Dashboard\ProjectorMode;

use App\Models\AcademicCycle;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('projector')]
class ProjectorBoard extends Component
{
    public $cycles = [];
    public $selected_cycle_id;
    public $selected_subject;
    public $selected_grade;
    public $selected_week;
    
    public ?Question $current_question = null;
    public bool $show_answer = false;

    public function mount(): void
    {
        $this->cycles = AcademicCycle::orderBy('id', 'desc')->get();
        $activeCycle = $this->cycles->where('is_active', true)->first();
        if ($activeCycle) {
            $this->selected_cycle_id = $activeCycle->id;
        }
    }

    public function loadQuestion(): void
    {
        $this->validate([
            'selected_cycle_id' => 'required',
            'selected_subject' => 'required',
            'selected_grade' => 'required',
            'selected_week' => 'required',
        ]);

        $this->current_question = Question::where('cycle_id', $this->selected_cycle_id)
            ->where('subject', $this->selected_subject)
            ->where('grade', $this->selected_grade)
            ->where('week', $this->selected_week)
            ->where('status', 'approved')
            ->inRandomOrder()
            ->first();
            
        $this->show_answer = false;
    }

    public function revealAnswer(): void
    {
        $this->show_answer = true;
    }

    public function render(): View
    {
        return view('livewire.dashboard.projector-mode.projector-board')->layout('components.layouts.maryui.app');
    }
}
