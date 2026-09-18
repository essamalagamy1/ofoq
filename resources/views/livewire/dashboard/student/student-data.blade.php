<div>
    <x-header title="{{ __('lang.students') ?? 'الطلاب' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-4">
                <x-input wire:model.live.debounce.500ms="search_student_id" placeholder="{{ __('lang.search_by_id') ?? 'بحث بالرقم' }}" icon="o-magnifying-glass" clearable class="w-32" />
                <x-input wire:model.live.debounce.500ms="search_name" placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }}" icon="o-magnifying-glass" clearable class="w-48" />
                <x-button icon="o-arrow-up-tray" class="btn-success" wire:click="$set('import_modal', true)">{{ __('lang.import') ?? 'استيراد' }}</x-button>
                <x-button icon="o-plus" class="btn-primary" wire:click="$dispatch('open-create-modal')">{{ __('lang.add') ?? 'إضافة' }}</x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">{{ __('lang.name') ?? 'الاسم' }}</th>
                        <th class="py-3 px-4">{{ __('lang.grade') ?? 'الصف' }}</th>
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
                                <span class="badge badge-outline badge-primary">{{ $student->grade }}</span>
                            </td>
                            <td class="py-3 px-4">{{ $student->semester }}</td>
                            <td class="py-3 px-4" dir="ltr">
                                {{ $student->parent_mobile_1_key }}{{ $student->parent_mobile_1 ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info" wire:click="$dispatch('open-update-modal', { student: {{ $student->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="delete({{ $student->id }})" wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
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
        
        @if($students->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.student.create-student')
    @livewire('dashboard.student.update-student')

    <!-- Import Modal -->
    <x-modal wire:model="import_modal" title="{{ __('lang.import_students') ?? 'استيراد الطلاب' }}" separator>
        <x-form wire:submit="importData">
            
            <x-input type="number" label="{{ __('lang.grade') ?? 'الصف (3-6)' }}" wire:model="import_grade" min="3" max="6" required />
            <x-file wire:model="import_file" label="{{ __('lang.select_excel_file') ?? 'اختر ملف Excel' }}" accept=".xlsx,.xls,.csv" required />
            
            <div class="text-sm text-gray-500 mt-2">
                الأعمدة المطلوبة في الملف: name, nationality, semester, parent_mobile_1_key, parent_mobile_1, parent_mobile_2_key, parent_mobile_2, parent_mobile_3_key, parent_mobile_3, can_share_opinion
            </div>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.import_modal = false" />
                <x-button label="{{ __('lang.import') ?? 'استيراد' }}" class="btn-primary" type="submit" spinner="importData" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
