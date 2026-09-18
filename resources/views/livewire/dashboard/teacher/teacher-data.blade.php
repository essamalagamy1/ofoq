<div>
    <x-header title="{{ __('lang.teachers') ?? 'المعلمين' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-4">
                <x-input wire:model.live.debounce.500ms="search_name" placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }}" icon="o-magnifying-glass" clearable class="w-48" />
                <x-input wire:model.live.debounce.500ms="search_mobile" placeholder="{{ __('lang.search_mobile') ?? 'بحث برقم الجوال' }}" icon="o-magnifying-glass" clearable class="w-48" />
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
                        <th class="py-3 px-4">{{ __('lang.mobile_number') ?? 'رقم الجوال' }}</th>
                        <th class="py-3 px-4">{{ __('lang.assigned_subject') ?? 'المادة الدراسية' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $teacher->id }}</td>
                            <td class="py-3 px-4 font-semibold">{{ $teacher->name }}</td>
                            <td class="py-3 px-4" dir="ltr">
                                {{ $teacher->phone_key }}{{ $teacher->mobile_number ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge badge-outline badge-primary">
                                    {{ \App\Enums\SubjectEnum::tryFrom($teacher->assigned_subject)?->title() ?? $teacher->assigned_subject }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info" wire:click="$dispatch('open-update-modal', { teacher: {{ $teacher->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="delete({{ $teacher->id }})" wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
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
        
        @if($teachers->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.teacher.create-teacher')
    @livewire('dashboard.teacher.update-teacher')
</div>
