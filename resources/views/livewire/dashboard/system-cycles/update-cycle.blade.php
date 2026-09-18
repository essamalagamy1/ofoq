<div>
    <x-modal wire:model="update_modal" title="{{ __('lang.update_cycle') ?? 'تعديل دورة' }}" separator>
        <x-form wire:submit="update">
            <x-input label="{{ __('lang.name') ?? 'الاسم' }}" wire:model="name" required />
            
            <x-toggle label="{{ __('lang.is_active') ?? 'تفعيل الدورة؟' }}" wire:model="is_active" hint="{{ __('lang.is_active_hint') ?? 'عند تفعيل هذه الدورة، سيتم تعطيل باقي الدورات تلقائياً.' }}" />

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.update_modal = false" />
                <x-button label="{{ __('lang.save') ?? 'تحديث' }}" class="btn-primary" type="submit" spinner="update" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
