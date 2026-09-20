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

    public string $assigned_subject = '';

    public bool $requires_password = false;

    #[On('open-create-modal')]
    public function openModal(): void
    {
        $this->reset([
            'name', 'password', 'phone', 'assigned_subject', 'requires_password'
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
            'phone' => 'required|string|max:20|unique:users,phone',
            'assigned_subject' => 'required|string|in:science,math,arabic',
        ];
    }

    public function create(): void
    {
        $validated = $this->validate();

        $user = clone User::create([
            'name' => $this->name,
            'password' => $this->requires_password ? Hash::make($this->password) : null,
            'requires_password' => $this->requires_password,
            'phone_key' => $this->phone_key,
            'phone' => $this->phone,
            'assigned_subject' => $this->assigned_subject,
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
