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
            
            <div class="w-64">
                <x-select 
                    wire:model.live="selected_cycle_id" 
                    :options="$cycles" 
                    option-value="id" 
                    option-label="name" 
                    placeholder="{{ __('lang.select_cycle') ?? 'اختر الدورة...' }}" 
                />
            </div>
        </div>

        @if($selected_cycle_id)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach($weekly_progress as $week => $data)
                    <div class="card bg-base-100 shadow-sm border border-base-200 p-4 flex flex-col items-center justify-center text-center relative overflow-hidden group hover:border-primary transition-all">
                        <h4 class="font-bold text-gray-700 mb-2">{{ __('lang.week') ?? 'الأسبوع' }} {{ $week }}</h4>
                        
                        @if($data['total_answered'] > 0)
                            <div class="mb-3 relative w-16 h-16 flex items-center justify-center">
                                @if($data['badge'])
                                    <x-icon name="s-star" class="w-14 h-14" style="color: {{ $data['badge']['color_hex'] }}; drop-shadow(0 4px 6px {{ $data['badge']['color_hex'] }}40)" />
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
                        @else
                            <div class="flex-1 flex flex-col items-center justify-center py-4">
                                <x-icon name="o-minus-circle" class="w-8 h-8 text-gray-300 mb-2" />
                                <span class="text-xs text-gray-400">{{ __('lang.no_answers') ?? 'لم يجب على أي سؤال' }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
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
