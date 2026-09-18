<?php

namespace App\Livewire\Dashboard\ShareOpinion;

use App\Models\AcademicCycle;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('create_opinion')]
class CreateSuggestion extends Component
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

    public function submit(): void
    {
        $validated = $this->validate();

        $activeCycle = AcademicCycle::where('is_active', true)->first();
        if (!$activeCycle) {
            $this->error(__('lang.no_active_cycle_error') ?? 'لا توجد دورة نشطة حالياً لاستقبال المقترحات.');
            return;
        }

        $validated['cycle_id'] = $activeCycle->id;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['is_parent_suggestion'] = true;

        Question::create($validated);

        $this->success(__('lang.suggestion_submitted') ?? 'تم تقديم مقترحك بنجاح وسنقوم بمراجعته.');
        
        $this->redirectRoute('parent.opinion');
    }

    public function render(): View
    {
        return view('livewire.dashboard.share-opinion.create-suggestion');
    }
}
