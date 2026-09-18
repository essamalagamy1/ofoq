<?php

namespace App\Livewire\Dashboard\Question;

use App\Models\AcademicCycle;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class CreateQuestion extends Component
{
    use Toast;

    public bool $create_modal = false;

    public string $subject = '';
    public ?int $grade = null;
    public ?int $week = null;
    public string $content = '';
    public string $option_a = '';
    public string $option_b = '';
    public string $option_c = '';
    public string $option_d = '';
    public string $correct_option = '';

    #[On('open-create-modal')]
    public function openModal(): void
    {
        $this->reset([
            'subject', 'grade', 'week', 'content', 
            'option_a', 'option_b', 'option_c', 'option_d', 'correct_option'
        ]);
        $this->create_modal = true;
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

    public function create(): void
    {
        $validated = $this->validate();

        $activeCycle = AcademicCycle::where('is_active', true)->first();
        if (!$activeCycle) {
            $this->error(__('lang.no_active_cycle_error') ?? 'لا توجد دورة نشطة');
            return;
        }

        $validated['cycle_id'] = $activeCycle->id;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'approved';
        $validated['is_parent_suggestion'] = false; // Because created from dashboard by teacher/admin

        Question::create($validated);

        $this->success(__('lang.created_successfully', ['attribute' => __('lang.question') ?? 'السؤال']));
        
        $this->create_modal = false;
        
        $this->dispatch('render')->to(QuestionData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.question.create-question');
    }
}
