<?php

namespace App\Livewire\Dashboard\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class CreateUser extends Component
{
    use Toast;

    public bool $modalAdd = false;

    public $name;

    public $password;

    public $phone;

    public $phone_key;

    public $password_confirmation;

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.dashboard.user.create-user');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'required|string|max:20|unique:users,phone',
            'phone_key' => 'required|string|max:5',
        ];
    }

    public function saveAdd(): void
    {
        $this->authorize('create_user');
        $this->validate();
        $user = User::create([
            'name' => $this->name,
            'password' => Hash::make($this->password),
            'phone' => $this->phone,
            'phone_key' => $this->phone_key,
        ]);
        $user->assignRole('user');
        $this->modalAdd = false;
        $this->dispatch('render')->component(UserData::class);
        $this->success(__('lang.created_successfully', ['attribute' => __('lang.user')]));
    }

    public function resetData(): void
    {
        $this->reset(['name', 'password', 'password_confirmation', 'phone', 'phone_key']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
