<?php

namespace App\Livewire\Dashboard\Student;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class CreateStudent extends Component
{
    use Toast;

    public bool $create_modal = false;

    public string $name = '';
    public string $nationality = '';
    public ?int $grade = null;
    public ?int $semester = null;
    public string $parent_mobile_1 = '';
    public string $parent_mobile_1_key = '+966';
    public string $parent_mobile_2 = '';
    public string $parent_mobile_2_key = '+966';
    public string $parent_mobile_3 = '';
    public string $parent_mobile_3_key = '+966';
    public bool $can_share_opinion = false;

    #[On('open-create-modal')]
    public function openModal(): void
    {
        $this->reset([
            'name', 'nationality', 'grade', 'semester', 
            'parent_mobile_1', 'parent_mobile_1_key',
            'parent_mobile_2', 'parent_mobile_2_key',
            'parent_mobile_3', 'parent_mobile_3_key', 
            'can_share_opinion'
        ]);
        $this->create_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'grade' => 'required|integer|between:3,6',
            'semester' => 'required|integer|between:1,3',
            'parent_mobile_1' => 'nullable|string|max:20',
            'parent_mobile_1_key' => 'nullable|string|max:10',
            'parent_mobile_2' => 'nullable|string|max:20',
            'parent_mobile_2_key' => 'nullable|string|max:10',
            'parent_mobile_3' => 'nullable|string|max:20',
            'parent_mobile_3_key' => 'nullable|string|max:10',
            'can_share_opinion' => 'boolean',
        ];
    }

    public function create(): void
    {
        $validated = $this->validate();

        Student::create($validated);

        $this->success(__('lang.added_successfully', ['attribute' => __('lang.student') ?? 'الطالب']));
        
        $this->create_modal = false;
        
        $this->dispatch('render')->to(StudentData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.student.create-student');
    }
}
