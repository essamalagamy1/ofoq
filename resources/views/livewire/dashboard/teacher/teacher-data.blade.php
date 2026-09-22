<div>
    <x-header title="{{ __('lang.teachers') ?? 'المعلمين' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-2 sm:gap-4">
                <x-button icon="o-arrow-up-tray" class="btn-success btn-sm sm:btn-md"
                    wire:click="$set('import_modal', true)">{{ __('lang.import') ?? 'استيراد' }}</x-button>
                <x-button icon="o-plus" class="btn-primary btn-sm sm:btn-md"
                    wire:click="$dispatch('open-create-modal')">{{ __('lang.add') ?? 'إضافة' }}</x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <div class="grid auto-rows-min gap-4 grid-cols-1 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 mb-6">
        <div
            class="relative overflow-hidden rounded-xl bg-base-100 shadow-sm border border-base-200 p-4 transition-all hover:shadow-md">
            <x-stat title="{{ __('lang.total_teachers') ?? 'إجمالي المعلمات' }}" value="{{ $this->totalTeachers }}"
                icon="o-users" color="text-info" />
        </div>
        <div
            class="relative overflow-hidden rounded-xl bg-base-100 shadow-sm border border-base-200 p-4 transition-all hover:shadow-md">
            <x-stat title="{{ __('lang.regular_teachers_count') ?? 'عدد المعلمات الأساسيات' }}"
                value="{{ $this->totalRegularTeachers }}" icon="o-check-badge" color="text-success" />
        </div>
        <div
            class="relative overflow-hidden rounded-xl bg-base-100 shadow-sm border border-base-200 p-4 transition-all hover:shadow-md">
            <x-stat title="{{ __('lang.substitute_teachers_count') ?? 'عدد المعلمات البديلات' }}"
                value="{{ $this->totalSubstituteTeachers }}" icon="o-user-group" color="text-warning" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        <div class="col-span-1 md:col-span-2">
            <x-ui.choices-advanced-search wire:model.live="search_teacher_id" :options="$all_teachers" option-label="name"
                option-sub-label="sub_label"
                placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }} / {{ __('lang.search_by_id') ?? 'بحث بالرقم' }} / {{ __('lang.search_mobile') ?? 'بحث برقم الجوال' }}"
                icon="o-magnifying-glass" clearable single searchable class="w-full bg-base-100" />
        </div>
        <div class="col-span-1">
            <x-select wire:model.live="search_subject" :options="collect(\App\Enums\SubjectEnum::getInstances())->map(
                fn($e) => ['value' => $e->value, 'title' => $e->title()],
            )" option-value="value" option-label="title"
                placeholder="{{ __('lang.subject') ?? 'المادة الدراسية' }}" icon="o-book-open" clearable
                class="w-full bg-base-100" />
        </div>
        <div class="col-span-1">
            <x-select wire:model.live="search_is_substitute" :options="[
                ['value' => 1, 'title' => __('lang.substitute_teacher') ?? 'معلمة بديلة'],
                ['value' => 0, 'title' => __('lang.regular_teacher') ?? 'معلمة عادية'],
            ]" option-value="value" option-label="title"
                placeholder="{{ __('lang.teacher_type') ?? 'نوع المعلمة' }}" icon="o-user" clearable
                class="w-full bg-base-100" />
        </div>
    </div>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">{{ __('lang.name') ?? 'الاسم' }}</th>
                        <th class="py-3 px-4">{{ __('lang.phone') ?? 'رقم الجوال' }}</th>
                        <th class="py-3 px-4">{{ __('lang.assigned_subject') ?? 'المادة الدراسية' }}</th>
                        <th class="py-3 px-4">{{ __('lang.assigned_grade') ?? 'الصف' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.substitute_teacher') ?? 'معلمة بديلة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $teacher->id }}</td>
                            <td class="py-3 px-4 font-semibold">{{ $teacher->name }}</td>
                            <td class="py-3 px-4" dir="ltr">
                                {{ $teacher->phone_key }}{{ $teacher->phone ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge badge-primary text-white">
                                    {{ $teacher->assigned_subject ? \App\Enums\SubjectEnum::coerce($teacher->assigned_subject)?->title() ?? $teacher->assigned_subject : '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge badge-info text-white">
                                    {{ $teacher->assigned_grade ? \App\Enums\GradeEnum::coerce($teacher->assigned_grade)?->title() ?? $teacher->assigned_grade : '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if ($teacher->is_substitute)
                                    <x-icon name="o-check-circle" class="w-6 h-6 text-success mx-auto" />
                                @else
                                    <x-icon name="o-x-circle" class="w-6 h-6 text-base-300 mx-auto" />
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info"
                                        wire:click="$dispatch('open-update-modal', { teacher: {{ $teacher->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error"
                                        wire:click="delete({{ $teacher->id }})"
                                        wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                {{ __('lang.no_data') ?? 'لا توجد بيانات متاحة' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($teachers->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.teacher.create-teacher')
    @livewire('dashboard.teacher.update-teacher')

    <!-- Import Modal -->
    <x-modal wire:model="import_modal" title="{{ __('lang.import_teachers') ?? 'استيراد المعلمات' }}" separator>
        <x-form wire:submit="importData">
            <x-file wire:model="import_file" label="{{ __('lang.select_excel_file') ?? 'اختر ملف Excel' }}"
                accept=".xlsx,.xls,.csv" required />

            <div class="text-sm text-gray-500 mt-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="font-bold mb-2">الأعمدة المطلوبة في الملف:</div>
                <div class="mb-4" dir="ltr">
                    <code class="bg-gray-200 px-2 py-1 rounded">name</code>,
                    <code class="bg-gray-200 px-2 py-1 rounded">phone</code>,
                    <code class="bg-gray-200 px-2 py-1 rounded">subject</code>,
                    <code class="bg-gray-200 px-2 py-1 rounded">grade</code>,
                    <code class="bg-gray-200 px-2 py-1 rounded">is_substitute</code>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    <li>عمود <span class="font-bold text-error">name</span> مطلوب.</li>
                    <li>قيم <span class="font-bold">subject</span>: science, math, arabic</li>
                    <li>قيم <span class="font-bold">grade</span>: 3, 4, 5, 6</li>
                    <li>قيم <span class="font-bold">is_substitute</span>: 1 للبديلة، 0 للعادية أو يمكن تركه فارغاً</li>
                </ul>
            </div>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.import_modal = false" />
                <x-button label="{{ __('lang.import') ?? 'استيراد' }}" class="btn-primary" type="submit"
                    spinner="importData" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
