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

    public $badges = [];

    public function mount()
    {
        $this->badges = \App\Models\BadgeSetting::with('media')->orderBy('min_percentage', 'desc')->take(3)->get();
    }

    public function checkPhone(): void
    {
        $this->validate([
            'phone' => 'required|string',
            'phone_key' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited();

        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);

        $user = \App\Models\User::where('phone', $cleanPhone)->where('phone_key', $this->phone_key)->first();

        if (!$user) {
            $student = \App\Models\Student::where(function ($query) {
                $query->where('parent_mobile_1', $this->phone)->where('parent_mobile_1_key', $this->phone_key)->orWhere('parent_mobile_2', $this->phone)->where('parent_mobile_2_key', $this->phone_key)->orWhere('parent_mobile_3', $this->phone)->where('parent_mobile_3_key', $this->phone_key);
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

        $this->phone = $cleanPhone;
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

        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);

        $user = \App\Models\User::where('phone', $cleanPhone)->where('phone_key', $this->phone_key)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => __('auth.failed'),
            ]);
        }

        $this->phone = $cleanPhone;
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
        class="flex flex-col gap-6 border-2 !border-[#d4a85a]/40 !bg-white/95 backdrop-blur-md shadow-[0_10px_40px_rgba(11,28,56,0.15)] text-lg font-medium !rounded-[2rem] dark:text-gray-300 dark:bg-gray-900/95 transition-all duration-300 p-2 md:p-4"
        separator>
        <div class="text-center mb-4 border-b-[3px] border-dashed border-[#d4a85a]/50 pb-4 pt-2">
            <h2
                class="text-3xl font-black text-[#0b1c38] flex items-center justify-center gap-3 drop-shadow-sm transition-transform duration-500 hover:scale-105">
                <x-icon name="o-user-circle" class="w-10 h-10 text-[#d4a85a]" />
                تسجيل الدخول
            </h2>
        </div>

        @session('status')
            <x-alert title="{{ session('status') }}"
                class="text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 my-4 text-center" />
        @endsession

        <form wire:submit="{{ $phone_checked ? 'login' : 'checkPhone' }}" class="flex flex-col gap-4 mt-3">

            @if (!$phone_checked)
                <div class="mb-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    {{ __('lang.enter_phone_number_login') ?? 'أدخل رقم جوالك لتسجيل الدخول' }}
                </div>

                <x-phone-input phoneProperty="phone" keyProperty="phone_key"
                    label="{{ __('lang.phone') ?? 'رقم الجوال' }}" required autofocus />
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

            <div class="flex items-center justify-end mt-4">
                <x-button type="submit"
                    class="w-full text-white text-xl font-bold rounded-xl border-2 border-[#d4a85a]/50 hover:-translate-y-1 hover:shadow-[0_10px_20px_rgba(212,168,90,0.3)] transition-all duration-300 relative overflow-hidden group"
                    style="background: linear-gradient(180deg, #1e3a8a, #0b1c38);"
                    spinner="{{ $phone_checked ? 'login' : 'checkPhone' }}">
                    <span class="relative z-10 flex items-center gap-2">
                        {{ __('lang.login') }}
                        <x-icon name="o-arrow-left"
                            class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-300 rtl:rotate-0 ltr:rotate-180" />
                    </span>
                    <div
                        class="absolute inset-0 bg-[#d4a85a]/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
                    </div>
                </x-button>
            </div>
        </form>

        <!-- Badges -->
        <div class="mt-8 border-t-[3px] border-dashed border-[#d4a85a]/50 pt-2">
            @php
                $siteSetting = \App\Models\SiteSetting::getSetting();
                $loginMessage = $siteSetting->getTranslation('login_message', app()->getLocale(), false);
            @endphp
            
            @if(!empty($loginMessage))
            <h3 class="text-center text-xl font-extrabold mt-4 mb-6 flex items-center justify-center gap-2 drop-shadow-sm"
                style="color: #0b1c38;">
                <span class="text-sm" style="color: #d4a85a;">✦</span> 
                {{ $loginMessage }}
                <span class="text-sm" style="color: #d4a85a;">✦</span>
            </h3>
            @endif
            <div class="flex justify-center flex-wrap gap-6 md:gap-10">
                @foreach ($badges as $badge)
                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full shadow-xl flex items-center justify-center border-4 border-white mb-2 relative"
                            style="background-color: {{ $badge->color_hex }};">
                            @if ($badge->getFirstMediaUrl('image'))
                                <img src="{{ $badge->getFirstMediaUrl('image') }}"
                                    class="w-full h-full object-cover rounded-full" alt="{{ $badge->name }}" />
                            @else
                                <x-icon name="s-star" class="w-8 h-8 md:w-10 md:h-10 text-white" />
                            @endif
                            <div class="absolute -bottom-2 -left-2 -right-2 h-4 opacity-50 blur-sm rounded-full"
                                style="background: linear-gradient(90deg, transparent, {{ $badge->color_hex }}, transparent);">
                            </div>
                        </div>
                        <span class="text-base font-extrabold" style="color: #0b1c38;">{{ $badge->name }}</span>
                    </div>
                @endforeach
            </div>
            <h3 class="text-center text-xl font-extrabold mt-4 flex items-center justify-center gap-2 drop-shadow-sm"
                style="color: #0b1c38;">
                <span class="text-sm" style="color: #d4a85a;">✦</span> أوسمة الإنجاز <span class="text-sm"
                    style="color: #d4a85a;">✦</span>
            </h3>
        </div>

    </x-card>
</div>
