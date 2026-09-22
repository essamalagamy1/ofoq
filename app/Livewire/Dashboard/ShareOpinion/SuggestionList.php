<?php

namespace App\Livewire\Dashboard\ShareOpinion;

use App\Models\Question;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('my_opinions')]
class SuggestionList extends Component
{
    use WithPagination, \Mary\Traits\Toast;

    public function delete(Question $question)
    {
        if ($question->user_id === auth()->id() && $question->status === 'pending') {
            $question->delete();
            $this->success(__('lang.suggestion_deleted') ?? 'تم الحذف بنجاح');
        } else {
            $this->error(__('lang.cannot_delete_suggestion') ?? 'لا يمكن حذف هذا المقترح');
        }
    }

    public function render(): View
    {
        $suggestions = Question::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('livewire.dashboard.share-opinion.suggestion-list', [
            'suggestions' => $suggestions
        ]);
    }
}
