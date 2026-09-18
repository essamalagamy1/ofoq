<?php

namespace App\Livewire\Dashboard\SystemCycles;

use App\Models\AcademicCycle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class UpdateCycle extends Component
{
    use Toast;

    public bool $update_modal = false;
    public ?AcademicCycle $cycle = null;

    public string $name = '';
    public bool $is_active = false;
    public int $active_week = 1;

    #[On('open-update-modal')]
    public function openModal(AcademicCycle $cycle): void
    {
        $this->cycle = $cycle;
        $this->name = $cycle->name;
        $this->is_active = $cycle->is_active;
        $this->active_week = $cycle->active_week ?? 1;
        
        $this->update_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'active_week' => 'required|integer|between:1,12',
        ];
    }

    public function update(): void
    {
        $validated = $this->validate();

        // If this one is set to active and it wasn't before, deactivate all others
        if ($this->is_active && !$this->cycle->is_active) {
            AcademicCycle::where('id', '!=', $this->cycle->id)->update(['is_active' => false]);
        }

        $this->cycle->update($validated);

        $this->success(__('lang.updated_successfully', ['attribute' => __('lang.cycle') ?? 'الدورة']));
        
        $this->update_modal = false;
        
        $this->dispatch('render')->to(CycleData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.system-cycles.update-cycle');
    }
}
