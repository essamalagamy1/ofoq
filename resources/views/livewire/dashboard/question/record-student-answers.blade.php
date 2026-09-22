<div x-data="{
    isFullscreen: false,
    showAnswer: false,
    fontSize: 48,
    init() {
        const updateState = () => {
            this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
        };
        document.addEventListener('fullscreenchange', updateState);
        document.addEventListener('webkitfullscreenchange', updateState);
        document.addEventListener('mozfullscreenchange', updateState);
        document.addEventListener('MSFullscreenChange', updateState);
    },
    toggleFullscreen() {
        let elem = document.getElementById('projector-container');

        if (!this.isFullscreen) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE11 */
                document.msExitFullscreen();
            }
        }
    }
}">
    <x-header title="{{ __('lang.record_answers') ?? 'تسجيل الإجابات' }}" separator>
        <x-slot:actions>
            <x-button icon="o-arrow-left" class="btn-ghost btn-sm sm:btn-md"
                link="{{ route('teacher.questions') }}">{{ __('lang.back') ?? 'رجوع' }}</x-button>
            <x-button icon="o-arrows-pointing-out" class="btn-primary btn-sm sm:btn-md" @click="toggleFullscreen"
                x-show="!isFullscreen">{{ __('lang.fullscreen') ?? 'ملء الشاشة' }}</x-button>
            <x-button icon="o-arrows-pointing-in" class="btn-error btn-sm sm:btn-md" @click="toggleFullscreen"
                x-show="isFullscreen">{{ __('lang.exit_fullscreen') ?? 'خروج' }}</x-button>
        </x-slot:actions>
    </x-header>

    <div class="gap-6 transition-all duration-500" id="projector-container"
        :class="isFullscreen ? 'flex flex-col bg-base-100 p-8 h-screen w-screen fixed top-0 right-0 z-[100] overflow-hidden' :
            'grid grid-cols-1 md:grid-cols-3'">

        {{-- Custom Toast overlay strictly for Fullscreen Mode --}}
        <div x-data="{ toastMessage: '', showToast: false }"
            @mary-toast.window="if(isFullscreen) { toastMessage = $event.detail.title || ($event.detail[0] && $event.detail[0].title) || 'تم'; showToast = true; setTimeout(() => showToast = false, 3000) }"
            x-show="showToast" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4"
            class="absolute top-8 left-8 z-[999] pointer-events-none">
            <div class="alert alert-success text-white shadow-xl flex items-center gap-3">
                <x-icon name="o-check-circle" class="w-6 h-6" />
                <span x-text="toastMessage" class="font-bold text-lg"></span>
            </div>
        </div>

        {{-- Right Column (Question) --}}
        <div class="flex flex-col gap-6 min-w-0 transition-all duration-500 w-full"
            :class="isFullscreen ? 'flex-1 h-[calc(100vh-4rem)] overflow-y-auto pr-2' : 'md:col-span-2'">
            <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 p-6 flex flex-col h-fit shrink-0 w-full min-w-0"
                :class="isFullscreen ? 'min-h-full justify-center' : ''">
                <div class="text-center mb-8 w-full min-w-0">
                    <div class="flex items-center justify-between mb-4">
                        <div></div>
                        <span
                            class="badge badge-primary">{{ \App\Enums\SubjectEnum::coerce($question->subject)?->title() ?? $question->subject }}
                            - {{ __('lang.grade') ?? 'الصف' }} {{ $question->grade }}</span>
                        <div x-show="isFullscreen" class="flex items-center gap-1 bg-base-200 rounded-lg p-1"
                            dir="ltr">
                            <button @click="fontSize = Math.max(24, fontSize - 4)"
                                class="btn btn-ghost btn-xs btn-circle"><x-icon name="o-minus"
                                    class="w-3 h-3" /></button>
                            <x-icon name="o-language" class="w-4 h-4 text-gray-500" />
                            <button @click="fontSize += 4" class="btn btn-ghost btn-xs btn-circle"><x-icon
                                    name="o-plus" class="w-3 h-3" /></button>
                        </div>
                        <div x-show="!isFullscreen"></div>
                    </div>
                    <h2 class="font-bold leading-relaxed text-base-content whitespace-pre-wrap break-all w-full"
                        :class="isFullscreen ? 'mb-12' : 'text-3xl mb-6'"
                        :style="isFullscreen ? `font-size: ${fontSize}px; line-height: 1.6;` : ''">
                        {{ $question->content }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" :class="isFullscreen ? 'gap-8 px-12' : ''">
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300"
                        :class="{
                            'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'A'
                            === '{{ $question->correct_option }}',
                            'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'A'
                            !== '{{ $question->correct_option }}',
                            'bg-base-200/50 border-base-200': !showAnswer,
                            'p-8 text-3xl': isFullscreen
                        }">
                        <span class="font-bold text-2xl shrink-0"
                            :class="showAnswer && 'A'
                            === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ?
                                'text-4xl' : ''">A</span>
                        <span class="break-all whitespace-pre-wrap w-full">{{ $question->option_a }}</span>
                    </div>
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300"
                        :class="{
                            'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'B'
                            === '{{ $question->correct_option }}',
                            'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'B'
                            !== '{{ $question->correct_option }}',
                            'bg-base-200/50 border-base-200': !showAnswer,
                            'p-8 text-3xl': isFullscreen
                        }">
                        <span class="font-bold text-2xl shrink-0"
                            :class="showAnswer && 'B'
                            === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ?
                                'text-4xl' : ''">B</span>
                        <span class="break-all whitespace-pre-wrap w-full">{{ $question->option_b }}</span>
                    </div>
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300"
                        :class="{
                            'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'C'
                            === '{{ $question->correct_option }}',
                            'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'C'
                            !== '{{ $question->correct_option }}',
                            'bg-base-200/50 border-base-200': !showAnswer,
                            'p-8 text-3xl': isFullscreen
                        }">
                        <span class="font-bold text-2xl shrink-0"
                            :class="showAnswer && 'C'
                            === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ?
                                'text-4xl' : ''">C</span>
                        <span class="break-all whitespace-pre-wrap w-full">{{ $question->option_c }}</span>
                    </div>
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300"
                        :class="{
                            'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'D'
                            === '{{ $question->correct_option }}',
                            'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'D'
                            !== '{{ $question->correct_option }}',
                            'bg-base-200/50 border-base-200': !showAnswer,
                            'p-8 text-3xl': isFullscreen
                        }">
                        <span class="font-bold text-2xl shrink-0"
                            :class="showAnswer && 'D'
                            === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ?
                                'text-4xl' : ''">D</span>
                        <span class="break-all whitespace-pre-wrap w-full">{{ $question->option_d }}</span>
                    </div>
                </div>
            </div>

            <div x-show="isFullscreen" class="flex justify-center items-center gap-4 mt-8 pb-8 shrink-0">
                <x-button icon="o-check-circle" class="btn-success btn-lg text-white" @click="showAnswer = true"
                    x-show="!showAnswer">{{ __('lang.show_answer') ?? 'إظهار الإجابة الصحيحة' }}</x-button>
                <x-button icon="o-eye-slash" class="btn-warning btn-lg" @click="showAnswer = false"
                    x-show="showAnswer">{{ __('lang.hide_answer') ?? 'إخفاء الإجابة' }}</x-button>
                <x-button icon="o-arrows-pointing-in" class="btn-error btn-lg"
                    @click="toggleFullscreen">{{ __('lang.exit_fullscreen') ?? 'الخروج' }}</x-button>
            </div>
        </div>

        {{-- Left Column (Students List) --}}
        <div x-show="!isFullscreen"
            class="md:col-span-1 bg-base-100 rounded-xl shadow-sm border border-base-200 flex flex-col overflow-hidden h-[800px]">
            <div class="p-4 border-b border-base-200 bg-base-50/50 rounded-t-xl sticky top-0 z-10 shrink-0">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                    <x-icon name="o-users" class="w-5 h-5 text-primary" />
                    {{ __('lang.students') ?? 'الطلاب' }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input wire:model.live.debounce.300ms="search_student"
                        placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }}" icon="o-magnifying-glass"
                        clearable class="shrink-0 w-full" />
                    <x-input type="number" wire:model.live.debounce.300ms="search_semester"
                        placeholder="{{ __('lang.semester') ?? 'الفصل' }}" icon="o-hashtag" clearable
                        class="shrink-0 w-full" />
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-2">
                <div class="flex flex-col gap-2">
                    @forelse($students as $student)
                        <div
                            class="bg-base-200/30 p-3 rounded-lg border border-base-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 hover:bg-base-200/50 transition-colors">
                            <div class="flex flex-col gap-1">
                                <span class="font-semibold text-base-content">{{ $student->name }}</span>
                                @if($student->semester)
                                    <span class="badge badge-sm badge-outline text-gray-500 text-[10px]">{{ __('lang.semester') ?? 'الفصل' }}: {{ $student->semester }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 dir-ltr w-full sm:w-auto justify-end">
                                <button wire:click="recordAnswer({{ $student->id }}, 'A')"
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'A' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    A
                                </button>
                                <button wire:click="recordAnswer({{ $student->id }}, 'B')"
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'B' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    B
                                </button>
                                <button wire:click="recordAnswer({{ $student->id }}, 'C')"
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'C' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    C
                                </button>
                                <button wire:click="recordAnswer({{ $student->id }}, 'D')"
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'D' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    D
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-500">
                            {{ __('lang.no_data') ?? 'لا يوجد طلاب مطابقين للبحث' }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
