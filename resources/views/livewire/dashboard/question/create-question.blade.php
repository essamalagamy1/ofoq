<div>
    <x-modal wire:model="create_modal" title="{{ __('lang.add_question') ?? 'إضافة سؤال' }}" separator box-class="max-w-4xl">
        <x-form wire:submit="create">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-select 
                    label="{{ __('lang.subject') ?? 'المادة' }}" 
                    wire:model="subject" 
                    :options="collect(\App\Enums\SubjectEnum::getInstances())->map(fn($e) => ['value' => $e->value, 'title' => $e->title()])" 
                    option-value="value" 
                    option-label="title" 
                    placeholder="{{ __('lang.select') ?? 'اختر...' }}" 
                    required 
                    :disabled="auth()->user()->hasRole('teacher')"
                />

                <x-input type="number" label="{{ __('lang.grade') ?? 'الصف (3-6)' }}" wire:model="grade" min="3" max="6" required :disabled="auth()->user()->hasRole('teacher')" />
                <x-input type="number" label="{{ __('lang.week') ?? 'الأسبوع' }}" wire:model="week" readonly hint="يتم تحديد الأسبوع تلقائياً من قبل الإدارة" />
            </div>

            <div class="mt-4">
                <x-textarea label="{{ __('lang.question_content') ?? 'نص السؤال' }}" wire:model="content" rows="3" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <x-input label="{{ __('lang.option_a') ?? 'الخيار A' }}" wire:model="option_a" required />
                <x-input label="{{ __('lang.option_b') ?? 'الخيار B' }}" wire:model="option_b" required />
                <x-input label="{{ __('lang.option_c') ?? 'الخيار C' }}" wire:model="option_c" required />
                <x-input label="{{ __('lang.option_d') ?? 'الخيار D' }}" wire:model="option_d" required />
            </div>

            <div class="mt-4 w-1/2">
                <x-select 
                    label="{{ __('lang.correct_option') ?? 'الخيار الصحيح' }}" 
                    wire:model="correct_option" 
                    :options="collect(\App\Enums\OptionEnum::getInstances())->map(fn($e) => ['value' => $e->value, 'title' => $e->value . ' - ' . $e->title()])" 
                    option-value="value" 
                    option-label="title" 
                    placeholder="{{ __('lang.select') ?? 'اختر...' }}" 
                    required 
                />
            </div>

            <x-slot:actions>
                <x-button label="{{ __('lang.cancel') ?? 'إلغاء' }}" @click="$wire.create_modal = false" />
                <x-button label="{{ __('lang.save') ?? 'حفظ' }}" class="btn-primary" type="submit" spinner="create" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
