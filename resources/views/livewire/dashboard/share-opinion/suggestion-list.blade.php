<div>
    <x-header title="{{ __('lang.my_suggestions') ?? 'مقترحاتي للأسئلة' }}" subtitle="{{ __('lang.my_suggestions_hint') ?? 'ساهم في بنك الأسئلة باقتراح أسئلة جديدة' }}" separator>
        <x-slot:actions>
            <x-button icon="o-plus" class="btn-primary" link="{{ route('parent.opinion.create') }}">
                {{ __('lang.suggest_new_question') ?? 'اقتراح سؤال جديد' }}
            </x-button>
        </x-slot:actions>
    </x-header>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">{{ __('lang.question') ?? 'السؤال' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.subject') ?? 'المادة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.grade') ?? 'الصف' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.status') ?? 'الحالة' }}</th>
                        <th class="py-3 px-4">{{ __('lang.teacher_comment') ?? 'تعليق المعلم' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $suggestion)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $suggestion->id }}</td>
                            <td class="py-3 px-4">
                                <div class="max-w-xs truncate" title="{{ $suggestion->content }}">
                                    {{ $suggestion->content }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="badge badge-primary text-white">
                                    {{ \App\Enums\SubjectEnum::coerce($suggestion->subject)?->title() ?? $suggestion->subject }}
                                </span>
                            </td>
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
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $suggestion->teacher_comment ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                {{ __('lang.no_suggestions_yet') ?? 'لم تقم بتقديم أي مقترحات بعد.' }}
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
</div>
