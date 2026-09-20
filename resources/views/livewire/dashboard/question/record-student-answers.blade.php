<div x-data="projectorMode()">
    <x-header title="{{ __('lang.record_answers') ?? 'تسجيل الإجابات' }}" separator>
        <x-slot:actions>
            <x-button icon="o-arrow-left" class="btn-ghost btn-sm sm:btn-md" link="{{ route('teacher.questions') }}">{{ __('lang.back') ?? 'رجوع' }}</x-button>
            <x-button icon="o-arrows-pointing-out" class="btn-primary btn-sm sm:btn-md" @click="toggleFullscreen" x-show="!isFullscreen">{{ __('lang.fullscreen') ?? 'ملء الشاشة' }}</x-button>
            <x-button icon="o-arrows-pointing-in" class="btn-error btn-sm sm:btn-md" @click="toggleFullscreen" x-show="isFullscreen">{{ __('lang.exit_fullscreen') ?? 'خروج' }}</x-button>
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="projector-container" :class="isFullscreen ? 'bg-base-100 p-8 h-screen w-screen fixed top-0 right-0 z-[100] overflow-hidden' : ''">
        
        {{-- Custom Toast overlay strictly for Fullscreen Mode --}}
        <div x-data="{ toastMessage: '', showToast: false }" 
             @mary-toast.window="if(isFullscreen) { toastMessage = $event.detail.title || ($event.detail[0] && $event.detail[0].title) || 'تم'; showToast = true; setTimeout(() => showToast = false, 3000) }" 
             x-show="showToast" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
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
        <div class="md:col-span-2 flex flex-col gap-6" :class="isFullscreen ? 'h-[calc(100vh-4rem)] overflow-y-auto pr-2' : ''">
            <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 p-6 flex flex-col h-fit shrink-0">
                <div class="text-center mb-8">
                    <span class="badge badge-primary mb-4">{{ \App\Enums\SubjectEnum::coerce($question->subject)?->title() ?? $question->subject }} - {{ __('lang.grade') ?? 'الصف' }} {{ $question->grade }}</span>
                    <h2 class="text-3xl font-bold leading-relaxed text-base-content" :class="isFullscreen ? 'text-5xl mb-12' : 'mb-6'">{{ $question->content }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" :class="isFullscreen ? 'gap-8 px-12' : ''">
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300" 
                         :class="{
                             'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'A' === '{{ $question->correct_option }}',
                             'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'A' !== '{{ $question->correct_option }}',
                             'bg-base-200/50 border-base-200': !showAnswer,
                             'p-8 text-3xl': isFullscreen 
                         }">
                        <span class="font-bold text-2xl" :class="showAnswer && 'A' === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ? 'text-4xl' : ''">A</span>
                        <span>{{ $question->option_a }}</span>
                    </div>
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300" 
                         :class="{
                             'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'B' === '{{ $question->correct_option }}',
                             'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'B' !== '{{ $question->correct_option }}',
                             'bg-base-200/50 border-base-200': !showAnswer,
                             'p-8 text-3xl': isFullscreen 
                         }">
                        <span class="font-bold text-2xl" :class="showAnswer && 'B' === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ? 'text-4xl' : ''">B</span>
                        <span>{{ $question->option_b }}</span>
                    </div>
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300" 
                         :class="{
                             'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'C' === '{{ $question->correct_option }}',
                             'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'C' !== '{{ $question->correct_option }}',
                             'bg-base-200/50 border-base-200': !showAnswer,
                             'p-8 text-3xl': isFullscreen 
                         }">
                        <span class="font-bold text-2xl" :class="showAnswer && 'C' === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ? 'text-4xl' : ''">C</span>
                        <span>{{ $question->option_c }}</span>
                    </div>
                    <div class="p-4 rounded-xl border flex items-center gap-4 text-xl transition-all duration-300" 
                         :class="{
                             'bg-success/20 border-success text-success scale-[1.02]': showAnswer && 'D' === '{{ $question->correct_option }}',
                             'opacity-40 bg-base-200/50 border-base-200': showAnswer && 'D' !== '{{ $question->correct_option }}',
                             'bg-base-200/50 border-base-200': !showAnswer,
                             'p-8 text-3xl': isFullscreen 
                         }">
                        <span class="font-bold text-2xl" :class="showAnswer && 'D' === '{{ $question->correct_option }}' ? 'text-success' : 'text-primary', isFullscreen ? 'text-4xl' : ''">D</span>
                        <span>{{ $question->option_d }}</span>
                    </div>
                </div>
            </div>
            
            <div x-show="isFullscreen" class="flex justify-center items-center gap-4 mt-8 pb-8 shrink-0">
                <x-button icon="o-check-circle" class="btn-success btn-lg text-white" @click="showAnswer = true" x-show="!showAnswer">{{ __('lang.show_answer') ?? 'إظهار الإجابة الصحيحة' }}</x-button>
                <x-button icon="o-eye-slash" class="btn-warning btn-lg" @click="showAnswer = false" x-show="showAnswer">{{ __('lang.hide_answer') ?? 'إخفاء الإجابة' }}</x-button>
                <x-button icon="o-arrows-pointing-in" class="btn-error btn-lg" @click="toggleFullscreen">{{ __('lang.exit_fullscreen') ?? 'الخروج' }}</x-button>
            </div>
        </div>

        {{-- Left Column (Students List) --}}
        <div class="md:col-span-1 bg-base-100 rounded-xl shadow-sm border border-base-200 flex flex-col overflow-hidden" :class="isFullscreen ? 'h-[calc(100vh-4rem)] border-l-2' : 'h-[800px]'">
            <div class="p-4 border-b border-base-200 bg-base-50/50 rounded-t-xl sticky top-0 z-10 shrink-0">
                <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                    <x-icon name="o-users" class="w-5 h-5 text-primary" />
                    {{ __('lang.students') ?? 'الطلاب' }}
                </h3>
                <x-input wire:model.live.debounce.300ms="search_student" placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }}" icon="o-magnifying-glass" clearable class="w-full" />
            </div>

            <div class="flex-1 overflow-y-auto p-2">
                <div class="flex flex-col gap-2">
                    @forelse($students as $student)
                        <div class="bg-base-200/30 p-3 rounded-lg border border-base-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 hover:bg-base-200/50 transition-colors">
                            <span class="font-semibold text-base-content">{{ $student->name }}</span>
                            
                            <div class="flex items-center gap-1 dir-ltr w-full sm:w-auto justify-end">
                                <button 
                                    wire:click="recordAnswer({{ $student->id }}, 'A')" 
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'A' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    A
                                </button>
                                <button 
                                    wire:click="recordAnswer({{ $student->id }}, 'B')" 
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'B' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    B
                                </button>
                                <button 
                                    wire:click="recordAnswer({{ $student->id }}, 'C')" 
                                    class="btn btn-sm btn-circle {{ isset($answers[$student->id]) && $answers[$student->id] === 'C' ? 'btn-primary' : 'btn-outline border-gray-300' }}">
                                    C
                                </button>
                                <button 
                                    wire:click="recordAnswer({{ $student->id }}, 'D')" 
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('projectorMode', () => ({
                isFullscreen: false,
                showAnswer: false,
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
                    let elem = document.getElementById("projector-container");
                    
                    if (!this.isFullscreen) {
                        if (elem.requestFullscreen) {
                            elem.requestFullscreen();
                        } else if (elem.webkitRequestFullscreen) { /* Safari */
                            elem.webkitRequestFullscreen();
                        } else if (elem.msRequestFullscreen) { /* IE11 */
                            elem.msRequestFullscreen();
                        }
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        } else if (document.webkitExitFullscreen) { /* Safari */
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) { /* IE11 */
                            document.msExitFullscreen();
                        }
                    }
                }
            }))
        })
    </script>
</div>
