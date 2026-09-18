<div>
    <x-modal wire:model="create_modal" title="{{ __('lang.add_student') ?? 'إضافة طالب' }}" separator>
        <x-form wire:submit="create">
            <x-input label="{{ __('lang.name') ?? 'الاسم' }}" wire:model="name" />
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <x-input label="{{ __('lang.nationality') ?? 'الجنسية' }}" wire:model="nationality" />
                <x-input type="number" label="{{ __('lang.grade') ?? 'الصف (3-6)' }}" wire:model="grade" min="3" max="6" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <x-input type="number" label="{{ __('lang.semester') ?? 'الفصل الدراسي (1-3)' }}" wire:model="semester" min="1" max="3" />
                <x-checkbox label="{{ __('lang.can_share_opinion') ?? 'السماح بالمشاركة بالرأي' }}" wire:model="can_share_opinion" />
            </div>

                <x-phone-input 
                    phoneProperty="parent_mobile_1" 
                    keyProperty="parent_mobile_1_key" 
                    label="{{ __('lang.parent_mobile_1') ?? 'هاتف ولي الأمر 1' }}" 
                />
                <x-phone-input 
                    phoneProperty="parent_mobile_2" 
                    keyProperty="parent_mobile_2_key" 
                    label="{{ __('lang.parent_mobile_2') ?? 'هاتف ولي الأمر 2' }}" 
                />
                <x-phone-input 
                    phoneProperty="parent_mobile_3" 
                    keyProperty="parent_mobile_3_key" 
                    label="{{ __('lang.parent_mobile_3') ?? 'هاتف ولي الأمر 3' }}" 
                />

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.create_modal = false" />
                <x-button label="{{ __('lang.save') ?? 'حفظ' }}" class="btn-primary" type="submit" spinner="create" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
