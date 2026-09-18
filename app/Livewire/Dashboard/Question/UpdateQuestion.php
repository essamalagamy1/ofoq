<?php

namespace App\Livewire\Dashboard\Question;

use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class UpdateQuestion extends Component
{
    use Toast;

    public bool $update_modal = false;
    public ?Question $question = null;

    public string $subject = '';
    public ?int $grade = null;
    public ?int $week = null;
    public string $content = '';
    public string $option_a = '';
    public string $option_b = '';
    public string $option_c = '';
    public string $option_d = '';
    public string $correct_option = '';

    #[On('open-update-modal')]
    public function openModal(Question $question): void
    {
        $this->question = $question;
        
        $this->subject = $question->subject;
        $this->grade = $question->grade;
        $this->week = $question->week;
        $this->content = $question->content;
        $this->option_a = $question->option_a;
        $this->option_b = $question->option_b;
        $this->option_c = $question->option_c;
        $this->option_d = $question->option_d;
        $this->correct_option = $question->correct_option;
        
        $this->update_modal = true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|in:science,math,arabic',
            'grade' => 'required|integer|between:3,6',
            'week' => 'required|integer|between:1,12',
            'content' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_option' => 'required|string|in:A,B,C,D',
        ];
    }

    public function update(): void
    {
        $validated = $this->validate();

        $this->question->update($validated);

        $this->success(__('lang.updated_successfully', ['attribute' => __('lang.question') ?? 'السؤال']));
        
        $this->update_modal = false;
        
        $this->dispatch('render')->to(QuestionData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.question.update-question');
    }
}
