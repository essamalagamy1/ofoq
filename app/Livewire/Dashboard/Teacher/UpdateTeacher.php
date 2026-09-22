<?php

namespace App\Livewire\Dashboard\Teacher;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class UpdateTeacher extends Component
{
    use Toast;

    public bool $update_modal = false;

    public ?User $teacher = null;

    public string $name = '';

    public string $password = '';

    public string $phone_key = '+966';

    public string $phone = '';

    public ?string $assigned_subject = null;

    public ?int $assigned_grade = null;

    public bool $requires_password = false;

    public bool $is_substitute = false;

    #[On('open-update-modal')]
    public function openModal(User $teacher): void
    {
        $this->teacher = $teacher;
        $this->name = $teacher->name;
        $this->password = ''; // empty unless they want to change it
        $this->phone_key = $teacher->phone_key ?? '+966';
        $this->phone = $teacher->phone ?? '';
        $this->assigned_subject = $teacher->assigned_subject;
        $this->assigned_grade = $teacher->assigned_grade;
        $this->requires_password = (bool) $teacher->requires_password;
        $this->is_substitute = (bool) $teacher->is_substitute;

        $this->update_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone_key' => 'nullable|string|max:10',
            'phone' => 'required|string|digits:9|unique:users,phone,'.$this->teacher->id,
            'assigned_subject' => 'nullable|string|in:science,math,arabic',
            'assigned_grade' => 'nullable|integer|in:3,4,5,6',
            'is_substitute' => 'boolean',
            'requires_password' => 'boolean',
        ];
    }

    public function update(): void
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        
        $validated = \Illuminate\Support\Facades\Validator::make(
            array_merge($this->all(), ['phone' => $cleanPhone]),
            $this->rules()
        )->validate();

        $this->phone = $cleanPhone;

        if ($this->requires_password && $this->password) {
            $validated['password'] = Hash::make($this->password);
        } elseif (!$this->requires_password) {
            $validated['password'] = null;
        }

        $this->teacher->update($validated);

        $this->success(__('lang.updated_successfully', ['attribute' => __('lang.teacher') ?? 'المعلم']));

        $this->update_modal = false;

        $this->dispatch('render')->to(TeacherData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.teacher.update-teacher');
    }
}
