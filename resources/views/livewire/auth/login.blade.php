<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('components.layouts.auth', ['title' => 'login'])] class extends Component {
    #[Validate('required|string')]
    public string $phone = '';

    #[Validate('required|string')]
    public string $phone_key = '+966';

    public string $password = '';

    public bool $remember = false;

    public bool $requires_password = false;
    public bool $phone_checked = false;

    public function checkPhone(): void
    {
        $this->validate([
            'phone' => 'required|string',
            'phone_key' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('phone', $this->phone)->where('phone_key', $this->phone_key)->first();

        if (!$user) {
            $student = \App\Models\Student::where(function ($query) {
                $query->where('parent_mobile_1', $this->phone)->where('parent_mobile_1_key', $this->phone_key)
                      ->orWhere('parent_mobile_2', $this->phone)->where('parent_mobile_2_key', $this->phone_key)
                      ->orWhere('parent_mobile_3', $this->phone)->where('parent_mobile_3_key', $this->phone_key);
            })->first();

            if ($student) {
                $user = \App\Models\User::create([
                    'name' => 'ولي أمر ' . $student->name,
                    'phone' => $this->phone,
                    'phone_key' => $this->phone_key,
                    'requires_password' => false,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                ]);
                $user->assignRole('parent');
            } else {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'phone' => __('auth.failed'),
                ]);
            }
        }

        $this->phone_checked = true;

        if ($user->requires_password) {
            $this->requires_password = true;
        } else {
            // Direct login for users who don't require password (e.g. parents)
            Auth::login($user, $this->remember);
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();
            $this->redirectBasedOnRole($user);
        }
    }

    public function login(): void
    {
        if (!$this->phone_checked) {
            $this->checkPhone();
            return;
        }

        $this->validate([
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('phone', $this->phone)->where('phone_key', $this->phone_key)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => __('auth.failed'),
            ]);
        }

        Auth::login($user, $this->remember);
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectBasedOnRole($user);
    }

    private function redirectBasedOnRole($user): void
    {
        if ($user->hasRole('super_admin')) {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        } elseif ($user->hasRole('teacher')) {
            $this->redirectIntended(default: route('teacher.dashboard', absolute: false), navigate: true);
        } elseif ($user->hasRole('parent')) {
            $this->redirectIntended(default: route('parent.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);
        }
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'phone' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate($this->phone_key . $this->phone . '|' . request()->ip());
    }

    public function resetPhone(): void
    {
        $this->phone_checked = false;
        $this->requires_password = false;
        $this->password = '';
    }
}; ?>

<div>
    <x-card
        class="flex flex-col gap-6 border border-gray-300 dark:border-gray-700 text-lg font-medium rounded-xl dark:text-gray-300
			dark:bg-gray-900  transition-colors duration-200"
        shadow separator>
        <x-auth-header :title="__('lang.log_account')" />

        @session('status')
            <x-alert title="{{ session('status') }}"
                class="text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 my-4 text-center" />
        @endsession

        <form wire:submit="{{ $phone_checked ? 'login' : 'checkPhone' }}" class="flex flex-col gap-6 mt-3">

            @if (!$phone_checked)
                <div class="mb-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    {{ __('lang.enter_phone_number_login') ?? 'أدخل رقم جوالك لتسجيل الدخول' }}
                </div>

                <x-phone-input phoneProperty="phone" keyProperty="phone_key" label="{{ __('lang.phone') ?? 'رقم الجوال' }}"
                    required autofocus />
            @else
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-between">
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <x-icon name="o-phone" class="w-5 h-5 text-gray-500" />
                        <span class="text-gray-700 dark:text-gray-300 font-medium"
                            dir="ltr">{{ $phone_key }}{{ $phone }}</span>
                    </div>
                    <button type="button" wire:click="resetPhone" class="text-sm text-primary hover:underline">
                        {{ __('lang.edit') ?? 'تعديل' }}
                    </button>
                </div>

                @if ($requires_password)
                    <!-- Password -->
                    <div class="relative mt-2">
                        <x-input wire:model="password" :label="__('lang.password')" type="password" required
                            autocomplete="current-password" :placeholder="__('lang.password')" viewable autofocus />

                        {{-- @if (Route::has('password.request'))
                            <a class="absolute end-0 top-0 text-sm link" href="{{route('password.request')}}" >
                                {{ __('lang.forgot_password') }}
                            </a>
                        @endif --}}
                    </div>
                @endif
            @endif

            <!-- Remember Me -->
            <x-checkbox wire:model="remember" :label="__('lang.remember_me')" />

            <div class="flex items-center justify-end">
                <x-button variant="primary" type="submit" class="w-full"
                    spinner="{{ $phone_checked ? 'login' : 'checkPhone' }}">
                    {{ $phone_checked && $requires_password ? __('lang.login') : __('lang.continue') ?? 'متابعة' }}
                </x-button>
            </div>
        </form>

    </x-card>
</div>
