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

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public $filter_status;

    public bool $review_modal = false;
    public ?Question $selected_suggestion = null;
    public string $teacher_comment = '';

    public function mount(): void
    {
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
            ->when($this->filter_status, function (Builder $query) {
                $query->where('status', $this->filter_status);
            })
            ->with(['cycle', 'creator'])
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.parent-suggestion.suggestion-data', [
            'suggestions' => $suggestions
        ]);
    }

    public function openReviewModal(Question $suggestion): void
    {
        $this->selected_suggestion = $suggestion;
        $this->teacher_comment = $suggestion->teacher_comment ?? '';
        $this->review_modal = true;
    }

    public function approve(): void
    {
        if ($this->selected_suggestion) {
            $this->selected_suggestion->update([
                'status' => 'approved',
                'teacher_comment' => $this->teacher_comment,
            ]);
            $this->success(__('lang.suggestion_approved') ?? 'تم قبول الاقتراح وإضافته لبنك الأسئلة.');
            $this->review_modal = false;
        }
    }

    public function reject(): void
    {
        $this->validate([
            'teacher_comment' => 'required|string',
        ], [
            'teacher_comment.required' => __('lang.rejection_reason_required') ?? 'يجب كتابة سبب الرفض في التعليق.',
        ]);

        if ($this->selected_suggestion) {
            $this->selected_suggestion->update([
                'status' => 'rejected',
                'teacher_comment' => $this->teacher_comment,
            ]);
            $this->success(__('lang.suggestion_rejected') ?? 'تم رفض الاقتراح.');
            $this->review_modal = false;
        }
    }
}
