<?php

namespace App\Livewire\Dashboard\Question;

use App\Models\AcademicCycle;
use App\Models\Question;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('questions')]
#[Lazy]
class QuestionData extends Component
{
    use Toast, WithPagination;

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public $search_content;
    public $filter_cycle_id;
    public $filter_subject;
    public $filter_grade;
    
    public $all_cycles = [];

    public function mount(): void
    {
        $this->all_cycles = AcademicCycle::orderBy('id', 'desc')->get()->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray();
        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.questions'),
                'icon' => 'o-question-mark-circle',
            ],
        ];
    }

    #[On('render')]
    public function render(): View
    {
        $data['questions'] = Question::query()
            ->with(['cycle', 'creator'])
            ->when($this->search_content, fn (Builder $query) => $query->where('content', 'like', "%{$this->search_content}%"))
            ->when($this->filter_cycle_id, fn (Builder $query) => $query->where('cycle_id', $this->filter_cycle_id))
            ->when($this->filter_subject, fn (Builder $query) => $query->where('subject', $this->filter_subject))
            ->when($this->filter_grade, fn (Builder $query) => $query->where('grade', $this->filter_grade))
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.question.question-data', $data);
    }

    public function delete(Question $question): void
    {
        $question->delete();
        $this->success(__('lang.deleted_successfully', ['attribute' => __('lang.question') ?? 'السؤال']));
    }
    
    public function checkActiveCycleAndOpenCreateModal(): void
    {
        $activeCycle = AcademicCycle::where('is_active', true)->first();
        
        if (!$activeCycle) {
            $this->error(__('lang.no_active_cycle_error') ?? 'لا يمكن إضافة سؤال لعدم وجود دورة أكاديمية نشطة حالياً. يرجى تفعيل دورة أولاً.');
            return;
        }
        
        $this->dispatch('open-create-modal');
    }
}
