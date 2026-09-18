<div>
    <x-header title="{{ __('lang.badges') ?? 'الشارات' }}" separator>
        <x-slot:actions>
            <div class="flex items-center gap-4">
                <x-input wire:model.live.debounce.500ms="search_name" placeholder="{{ __('lang.search_by_name') ?? 'بحث بالاسم' }}" icon="o-magnifying-glass" clearable class="w-48" />
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
                        <th class="py-3 px-4 text-center">{{ __('lang.min_percentage') ?? 'أقل نسبة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.max_percentage') ?? 'أعلى نسبة' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.color') ?? 'اللون' }}</th>
                        <th class="py-3 px-4 text-center">{{ __('lang.action') ?? 'الإجراءات' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($badges as $badge)
                        <tr class="hover:bg-base-100/80 transition-colors border-b border-base-100 last:border-0">
                            <td class="py-3 px-4">{{ $badge->id }}</td>
                            <td class="py-3 px-4 font-semibold">{{ $badge->name }}</td>
                            <td class="py-3 px-4 text-center">{{ $badge->min_percentage }}%</td>
                            <td class="py-3 px-4 text-center">{{ $badge->max_percentage }}%</td>
                            <td class="py-3 px-4 text-center">
                                <div class="w-8 h-8 rounded-full border border-base-300 mx-auto" style="background-color: {{ $badge->color_hex }};" title="{{ $badge->color_hex }}"></div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-button icon="o-pencil" class="btn-sm btn-ghost text-info" wire:click="$dispatch('open-update-modal', { badge: {{ $badge->id }} })" />
                                    <x-button icon="o-trash" class="btn-sm btn-ghost text-error" wire:click="delete({{ $badge->id }})" wire:confirm="{{ __('lang.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}" />
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
        
        @if($badges->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $badges->links() }}
            </div>
        @endif
    </div>

    @livewire('dashboard.badge.create-badge')
    @livewire('dashboard.badge.update-badge')
</div>
