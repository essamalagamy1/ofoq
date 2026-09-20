<?php

namespace App\Livewire\Dashboard\Badge;

use App\Models\BadgeSetting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class UpdateBadge extends Component
{
    use Toast, WithFileUploads;

    public bool $update_modal = false;
    public ?BadgeSetting $badge = null;
    public $image;

    public string $name = '';
    public ?int $min_percentage = null;
    public ?int $max_percentage = null;
    public string $color_hex = '#FFD700';

    #[On('open-update-modal')]
    public function openModal(BadgeSetting $badge): void
    {
        $this->badge = $badge;
        $this->name = $badge->name;
        $this->min_percentage = $badge->min_percentage;
        $this->max_percentage = $badge->max_percentage;
        $this->color_hex = $badge->color_hex ?? '#000000';
        $this->reset('image');
        
        $this->update_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'min_percentage' => 'required|integer|min:0|max:100',
            'max_percentage' => 'required|integer|min:0|max:100|gte:min_percentage',
            'color_hex' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'max_percentage.gte' => __('lang.max_percentage_error') ?? 'النسبة العليا يجب أن تكون أكبر من أو تساوي النسبة الدنيا.',
        ];
    }

    public function update(): void
    {
        $validated = $this->validate();

        $this->badge->update([
            'name' => $validated['name'],
            'min_percentage' => $validated['min_percentage'],
            'max_percentage' => $validated['max_percentage'],
            'color_hex' => $validated['color_hex'],
        ]);

        if ($this->image) {
            $this->badge->clearMediaCollection('image');
            $this->badge->addMedia($this->image)->toMediaCollection('image');
        }

        $this->success(__('lang.updated_successfully', ['attribute' => __('lang.badge') ?? 'الشارة']));
        
        $this->update_modal = false;
        
        $this->dispatch('render')->to(BadgeData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.badge.update-badge');
    }
}
