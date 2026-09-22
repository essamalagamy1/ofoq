<?php

namespace App\Livewire\Dashboard\ShareOpinion;

use App\Models\AcademicCycle;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('update_opinion')]
class UpdateSuggestion extends Component
{
    use Toast;

    public string $subject = '';
    public ?int $grade = null;
    public ?int $week = null;
    public string $content = '';
    public string $option_a = '';
    public string $option_b = '';
    public string $option_c = '';
    public string $option_d = '';
    public string $correct_option = '';
    public Question $question;
    public array $available_grades = [];

    public function mount(Question $question): void
    {
        abort_if($question->user_id !== auth()->id() || $question->status !== 'pending', 403);
        
        $this->question = $question;
        $this->subject = $question->subject;
        $this->grade = $question->grade;
        $this->content = $question->content;
        $this->option_a = $question->option_a;
        $this->option_b = $question->option_b;
        $this->option_c = $question->option_c;
        $this->option_d = $question->option_d;
        $this->correct_option = $question->correct_option;
        $activeCycle = AcademicCycle::where('is_active', true)->first();
        if ($activeCycle) {
            $this->week = $activeCycle->active_week ?? 1;
        }

        // Get parent's children's grades
        $user = auth()->user();
        $this->available_grades = \App\Models\Student::where('parent_mobile_1', $user->phone)
            ->orWhere('parent_mobile_2', $user->phone)
            ->orWhere('parent_mobile_3', $user->phone)
            ->select('grade')
            ->distinct()
            ->pluck('grade')
            ->map(fn($grade) => ['id' => $grade, 'name' => (string) $grade])
            ->toArray();
        
        // Ensure the selected grade (if any) is valid; otherwise it stays null.
        if (count($this->available_grades) === 1 && !$this->grade) {
            $this->grade = $this->available_grades[0]['id'];
        }
    }

    public function rules(): array
    {
        $gradesList = collect($this->available_grades)->pluck('id')->toArray();
        $gradesRule = empty($gradesList) ? 'required|integer|between:3,6' : 'required|integer|in:' . implode(',', $gradesList);

        return [
            'subject' => 'required|string|in:science,math,arabic',
            'grade' => $gradesRule,
            'content' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_option' => 'required|string|in:A,B,C,D',
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate();

        $this->question->update($validated);

        $this->success(__('lang.suggestion_updated') ?? 'تم تحديث مقترحك بنجاح.');
        
        $this->redirectRoute('parent.opinion');
    }

    public function render(): View
    {
        return view('livewire.dashboard.share-opinion.update-suggestion');
    }
}
