<div class="space-y-6">
    {{-- Welcome Message --}}
    <div class="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-6 text-primary-content shadow-lg flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold mb-2">{{ __('lang.welcome_back') ?? 'مرحباً بك مجدداً' }}، {{ auth()->user()->name }}! 👋</h2>
            <p class="text-primary-content/80">{{ __('lang.welcome_parent_msg') ?? 'نحن سعداء بمتابعتك لمستوى أبنائك وتفاعلك معنا.' }}</p>
        </div>
        <div class="hidden md:block">
            <x-icon name="o-heart" class="w-16 h-16 text-primary-content/20" />
        </div>
    </div>

    <x-header title="{{ __('lang.my_children') ?? 'أبنائي' }}" subtitle="{{ __('lang.welcome_parent') ?? 'مرحباً بك في لوحة متابعة أبنائك' }}" separator>
        @if($can_share_opinion)
            <x-slot:actions>
                <x-button icon="o-light-bulb" link="{{ route('parent.opinion') }}" class="btn-primary">
                    {{ __('lang.opinion') ?? 'اقتراح أسئلة' }}
                </x-button>
            </x-slot:actions>
        @endif
    </x-header>

    @if(session('error'))
        <x-alert icon="o-exclamation-triangle" class="alert-error mb-6">
            {{ session('error') }}
        </x-alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($children as $child)
            <div class="card bg-base-100 shadow-xl border border-base-200 overflow-hidden">
                <div class="bg-primary/10 p-6 flex flex-col items-center text-center">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $child->name }}</h3>
                    <p class="text-primary font-semibold mt-1">{{ __('lang.grade') ?? 'الصف' }}: {{ $child->grade }} | {{ __('lang.semester') ?? 'الفصل' }}: {{ $child->semester }}</p>
                </div>
                
                <div class="p-6 bg-base-100">
                    <div class="flex flex-col gap-3">
                        <x-button icon="o-star" class="btn-warning w-full" wire:click="$dispatch('open-student-progress-modal', { student: {{ $child->id }} })">
                            {{ __('lang.track_badges') ?? 'تتبع الشارات الأسبوعية' }}
                        </x-button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 bg-base-100 rounded-2xl border-2 border-dashed border-base-300">
                <x-icon name="o-users" class="w-24 h-24 mx-auto mb-4 text-gray-300" />
                <h3 class="text-xl font-bold">{{ __('lang.no_children_found') ?? 'لا يوجد أبناء مسجلين برقم الجوال الخاص بك.' }}</h3>
                <p class="mt-2">{{ __('lang.contact_school') ?? 'يرجى التواصل مع إدارة المدرسة لتحديث بياناتك.' }}</p>
            </div>
        @endforelse
    </div>

    @livewire('dashboard.student.student-progress')
</div>
