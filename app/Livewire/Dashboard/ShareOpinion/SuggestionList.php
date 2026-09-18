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
    use WithPagination;

    public function render(): View
    {
        $suggestions = Question::where('user_id', auth()->id())
            ->where('is_parent_suggestion', true)
            ->latest()
            ->paginate(15);

        return view('livewire.dashboard.share-opinion.suggestion-list', [
            'suggestions' => $suggestions
        ]);
    }
}
