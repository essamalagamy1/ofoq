<div>
    <x-modal wire:model="create_modal" title="{{ __('lang.add_cycle') ?? 'إضافة دورة' }}" separator>
        <x-form wire:submit="create">
            <x-input label="{{ __('lang.name') ?? 'الاسم' }}" wire:model="name" placeholder="{{ __('lang.cycle_name_example') ?? 'مثال: الفصل الدراسي الأول 2026' }}" required />
            
            <x-toggle label="{{ __('lang.is_active') ?? 'تفعيل الدورة؟' }}" wire:model="is_active" hint="{{ __('lang.is_active_hint') ?? 'عند تفعيل هذه الدورة، سيتم تعطيل باقي الدورات تلقائياً.' }}" />

            <x-alert icon="o-exclamation-triangle" class="alert-info text-sm">
                ملاحظة: عند إنشاء دورة جديدة، يكون الأسبوع النشط الافتراضي هو الأسبوع 1. يجب على الإدارة تغيير الأسبوع النشط مع بداية كل أسبوع جديد من خلال تعديل الدورة، حتى تُسجل أسئلة ومقترحات هذا الأسبوع بالشكل الصحيح.
            </x-alert>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.create_modal = false" />
                <x-button label="{{ __('lang.save') ?? 'حفظ' }}" class="btn-primary" type="submit" spinner="create" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
