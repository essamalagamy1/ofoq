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
    public string $mobile_number = '';
    public string $assigned_subject = '';

    #[On('open-create-modal')]
    public function openModal(): void
    {
        $this->reset([
            'name', 'password', 'mobile_number', 'assigned_subject'
        ]);
        $this->phone_key = '+966';
        $this->create_modal = true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'phone_key' => 'nullable|string|max:10',
            'mobile_number' => 'required|string|max:20|unique:users,mobile_number',
            'assigned_subject' => 'required|string|in:science,math,arabic',
        ];
    }

    public function create(): void
    {
        $validated = $this->validate();

        $user = clone User::create([
            'name' => $this->name,
            'email' => $this->mobile_number . '@ofoq.test',
            'password' => Hash::make($this->password),
            'phone_key' => $this->phone_key,
            'mobile_number' => $this->mobile_number,
            'assigned_subject' => $this->assigned_subject,
            'type' => 'teacher',
            'requires_password' => true,
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
