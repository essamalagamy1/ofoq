<div>
    <x-header title="{{ __('lang.cycles') ?? 'الدورات الأكاديمية' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-2 sm:gap-4">
                <x-button icon="o-plus" class="btn-primary btn-sm sm:btn-md" wire:click="$dispatch('open-create-modal')">{{ __('lang.add') ?? 'إضافة' }}</x-button>
            </div>
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        <x-input wire:model.live.debounce.500ms="search_name" placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }}" icon="o-magnifying-glass" clearable class="w-full" />
    </div>

    <div class="bg-base-100 rounded-lg shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/50">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">{{ __('lang.name') ?? 'الاسم' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.active_week') ?? 'الأسبوع النشط' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.status') ?? 'الحالة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cycles as $cycle)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $cycle->id }}</td>
                            <td class="py-3 px-4 font-semibold">{{ $cycle->name }}</td>
                            <td class="py-3 px-4 text-center font-bold text-primary">{{ $cycle->active_week ?? 1 }}</td>
                            <td class="py-3 px-4 text-center">
                                <x-toggle wire:model.live="cycles.{{ $loop->index }}.is_active" wire:click="toggleActive({{ $cycle->id }})" />
                                @if($cycle->is_active)
                                    <span class="text-success text-xs block mt-1">{{ __('lang.active') ?? 'نشط' }}</span>
                                @else
                                    <span class="text-gray-400 text-xs block mt-1">{{ __('lang.inactive') ?? 'غير نشط' }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info" wire:click="$dispatch('open-update-modal', { cycle: {{ $cycle->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="delete({{ $cycle->id }})" wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
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
        
        @if($cycles->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $cycles->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.system-cycles.create-cycle')
    @livewire('dashboard.system-cycles.update-cycle')
</div>
