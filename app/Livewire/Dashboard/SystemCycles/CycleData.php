<?php

namespace App\Livewire\Dashboard\SystemCycles;

use App\Models\AcademicCycle;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('cycles')]
#[Lazy]
class CycleData extends Component
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
                'label' => __('lang.cycles'),
                'icon' => 'o-arrow-path',
            ],
        ];
    }

    #[On('render')]
    public function render(): View
    {
        $data['cycles'] = AcademicCycle::query()
            ->when($this->search_name, fn (Builder $query) => $query->where('name', 'like', "%{$this->search_name}%"))
            ->latest()
            ->paginate(20);

        return view('livewire.dashboard.system-cycles.cycle-data', $data);
    }

    public function delete(AcademicCycle $cycle): void
    {
        $cycle->delete();
        $this->success(__('lang.deleted_successfully', ['attribute' => __('lang.cycle') ?? 'الدورة']));
    }

    public function toggleActive(AcademicCycle $cycle): void
    {
        // If we are turning it on, turn off all others
        if (!$cycle->is_active) {
            AcademicCycle::where('id', '!=', $cycle->id)->update(['is_active' => false]);
            $cycle->update(['is_active' => true]);
            $this->success(__('lang.cycle_activated') ?? 'تم تفعيل الدورة، وتم تعطيل باقي الدورات');
        } else {
            // Turning it off manually
            $cycle->update(['is_active' => false]);
            $this->success(__('lang.cycle_deactivated') ?? 'تم تعطيل الدورة');
        }
    }
}
