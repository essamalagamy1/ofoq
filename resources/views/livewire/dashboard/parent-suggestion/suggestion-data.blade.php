<div>
    <x-header title="{{ __('lang.parent_suggestions') ?? 'مقترحات أولياء الأمور' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-4">
                <x-select 
                    wire:model.live="filter_status" 
                    :options="collect(\App\Enums\SuggestionStatusEnum::getInstances())->map(fn($e) => ['value' => $e->value, 'title' => $e->title()])" 
                    option-value="value" 
                    option-label="title" 
                    placeholder="{{ __('lang.all_statuses') ?? 'كل الحالات' }}" 
                    class="w-40"
                />
            </div>
        </x-slot:actions>
    </x-header>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">{{ __('lang.question') ?? 'السؤال' }}</th>
                        <th class="py-3 px-4">{{ __('lang.parent') ?? 'ولي الأمر' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.grade') ?? 'الصف' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.status') ?? 'الحالة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $suggestion)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $suggestion->id }}</td>
                            <td class="py-3 px-4">
                                <div class="max-w-md truncate" title="{{ $suggestion->content }}">
                                    {{ $suggestion->content }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $suggestion->creator->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">{{ $suggestion->grade }}</td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $statusEnum = \App\Enums\SuggestionStatusEnum::coerce($suggestion->status);
                                    $color = $statusEnum ? $statusEnum->color() : 'gray-500';
                                @endphp
                                <span class="badge text-white bg-{{ $color }} border-{{ $color }}">
                                    {{ $statusEnum ? $statusEnum->title() : $suggestion->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-eye" class="btn-sm btn-ghost text-info" wire:click="openReviewModal({{ $suggestion->id }})" tooltip="{{ __('lang.review') ?? 'مراجعة' }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                {{ __('lang.no_data') ?? 'لا توجد بيانات متاحة' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($suggestions->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $suggestions->links() }}
            </div>
        @endif
    </div>

    <!-- Review Modal -->
    <x-modal wire:model="review_modal" title="{{ __('lang.review_suggestion') ?? 'مراجعة الاقتراح' }}" separator box-class="max-w-3xl">
        
        @if($selected_suggestion)
            <div class="bg-base-200 p-4 rounded-lg mb-4">
                <div class="mb-4">
                    <span class="text-gray-500 text-sm">{{ __('lang.parent') ?? 'ولي الأمر' }}:</span> 
                    <strong>{{ $selected_suggestion->creator->name ?? '-' }}</strong>
                </div>
                
                <h3 class="font-bold text-lg mb-2">{{ __('lang.question_content') ?? 'نص السؤال' }}:</h3>
                <p class="text-lg">{{ $selected_suggestion->content }}</p>

                <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                    <div class="p-2 border border-base-300 rounded {{ $selected_suggestion->correct_option === 'A' ? 'bg-success/20 border-success text-success-content font-bold' : '' }}">
                        <span class="font-bold mr-2">A:</span> {{ $selected_suggestion->option_a }}
                    </div>
                    <div class="p-2 border border-base-300 rounded {{ $selected_suggestion->correct_option === 'B' ? 'bg-success/20 border-success text-success-content font-bold' : '' }}">
                        <span class="font-bold mr-2">B:</span> {{ $selected_suggestion->option_b }}
                    </div>
                    <div class="p-2 border border-base-300 rounded {{ $selected_suggestion->correct_option === 'C' ? 'bg-success/20 border-success text-success-content font-bold' : '' }}">
                        <span class="font-bold mr-2">C:</span> {{ $selected_suggestion->option_c }}
                    </div>
                    <div class="p-2 border border-base-300 rounded {{ $selected_suggestion->correct_option === 'D' ? 'bg-success/20 border-success text-success-content font-bold' : '' }}">
                        <span class="font-bold mr-2">D:</span> {{ $selected_suggestion->option_d }}
                    </div>
                </div>
            </div>

            @if($selected_suggestion->status === \App\Enums\SuggestionStatusEnum::Pending)
                <div class="mt-4">
                    <x-textarea label="{{ __('lang.teacher_comment') ?? 'تعليق المعلم' }}" wire:model="teacher_comment" placeholder="{{ __('lang.teacher_comment_hint') ?? 'اكتب تعليقك هنا (مطلوب في حالة الرفض)' }}" rows="3" />
                </div>
            @else
                <div class="mt-4 p-4 border border-base-300 rounded-lg">
                    <h4 class="font-bold text-gray-700">{{ __('lang.teacher_comment') ?? 'تعليق المعلم' }}:</h4>
                    <p class="mt-1 text-gray-600">{{ $selected_suggestion->teacher_comment ?: '-' }}</p>
                </div>
            @endif
        @endif

        <x-slot:actions>
            <x-button label="{{ __('lang.close') ?? 'إغلاق' }}" @click="$wire.review_modal = false" />
            
            @if($selected_suggestion && $selected_suggestion->status === \App\Enums\SuggestionStatusEnum::Pending)
                <x-button label="{{ __('lang.reject') ?? 'رفض' }}" class="btn-error" wire:click="reject" spinner />
                <x-button label="{{ __('lang.approve') ?? 'قبول وإضافة' }}" class="btn-success" wire:click="approve" spinner />
            @endif
        </x-slot:actions>
    </x-modal>
</div>
