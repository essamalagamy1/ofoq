<?php

namespace App\Livewire\Dashboard\ParentSuggestion;

use App\Models\Question;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('suggestions')]
#[Lazy]
class SuggestionData extends Component
{
    use Toast, WithPagination;

    public bool $comment_modal = false;
    public ?Question $selected_suggestion = null;
    public string $teacher_comment = '';

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public function mount(): void
    {
        abort_if(auth()->user()->is_substitute, 403, __('lang.unauthorized') ?? 'غير مصرح');
        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.parent_suggestions'),
                'icon' => 'o-light-bulb',
            ],
        ];
    }

    #[On('render')]
    public function render(): View
    {
        // Get suggestions assigned to the teacher's subject if the teacher is filtering.
        // Wait, questions have a subject. The teacher has an assigned_subject.
        // So they should only see suggestions for their subject.
        $user = auth()->user();
        
        $suggestions = Question::query()
            ->where('is_parent_suggestion', true)
            ->when($user->hasRole('teacher'), function (Builder $query) use ($user) {
                $query->where('subject', $user->assigned_subject);
            })
            ->with(['cycle', 'creator'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Question::where('is_parent_suggestion', true)->when($user->hasRole('teacher'), fn($q) => $q->where('subject', $user->assigned_subject))->count(),
        ];

        return view('livewire.dashboard.parent-suggestion.suggestion-data', [
            'suggestions' => $suggestions,
            'stats' => $stats
        ]);
    }

    public function addToQuestions(Question $suggestion): void
    {
        $activeCycle = \App\Models\AcademicCycle::where('is_active', true)->first();
        
        $suggestion->update([
            'is_parent_suggestion' => false,
            'status' => 'approved',
            'week' => $activeCycle ? $activeCycle->active_week : 1,
            'cycle_id' => $activeCycle ? $activeCycle->id : $suggestion->cycle_id,
        ]);
        
        $this->success(__('lang.suggestion_added_to_questions') ?? 'تم إضافة السؤال لأسئلة الطلاب بنجاح.');
    }

    public function openCommentModal(Question $suggestion): void
    {
        $this->selected_suggestion = $suggestion;
        $this->teacher_comment = $suggestion->teacher_comment ?? '';
        $this->comment_modal = true;
    }

    public function saveComment(): void
    {
        if ($this->selected_suggestion) {
            $this->selected_suggestion->update([
                'teacher_comment' => $this->teacher_comment
            ]);
            $this->success(__('lang.comment_saved') ?? 'تم حفظ التعليق بنجاح.');
        }
        $this->comment_modal = false;
        $this->selected_suggestion = null;
    }
}
