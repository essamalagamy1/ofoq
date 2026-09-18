<?php

namespace App\Livewire\Dashboard\Badge;

use App\Models\BadgeSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('badges')]
#[Lazy]
class BadgeData extends Component
{
    use Toast, WithPagination;

    public function placeholder(): View
    {
        return view('livewire.placeholders.page-loading');
    }

    public $search_name;

    public function mount(): void
    {
        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.badges'),
                'icon' => 'o-star',
            ],
        ];
    }

    #[On('render')]
    public function render(): View
    {
        $data['badges'] = BadgeSetting::query()
            ->when($this->search_name, fn (Builder $query) => $query->where('name', 'like', "%{$this->search_name}%"))
            ->orderBy('min_percentage', 'asc')
            ->paginate(20);

        return view('livewire.dashboard.badge.badge-data', $data);
    }

    public function delete(BadgeSetting $badge): void
    {
        $badge->delete();
        $this->success(__('lang.deleted_successfully', ['attribute' => __('lang.badge') ?? 'الشارة']));
    }
}
