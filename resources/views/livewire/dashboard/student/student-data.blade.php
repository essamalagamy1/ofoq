<div>
    <x-header title="{{ __('lang.students') ?? 'الطلاب' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-2 sm:gap-4">
                <x-button icon="o-arrow-up-tray" class="btn-success btn-sm sm:btn-md"
                    wire:click="$set('import_modal', true)">{{ __('lang.import') ?? 'استيراد' }}</x-button>
                <x-button icon="o-plus" class="btn-primary btn-sm sm:btn-md"
                    wire:click="$dispatch('open-create-modal')">{{ __('lang.add') ?? 'إضافة' }}</x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <div class="gap-4 mb-4">
        <x-ui.choices-advanced-search wire:model.live="search_student_id" :options="$all_students" option-label="name"
            option-sub-label="sub_label"
            placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }} / {{ __('lang.search_by_id') ?? 'بحث بالرقم' }}"
            icon="o-magnifying-glass" clearable single searchable />
    </div>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4 cursor-pointer hover:bg-base-200 transition-colors select-none"
                            wire:click="sortByColumn('id')">
                            <div class="flex items-center gap-1">
                                #
                                @if ($sortBy['column'] === 'id')
                                    <x-icon name="o-chevron-{{ $sortBy['direction'] === 'asc' ? 'up' : 'down' }}"
                                        class="w-4 h-4 text-primary" />
                                @else
                                    <x-icon name="o-chevron-up-down" class="w-4 h-4 text-gray-400" />
                                @endif
                            </div>
                        </th>
                        <th class="py-3 px-4">{{ __('lang.name') ?? 'الاسم' }}</th>
                        <th class="py-3 px-4">{{ __('lang.grade') ?? 'الصف' }}</th>
                        <th class="py-3 px-4 cursor-pointer hover:bg-base-200 transition-colors select-none"
                            wire:click="sortByColumn('overall_evaluation')">
                            <div class="flex items-center gap-1 whitespace-nowrap">
                                {{ __('lang.overall_cycle_evaluation') ?? 'التقييم الإجمالي' }}
                                @if ($sortBy['column'] === 'overall_evaluation')
                                    <x-icon name="o-chevron-{{ $sortBy['direction'] === 'asc' ? 'up' : 'down' }}"
                                        class="w-4 h-4 text-primary" />
                                @else
                                    <x-icon name="o-chevron-up-down" class="w-4 h-4 text-gray-400" />
                                @endif
                            </div>
                        </th>
                        <th class="py-3 px-4">{{ __('lang.semester') ?? 'الفصل الدراسي' }}</th>
                        <th class="py-3 px-4">{{ __('lang.parent_mobile') ?? 'هاتف ولي الأمر' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $student->id }}</td>
                            <td class="py-3 px-4 font-semibold">{{ $student->name }}</td>
                            <td class="py-3 px-4">
                                <span class="badge badge-primary text-white">{{ $student->grade }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-center">
                                @if (isset($student->answers_avg_is_correct))
                                    @php
                                        $percentage = round($student->answers_avg_is_correct * 100);
                                        $earnedBadge = null;
                                        foreach ($allBadges as $badge) {
                                            if (
                                                $percentage >= $badge->min_percentage &&
                                                $percentage <= $badge->max_percentage
                                            ) {
                                                $earnedBadge = $badge;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($earnedBadge)
                                            @if ($earnedBadge->getFirstMediaUrl('image'))
                                                <img src="{{ $earnedBadge->getFirstMediaUrl('image') }}"
                                                    class="w-8 h-8 object-cover rounded-full shadow-sm"
                                                    alt="{{ $earnedBadge->name }}" title="{{ $earnedBadge->name }}" />
                                            @else
                                                <x-icon name="s-star" class="w-8 h-8"
                                                    style="color: {{ $earnedBadge->color_hex }}"
                                                    title="{{ $earnedBadge->name }}" />
                                            @endif
                                        @endif
                                        <span class="{{ $percentage >= 50 ? 'text-success' : 'text-error' }}">
                                            {{ $percentage }}%
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ $student->semester }}</td>
                            <td class="py-3 px-4" dir="ltr">
                                {{ $student->parent_mobile_1_key }}{{ $student->parent_mobile_1 ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-star" class="btn-sm btn-ghost text-warning"
                                        wire:click="$dispatch('open-student-progress-modal', { student: {{ $student->id }} })"
                                        tooltip="{{ __('lang.track_badges') ?? 'تتبع الشارات' }}" />
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info"
                                        wire:click="$dispatch('open-update-modal', { student: {{ $student->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error"
                                        wire:click="delete({{ $student->id }})"
                                        wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
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

        @if ($students->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.student.create-student')
    @livewire('dashboard.student.update-student')
    @livewire('dashboard.student.student-progress')

    <!-- Import Modal -->
    <x-modal wire:model="import_modal" title="{{ __('lang.import_students') ?? 'استيراد الطلاب' }}" separator>
        <x-form wire:submit="importData">

            <x-input type="number" label="{{ __('lang.grade') ?? 'الصف (3-6)' }}" wire:model="import_grade"
                min="3" max="6" />
            <x-file wire:model="import_file" label="{{ __('lang.select_excel_file') ?? 'اختر ملف Excel' }}"
                accept=".xlsx,.xls,.csv" required />

            <div class="text-sm text-gray-500 mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="font-bold mb-2">الأعمدة المطلوبة في الملف:</div>
                <div class="mb-4" dir="ltr">
                    name, nationality, semester, phone_1, phone_2, phone_3
                </div>
                <a href="{{ asset('temp.xlsx') }}" download class="btn btn-sm btn-outline btn-info">
                    <x-icon name="o-arrow-down-tray" class="w-4 h-4" />
                    {{ __('lang.download_template') ?? 'تحميل قالب الاستيراد (temp.xlsx)' }}
                </a>
            </div>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.import_modal = false" />
                <x-button label="{{ __('lang.import') ?? 'استيراد' }}" class="btn-primary" type="submit"
                    spinner="importData" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
