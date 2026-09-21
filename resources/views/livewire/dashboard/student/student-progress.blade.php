<div>
    <x-modal wire:model="show_modal" title="{{ __('lang.student_progress') ?? 'تقدم الطالب' }} - {{ $student->name ?? '' }}" separator box-class="max-w-6xl">
        
        <div class="mb-6 bg-base-200 p-4 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-4">
                <x-icon name="o-academic-cap" class="w-8 h-8 text-primary" />
                <div>
                    <h3 class="font-bold text-lg">{{ __('lang.track_badges') ?? 'تتبع الشارات الأسبوعية' }}</h3>
                    <p class="text-sm text-gray-500">{{ __('lang.badges_hint') ?? 'تُحسب الشارة بناءً على نسبة الإجابات الصحيحة في كل أسبوع.' }}</p>
                </div>
            </div>
            
            @unless(auth()->user()->hasRole('parent'))
                <div class="w-64">
                    <x-select 
                        wire:model.live="selected_cycle_id" 
                        :options="$cycles" 
                        option-value="id" 
                        option-label="name" 
                        placeholder="{{ __('lang.select_cycle') ?? 'اختر الدورة...' }}" 
                    />
                </div>
            @endunless
        </div>

        @if($selected_cycle_id)
            {{-- Overall Cycle Progress (Hidden for Parents) --}}
            @unless(auth()->user()->hasRole('parent'))
                @if($overall_progress)
                <div class="mb-6 bg-base-100 rounded-xl shadow-md border-2 border-primary/20 p-6 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/5 rounded-full blur-2xl"></div>
                    
                    <div class="flex items-center gap-6 z-10">
                        <div class="relative w-24 h-24 flex items-center justify-center shrink-0">
                            @if($overall_progress['badge'])
                                @if(!empty($overall_progress['badge']['image']))
                                    <img src="{{ $overall_progress['badge']['image'] }}" class="w-20 h-20 object-cover rounded-full shadow-lg" alt="{{ $overall_progress['badge']['name'] }}" />
                                @else
                                    <x-icon name="s-star" class="w-20 h-20" style="color: {{ $overall_progress['badge']['color_hex'] }}; drop-shadow(0 6px 8px {{ $overall_progress['badge']['color_hex'] }}50)" />
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full border-4 border-gray-200 flex items-center justify-center bg-gray-50 shadow-inner">
                                    <x-icon name="o-x-mark" class="w-10 h-10 text-gray-400" />
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <h4 class="text-2xl font-bold text-gray-800 mb-1">{{ __('lang.overall_cycle_evaluation') ?? 'التقييم الإجمالي للدورة' }}</h4>
                            @if($overall_progress['badge'])
                                <div class="flex items-center gap-2">
                                    <span class="badge font-bold text-white badge-lg" style="background-color: {{ $overall_progress['badge']['color_hex'] }}; border-color: {{ $overall_progress['badge']['color_hex'] }};">
                                        {{ $overall_progress['badge']['name'] }}
                                    </span>
                                    <span class="text-gray-500 text-sm">{{ __('lang.based_on_all_weeks') ?? 'بناءً على أداء جميع الأسابيع' }}</span>
                                </div>
                            @else
                                <span class="badge badge-ghost text-gray-500">{{ __('lang.no_badge') ?? 'لا توجد شارة' }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-row md:flex-col gap-4 md:gap-2 text-center md:text-end z-10 bg-base-200/50 p-4 rounded-xl">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">{{ __('lang.total_score') ?? 'النسبة الإجمالية' }}</p>
                            <p class="text-3xl font-black text-primary">{{ $overall_progress['percentage'] }}%</p>
                        </div>
                        <div class="divider m-0 md:hidden"></div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">{{ __('lang.answers') ?? 'الإجابات' }}</p>
                            <p class="text-lg font-bold text-gray-700 dir-ltr">{{ $overall_progress['correct_answers'] }} / {{ $overall_progress['total_answered'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
            @endunless

            @if($selected_week)
                <div class="mb-4 flex items-center justify-between">
                    <h4 class="font-bold text-xl text-primary">{{ __('lang.week') ?? 'الأسبوع' }} {{ $selected_week }} - {{ __('lang.details') ?? 'التفاصيل' }}</h4>
                    <x-button icon="o-arrow-right" class="btn-sm btn-ghost" wire:click="backToWeeks">
                        {{ __('lang.back') ?? 'رجوع' }}
                    </x-button>
                </div>
                
                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    @forelse($week_details as $answer)
                        <div class="bg-base-100 p-4 rounded-xl border {{ $answer->is_correct ? 'border-success/50 bg-success/5' : 'border-error/50 bg-error/5' }}">
                            <div class="mb-3">
                                <span class="badge badge-primary badge-sm mb-2">{{ \App\Enums\SubjectEnum::coerce($answer->question->subject)?->title() ?? $answer->question->subject }}</span>
                                <h5 class="font-bold text-lg leading-relaxed">{{ $answer->question->content }}</h5>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mt-4">
                                <div class="p-3 rounded-lg bg-base-200/50 border border-base-300">
                                    <span class="text-gray-500 block mb-1">{{ __('lang.student_answer') ?? 'إجابة الطالب' }}</span>
                                    <div class="flex items-center gap-2">
                                        @if($answer->is_correct)
                                            <x-icon name="o-check-circle" class="w-5 h-5 text-success" />
                                            <span class="font-bold text-success">{{ $answer->question->{'option_' . strtolower($answer->selected_option)} ?? $answer->selected_option }}</span>
                                        @else
                                            <x-icon name="o-x-circle" class="w-5 h-5 text-error" />
                                            <span class="font-bold text-error">{{ $answer->question->{'option_' . strtolower($answer->selected_option)} ?? $answer->selected_option }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-3 rounded-lg bg-success/10 border border-success/30">
                                    <span class="text-gray-500 block mb-1">{{ __('lang.correct_answer') ?? 'الإجابة الصحيحة' }}</span>
                                    <div class="flex items-center gap-2">
                                        <x-icon name="o-check-circle" class="w-5 h-5 text-success" />
                                        <span class="font-bold text-success">{{ $answer->question->{'option_' . strtolower($answer->question->correct_option)} ?? $answer->question->correct_option }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            {{ __('lang.no_data') ?? 'لا توجد بيانات' }}
                        </div>
                    @endforelse
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($weekly_progress as $week => $data)
                        @php
                            $isClickable = $selected_cycle_id === $active_cycle_id && $week < $active_cycle_week && $data['total_answered'] > 0;
                        @endphp
                        <div class="card bg-base-100 shadow-sm border border-base-200 p-4 flex flex-col items-center justify-center text-center relative overflow-hidden group {{ $isClickable ? 'hover:border-primary cursor-pointer hover:shadow-md transition-all' : '' }}"
                             @if($isClickable) wire:click="showWeekDetails({{ $week }})" @endif>
                            
                            <h4 class="font-bold text-gray-700 mb-2">{{ __('lang.week') ?? 'الأسبوع' }} {{ $week }}</h4>
                        
                        @if($data['total_answered'] > 0)
                            <div class="mb-3 relative w-16 h-16 flex items-center justify-center">
                                @if($data['badge'])
                                    @if(!empty($data['badge']['image']))
                                        <img src="{{ $data['badge']['image'] }}" class="w-14 h-14 object-cover rounded-full shadow-md" alt="{{ $data['badge']['name'] }}" />
                                    @else
                                        <x-icon name="s-star" class="w-14 h-14" style="color: {{ $data['badge']['color_hex'] }}; drop-shadow(0 4px 6px {{ $data['badge']['color_hex'] }}40)" />
                                    @endif
                                @else
                                    <div class="w-12 h-12 rounded-full border-4 border-gray-200 flex items-center justify-center bg-gray-50">
                                        <x-icon name="o-x-mark" class="w-6 h-6 text-gray-400" />
                                    </div>
                                @endif
                            </div>
                            
                            @if($data['badge'])
                                <span class="badge font-bold mb-2 text-white" style="background-color: {{ $data['badge']['color_hex'] }}; border-color: {{ $data['badge']['color_hex'] }};">
                                    {{ $data['badge']['name'] }}
                                </span>
                            @else
                                <span class="badge badge-ghost mb-2 text-gray-500 text-xs">{{ __('lang.no_badge') ?? 'لا توجد شارة' }}</span>
                            @endif

                            <div class="text-xs text-gray-500 flex flex-col gap-1">
                                <span class="font-bold text-gray-700">{{ $data['percentage'] }}%</span>
                                <span>{{ $data['correct_answers'] }} / {{ $data['total_answered'] }} {{ __('lang.correct') ?? 'صحيح' }}</span>
                            </div>
                            
                            @if($isClickable)
                                <div class="absolute inset-0 bg-primary/90 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <x-icon name="o-eye" class="w-8 h-8 text-primary-content mb-2" />
                                    <span class="text-primary-content font-bold text-sm">{{ __('lang.view_details') ?? 'عرض التفاصيل' }}</span>
                                </div>
                            @endif
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center py-4">
                                <x-icon name="o-minus-circle" class="w-8 h-8 text-gray-300 mb-2" />
                                <span class="text-xs text-gray-400">{{ __('lang.no_answers') ?? 'لم يجب على أي سؤال' }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
        @else
            <div class="py-12 text-center text-gray-500">
                <x-icon name="o-inbox" class="w-12 h-12 mx-auto text-gray-300 mb-4" />
                <p>{{ __('lang.no_cycles_available') ?? 'لا توجد دورات أكاديمية متاحة لعرض التقدم.' }}</p>
            </div>
        @endif

        <x-slot:actions>
            <x-button label="{{ __('lang.close') ?? 'إغلاق' }}" @click="$wire.show_modal = false" />
        </x-slot:actions>
    </x-modal>
</div>
