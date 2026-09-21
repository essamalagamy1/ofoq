<div>
    <x-header title="{{ __('lang.parent_suggestions') ?? 'مقترحات أولياء الأمور' }}" separator>
    </x-header>

    <div class="grid grid-cols-1 gap-4 mb-6">
        <x-stat title="{{ __('lang.total') ?? 'إجمالي المقترحات' }}" value="{{ $stats['total'] }}" icon="o-document-text" class="bg-base-100 shadow-sm border border-base-200" />
    </div>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">{{ __('lang.question') ?? 'السؤال' }}</th>
                        <th class="py-3 px-4">{{ __('lang.parent') ?? 'ولي الأمر' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.grade') ?? 'الصف' }}</th>
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
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-chat-bubble-bottom-center-text" class="btn-sm btn-info text-white" wire:click="openCommentModal({{ $suggestion->id }})" tooltip="{{ __('lang.add_comment') ?? 'إضافة تعليق' }}" spinner />
                                    <x-button icon="o-plus-circle" class="btn-sm btn-success text-white" wire:click="addToQuestions({{ $suggestion->id }})" tooltip="{{ __('lang.add_to_questions') ?? 'إضافة لأسئلة الطلاب' }}" spinner />
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

    <!-- Comment Modal -->
    <x-modal wire:model="comment_modal" title="{{ __('lang.add_comment') ?? 'إضافة تعليق على مقترح' }}" separator>
        @if($selected_suggestion)
            <div class="mb-4">
                <p class="text-sm font-semibold text-gray-500">{{ __('lang.question') ?? 'السؤال' }}:</p>
                <p class="text-base text-gray-800 bg-base-200 p-3 rounded-lg">{{ $selected_suggestion->content }}</p>
            </div>
            
            <x-textarea 
                label="{{ __('lang.teacher_comment') ?? 'تعليق المعلم' }}" 
                wire:model="teacher_comment" 
                placeholder="{{ __('lang.enter_comment_here') ?? 'اكتب تعليقك ليظهر لولي الأمر...' }}" 
                rows="4" 
            />
        @endif
        
        <x-slot:actions>
            <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.comment_modal = false" />
            <x-button label="{{ __('lang.save') ?? 'حفظ' }}" class="btn-primary" wire:click="saveComment" spinner="saveComment" />
        </x-slot:actions>
    </x-modal>
</div>
