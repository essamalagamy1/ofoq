<?php

namespace App\Livewire\Dashboard\Teacher;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('teachers')]
#[Lazy]
class TeacherData extends Component
{
    use Toast, WithPagination;

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public $search_name;
    public $search_mobile;

    public function mount(): void
    {
        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.teachers'),
                'icon' => 'o-academic-cap',
            ],
        ];
    }

    #[On('render')]
    public function render(): View
    {
        $data['teachers'] = User::query()
            ->where('type', 'teacher')
            ->when($this->search_name, fn (Builder $query) => $query->where('name', 'like', "%{$this->search_name}%"))
            ->when($this->search_mobile, fn (Builder $query) => $query->where('phone', 'like', "%{$this->search_mobile}%"))
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.teacher.teacher-data', $data);
    }

    public function delete(User $user): void
    {
        if ($user->type === 'teacher') {
            $user->delete();
            $this->success(__('lang.deleted_successfully', ['attribute' => __('lang.teacher') ?? 'المعلم']));
        }
    }
}
