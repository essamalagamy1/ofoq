<?php

namespace App\Livewire\Dashboard\Question;

use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ShowQuestionAnswers extends Component
{
    use WithPagination;

    public bool $show_modal = false;
    public ?Question $question = null;

    #[On('open-show-answers-modal')]
    public function openModal(Question $question): void
    {
        $this->question = $question;
        $this->resetPage(); // Reset pagination when opening a new question
        $this->show_modal = true;
    }

    public function render(): View
    {
        $answers = collect();
        
        if ($this->question) {
            $answers = StudentAnswer::where('question_id', $this->question->id)
                ->with('student')
                ->latest()
                ->paginate(15);
        }

        return view('livewire.dashboard.question.show-question-answers', [
            'answers' => $answers
        ]);
    }
}
