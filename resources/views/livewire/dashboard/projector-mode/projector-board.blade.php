<div class="min-h-screen bg-base-200 p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
        <x-header title="{{ __('lang.projector_mode') ?? 'وضع البروجيكتور' }}" subtitle="{{ __('lang.projector_hint') ?? 'اعرض الأسئلة بوضوح لطلابك داخل الفصل' }}" separator>
            <x-slot:actions>
                <div class="flex flex-wrap items-center gap-4 bg-base-100 p-2 rounded-lg shadow-sm border border-base-200">
                    <x-select 
                        wire:model="selected_cycle_id" 
                        :options="$cycles" 
                        option-value="id" 
                        option-label="name" 
                        placeholder="{{ __('lang.select_cycle') ?? 'اختر الدورة...' }}" 
                        class="w-32"
                    />

                    <x-select 
                        wire:model="selected_subject" 
                        :options="collect(\App\Enums\SubjectEnum::getInstances())->map(fn($e) => ['value' => $e->value, 'title' => $e->title()])" 
                        option-value="value" 
                        option-label="title" 
                        placeholder="{{ __('lang.subject') ?? 'المادة' }}" 
                        class="w-32"
                    />

                    <x-select 
                        wire:model="selected_grade" 
                        :options="[
                            ['id' => 3, 'name' => '3'],
                            ['id' => 4, 'name' => '4'],
                            ['id' => 5, 'name' => '5'],
                            ['id' => 6, 'name' => '6'],
                        ]"
                        option-value="id" 
                        option-label="name" 
                        placeholder="{{ __('lang.grade') ?? 'الصف' }}" 
                        class="w-24"
                    />

                    <x-select 
                        wire:model="selected_week" 
                        :options="collect(range(1, 12))->map(fn($w) => ['id' => $w, 'name' => $w])->toArray()"
                        option-value="id" 
                        option-label="name" 
                        placeholder="{{ __('lang.week') ?? 'الأسبوع' }}" 
                        class="w-24"
                    />

                    <x-button icon="o-play" class="btn-primary" wire:click="loadQuestion" spinner>{{ __('lang.show_question') ?? 'عرض السؤال' }}</x-button>
                </div>
            </x-slot:actions>
        </x-header>

        <div class="mt-8">
            @if($current_question)
                <div class="card bg-base-100 shadow-xl border-t-4 border-primary p-8 md:p-12">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-5xl font-bold leading-tight text-gray-800">
                            {{ $current_question->content }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        @foreach(['A' => $current_question->option_a, 'B' => $current_question->option_b, 'C' => $current_question->option_c, 'D' => $current_question->option_d] as $letter => $option)
                            @php
                                $isCorrect = $current_question->correct_option === $letter;
                                $showAsCorrect = $show_answer && $isCorrect;
                                $showAsWrong = $show_answer && !$isCorrect;
                                
                                $cardClass = 'bg-base-200 border-base-300 text-gray-700';
                                if ($showAsCorrect) {
                                    $cardClass = 'bg-success text-white border-success scale-105 shadow-lg';
                                } elseif ($showAsWrong) {
                                    $cardClass = 'bg-base-200 border-base-300 text-gray-400 opacity-50';
                                }
                            @endphp
                            
                            <div class="card {{ $cardClass }} border-2 p-6 rounded-2xl flex flex-row items-center gap-6 transition-all duration-500 ease-in-out">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl font-bold {{ $showAsCorrect ? 'bg-white text-success' : 'bg-base-300 text-gray-600' }}">
                                    {{ $letter }}
                                </div>
                                <div class="text-2xl md:text-3xl font-semibold flex-1">
                                    {{ $option }}
                                </div>
                                @if($showAsCorrect)
                                    <x-icon name="o-check-circle" class="w-12 h-12 text-white" />
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-16 text-center">
                        @if(!$show_answer)
                            <x-button icon="o-eye" class="btn-success btn-lg text-white text-xl px-12" wire:click="revealAnswer" spinner>{{ __('lang.reveal_answer') ?? 'إظهار الإجابة' }}</x-button>
                        @else
                            <x-button icon="o-arrow-path" class="btn-primary btn-lg text-xl px-12" wire:click="loadQuestion" spinner>{{ __('lang.next_question') ?? 'السؤال التالي' }}</x-button>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-20 text-gray-500 bg-base-100 rounded-2xl border-2 border-dashed border-base-300">
                    <x-icon name="o-presentation-chart-bar" class="w-24 h-24 mb-6 text-gray-300" />
                    <h3 class="text-2xl font-bold">{{ __('lang.projector_ready') ?? 'البروجيكتور جاهز' }}</h3>
                    <p class="text-lg mt-2">{{ __('lang.select_options_to_start') ?? 'الرجاء تحديد الدورة والمادة والصف والأسبوع للبدء بعرض الأسئلة.' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
