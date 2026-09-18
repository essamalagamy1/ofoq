<div>
    <x-header title="{{ __('lang.questions') ?? 'بنك الأسئلة' }}" separator>
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-4">
                <x-input wire:model.live.debounce.500ms="search_content" placeholder="{{ __('lang.search_by_question') ?? 'بحث في السؤال' }}" icon="o-magnifying-glass" clearable class="w-48" />
                
                <x-select 
                    wire:model.live="filter_cycle_id" 
                    :options="$all_cycles" 
                    option-value="id" 
                    option-label="name" 
                    placeholder="{{ __('lang.all_cycles') ?? 'كل الدورات' }}" 
                    class="w-40"
                />

                <x-select 
                    wire:model.live="filter_subject" 
                    :options="collect(\App\Enums\SubjectEnum::getInstances())->map(fn($e) => ['value' => $e->value, 'title' => $e->title()])" 
                    option-value="value" 
                    option-label="title" 
                    placeholder="{{ __('lang.all_subjects') ?? 'كل المواد' }}" 
                    class="w-32"
                />

                <x-select 
                    wire:model.live="filter_grade" 
                    :options="[
                        ['id' => 3, 'name' => '3'],
                        ['id' => 4, 'name' => '4'],
                        ['id' => 5, 'name' => '5'],
                        ['id' => 6, 'name' => '6'],
                    ]"
                    option-value="id" 
                    option-label="name" 
                    placeholder="{{ __('lang.all_grades') ?? 'كل الصفوف' }}" 
                    class="w-32"
                />

                <x-button icon="o-plus" class="btn-primary" wire:click="checkActiveCycleAndOpenCreateModal">{{ __('lang.add') ?? 'إضافة' }}</x-button>
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
                        <th class="py-3 px-4">{{ __('lang.cycle') ?? 'الدورة' }}</th>
                        <th class="py-3 px-4">{{ __('lang.subject') ?? 'المادة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.grade') ?? 'الصف' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.week') ?? 'الأسبوع' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $question->id }}</td>
                            <td class="py-3 px-4">
                                <div class="max-w-xs truncate" title="{{ $question->content }}">
                                    {{ $question->content }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $question->cycle->name ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="badge badge-outline badge-primary">
                                    {{ \App\Enums\SubjectEnum::tryFrom($question->subject)?->title() ?? $question->subject }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">{{ $question->grade }}</td>
                            <td class="py-3 px-4 text-center">{{ $question->week }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info" wire:click="$dispatch('open-update-modal', { question: {{ $question->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="delete({{ $question->id }})" wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                {{ __('lang.no_data') ?? 'لا توجد بيانات متاحة' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($questions->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $questions->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.question.create-question')
    @livewire('dashboard.question.update-question')
</div>
