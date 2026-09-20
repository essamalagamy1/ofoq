<div>
    <x-modal wire:model="create_modal" title="{{ __('lang.add_badge') ?? 'إضافة شارة' }}" separator>
        <x-form wire:submit="create">
            <x-input label="{{ __('lang.name') ?? 'الاسم' }}" wire:model="name" required />
            
            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-input type="number" label="{{ __('lang.min_percentage') ?? 'أقل نسبة' }}" wire:model="min_percentage" min="0" max="100" required />
                <x-input type="number" label="{{ __('lang.max_percentage') ?? 'أعلى نسبة' }}" wire:model="max_percentage" min="0" max="100" required />
            </div>

            <div class="mt-4">
                <x-input type="color" label="{{ __('lang.color') ?? 'اللون' }}" wire:model="color_hex" class="h-14" required />
            </div>

            <div class="mt-4">
                <x-file label="{{ __('lang.image') ?? 'الصورة' }}" wire:model="image" accept="image/*" required />
            </div>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.create_modal = false" />
                <x-button label="{{ __('lang.save') ?? 'حفظ' }}" class="btn-primary" type="submit" spinner="create" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
