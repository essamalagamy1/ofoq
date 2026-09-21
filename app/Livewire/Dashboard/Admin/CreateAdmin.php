<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Spatie\Permission\Models\Role;

class CreateAdmin extends Component
{
    use Toast;

    public bool $modalAdd = false;

    public $name;

    public $password;

    public $phone;

    public $phone_key;

    public $password_confirmation;

    public $roles = [];

    public $all_roles;

    public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('livewire.dashboard.admin.create-admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'required|string|digits:9|unique:users,phone',
            'phone_key' => 'required|string|max:5',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ];
    }

    public function saveAdd(): void
    {
        $this->phone = preg_replace('/[^0-9]/', '', $this->phone);

        $this->authorize('create_admin');
        $this->validate();
        $user = User::create([
            'name' => $this->name,
            'password' => Hash::make($this->password),
            'phone' => $this->phone,
            'phone_key' => $this->phone_key,
        ]);
        $user->assignRole('admin');

        // Assign selected roles
        $roleNames = Role::whereIn('id', $this->roles)->pluck('name')->toArray();
        $user->syncRoles(array_merge(['admin'], $roleNames));

        $this->modalAdd = false;
        $this->dispatch('render')->component(AdminData::class);
        $this->success(__('lang.created_successfully', ['attribute' => __('lang.admin')]));
    }

    public function resetData(): void
    {
        $this->reset(['name', 'password', 'password_confirmation', 'phone', 'phone_key', 'roles']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
