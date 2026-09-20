<?php

namespace App\Livewire\Dashboard\Student;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class UpdateStudent extends Component
{
    use Toast;

    public bool $update_modal = false;
    public ?Student $student = null;

    public string $name = '';
    public string $nationality = '';
    public ?int $grade = null;
    public ?int $semester = null;
    public string $parent_mobile_1 = '';
    public string $parent_mobile_1_key = '';
    public string $parent_mobile_2 = '';
    public string $parent_mobile_2_key = '';
    public string $parent_mobile_3 = '';
    public string $parent_mobile_3_key = '';
    public bool $can_share_opinion = false;

    #[On('open-update-modal')]
    public function openModal(Student $student): void
    {
        $this->student = $student;
        $this->name = $student->name;
        $this->nationality = $student->nationality ?? '';
        $this->grade = $student->grade;
        $this->semester = $student->semester;
        $this->parent_mobile_1 = $student->parent_mobile_1 ?? '';
        $this->parent_mobile_1_key = $student->parent_mobile_1_key ?? '+966';
        $this->parent_mobile_2 = $student->parent_mobile_2 ?? '';
        $this->parent_mobile_2_key = $student->parent_mobile_2_key ?? '+966';
        $this->parent_mobile_3 = $student->parent_mobile_3 ?? '';
        $this->parent_mobile_3_key = $student->parent_mobile_3_key ?? '+966';
        $this->can_share_opinion = $student->can_share_opinion;
        
        $this->update_modal = true;
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

    public function update(): void
    {
        $validated = $this->validate();

        if (!empty($validated['parent_mobile_1'])) {
            $validated['parent_mobile_1'] = preg_replace('/[^0-9]/', '', $validated['parent_mobile_1']);
        }
        if (!empty($validated['parent_mobile_2'])) {
            $validated['parent_mobile_2'] = preg_replace('/[^0-9]/', '', $validated['parent_mobile_2']);
        }
        if (!empty($validated['parent_mobile_3'])) {
            $validated['parent_mobile_3'] = preg_replace('/[^0-9]/', '', $validated['parent_mobile_3']);
        }

        $this->student->update($validated);

        $this->success(__('lang.updated_successfully', ['attribute' => __('lang.student') ?? 'الطالب']));
        
        $this->update_modal = false;
        
        $this->dispatch('render')->to(StudentData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.student.update-student');
    }
}
