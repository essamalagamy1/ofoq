<div>
    <x-modal wire:model="update_modal" title="{{ __('lang.update_teacher') ?? 'تعديل معلم' }}" separator>
        <x-form wire:submit="update">
            <x-input label="{{ __('lang.name') ?? 'الاسم' }}" wire:model="name" required />
            <div class="mt-4">
                <x-checkbox label="{{ __('lang.requires_password') ?? 'تفعيل الدخول بكلمة مرور' }}"
                    wire:model.live="requires_password" />
            </div>

            @if ($requires_password)
                <x-password label="{{ __('lang.password') ?? 'كلمة المرور' }}" wire:model="password"
                    hint="{{ __('lang.optional') ?? 'اختياري: اتركه فارغاً إذا لم ترغب بتغيير كلمة المرور' }}" />
            @endif

            <div class="mt-4">
                <x-phone-input phoneProperty="phone" keyProperty="phone_key"
                    label="{{ __('lang.phone') ?? 'رقم الجوال' }}" />
            </div>

            <div class="mt-4">
                <x-checkbox label="{{ __('lang.substitute_teacher') ?? 'معلمة بديلة' }}"
                    wire:model.live="is_substitute" />
            </div>

            <div class="mt-4">
                <x-select label="{{ __('lang.assigned_subject') ?? 'المادة الدراسية' }}" wire:model="assigned_subject"
                    :options="collect(\App\Enums\SubjectEnum::getInstances())->map(
                        fn($e) => ['value' => $e->value, 'title' => $e->title()],
                    )" option-value="value" option-label="title"
                    placeholder="{{ __('lang.select') ?? 'اختر...' }}" />
            </div>

            <div class="mt-4">
                <x-select label="{{ __('lang.assigned_grade') ?? 'الصف' }}" wire:model="assigned_grade"
                    :options="collect(\App\Enums\GradeEnum::getInstances())->map(
                        fn($e) => ['value' => $e->value, 'title' => $e->title()],
                    )" option-value="value" option-label="title"
                    placeholder="{{ __('lang.select') ?? 'اختر...' }}" />
            </div>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.update_modal = false" />
                <x-button label="{{ __('lang.save') ?? 'تحديث' }}" class="btn-primary" type="submit"
                    spinner="update" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
