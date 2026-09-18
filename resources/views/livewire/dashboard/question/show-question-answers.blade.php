<div>
    <x-modal wire:model="show_modal" title="{{ __('lang.view_answers') ?? 'عرض الإجابات' }}" separator box-class="max-w-4xl">
        
        @if($question)
            <div class="mb-4 p-4 bg-base-200 rounded-lg">
                <h3 class="font-bold text-lg mb-2">{{ __('lang.question_content') ?? 'نص السؤال' }}:</h3>
                <p>{{ $question->content }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200/50">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">{{ __('lang.student_name') ?? 'اسم الطالب' }}</th>
                            <th class="py-3 px-4 text-center">{{ __('lang.status') ?? 'الحالة' }}</th>
                            <th class="py-3 px-4 text-center">{{ __('lang.date') ?? 'التاريخ' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($answers as $answer)
                            <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                                <td class="py-3 px-4">{{ $answer->id }}</td>
                                <td class="py-3 px-4 font-semibold">{{ $answer->student->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($answer->is_correct)
                                        <span class="badge badge-success text-white">{{ __('lang.correct_answer') ?? 'إجابة صحيحة' }}</span>
                                    @else
                                        <span class="badge badge-error text-white">{{ __('lang.wrong_answer') ?? 'إجابة خاطئة' }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center text-sm text-gray-500">
                                    {{ $answer->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">
                                    {{ __('lang.no_answers_yet') ?? 'لا توجد إجابات على هذا السؤال حتى الآن' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($answers instanceof \Illuminate\Pagination\LengthAwarePaginator && $answers->hasPages())
                <div class="mt-4">
                    {{ $answers->links() }}
                </div>
            @endif
        @endif

        <x-slot:actions>
            <x-button label="{{ __('lang.close') ?? 'إغلاق' }}" @click="$wire.show_modal = false" />
        </x-slot:actions>
    </x-modal>
</div>
