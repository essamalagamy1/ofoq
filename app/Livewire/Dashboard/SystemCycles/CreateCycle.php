<?php

namespace App\Livewire\Dashboard\SystemCycles;

use App\Models\AcademicCycle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class CreateCycle extends Component
{
    use Toast;

    public bool $create_modal = false;

    public string $name = '';
    public bool $is_active = false;

    #[On('open-create-modal')]
    public function openModal(): void
    {
        $this->reset(['name', 'is_active']);
        $this->create_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function create(): void
    {
        $validated = $this->validate();

        // If this one is set to active, deactivate all others
        if ($this->is_active) {
            AcademicCycle::query()->update(['is_active' => false]);
        }

        AcademicCycle::create($validated);

        $this->success(__('lang.created_successfully', ['attribute' => __('lang.cycle') ?? 'الدورة']));
        
        $this->create_modal = false;
        
        $this->dispatch('render')->to(CycleData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.system-cycles.create-cycle');
    }
}
