<?php

namespace App\Livewire\Dashboard\Teacher;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class CreateTeacher extends Component
{
    use Toast;

    public bool $create_modal = false;

    public string $name = '';

    public string $password = '';

    public string $phone_key = '+966';

    public string $phone = '';

    public ?string $assigned_subject = null;

    public ?int $assigned_grade = null;

    public bool $requires_password = false;

    public bool $is_substitute = false;

    #[On('open-create-modal')]
    public function openModal(): void
    {
        $this->reset([
            'name', 'password', 'phone', 'assigned_subject', 'assigned_grade', 'requires_password', 'is_substitute'
        ]);
        $this->phone_key = '+966';
        $this->create_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'requires_password' => 'boolean',
            'password' => $this->requires_password ? 'required|string|min:8' : 'nullable',
            'phone_key' => 'nullable|string|max:10',
            'phone' => 'required|string|digits:9|unique:users,phone',
            'assigned_subject' => 'nullable|string|in:science,math,arabic',
            'assigned_grade' => 'nullable|integer|in:3,4,5,6',
            'is_substitute' => 'boolean',
        ];
    }

    public function create(): void
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        
        $validated = \Illuminate\Support\Facades\Validator::make(
            array_merge($this->all(), ['phone' => $cleanPhone]),
            $this->rules()
        )->validate();

        $this->phone = $cleanPhone;

        $user = clone User::create([
            'name' => $this->name,
            'password' => $this->requires_password ? Hash::make($this->password) : null,
            'requires_password' => $this->requires_password,
            'phone_key' => $this->phone_key,
            'phone' => $this->phone,
            'assigned_subject' => $this->assigned_subject,
            'assigned_grade' => $this->assigned_grade,
            'is_substitute' => $this->is_substitute,
            'type' => 'teacher',
        ]);

        $user->assignRole('teacher');

        $this->success(__('lang.created_successfully', ['attribute' => __('lang.teacher') ?? 'المعلم']));

        $this->create_modal = false;

        $this->dispatch('render')->to(TeacherData::class);
    }

    public function render(): View
    {
        return view('livewire.dashboard.teacher.create-teacher');
    }
}
