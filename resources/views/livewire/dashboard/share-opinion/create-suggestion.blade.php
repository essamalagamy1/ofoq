<div class="max-w-4xl mx-auto">
    <x-header title="{{ __('lang.suggest_new_question') ?? 'اقتراح سؤال جديد' }}" subtitle="{{ __('lang.suggest_question_hint') ?? 'نقدر مساهمتك في إثراء بنك الأسئلة لأبنائنا' }}" separator>
        <x-slot:actions>
            <x-button icon="o-arrow-right" class="btn-ghost" link="{{ route('parent.opinion') }}">
                {{ __('lang.back') ?? 'رجوع' }}
            </x-button>
        </x-slot:actions>
    </x-header>

    <div class="bg-base-100 p-6 rounded-lg shadow-sm border border-base-200">
        <x-form wire:submit="submit">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-select label="{{ __('lang.subject') ?? 'المادة' }}" wire:model="subject" :options="collect(\App\Enums\SubjectEnum::getInstances())->map(fn($e) => ['value' => $e->value, 'title' => $e->title()])" option-value="value" option-label="title" placeholder="{{ __('lang.select_subject') ?? 'اختر المادة...' }}" required />
                
                <x-select label="{{ __('lang.grade') ?? 'الصف' }}" wire:model="grade" :options="$available_grades" option-value="id" option-label="name" placeholder="{{ __('lang.select_grade') ?? 'اختر الصف...' }}" required />
                
                <x-input type="number" label="{{ __('lang.week') ?? 'الأسبوع' }}" wire:model="week" readonly hint="يتم تحديد الأسبوع تلقائياً من قبل الإدارة" />
            </div>

            <x-textarea label="{{ __('lang.question_content') ?? 'نص السؤال' }}" wire:model="content" placeholder="{{ __('lang.question_content_hint') ?? 'اكتب نص السؤال هنا بدقة...' }}" rows="3" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="{{ __('lang.option_a') ?? 'الخيار الأول (A)' }}" wire:model="option_a" required />
                <x-input label="{{ __('lang.option_b') ?? 'الخيار الثاني (B)' }}" wire:model="option_b" required />
                <x-input label="{{ __('lang.option_c') ?? 'الخيار الثالث (C)' }}" wire:model="option_c" required />
                <x-input label="{{ __('lang.option_d') ?? 'الخيار الرابع (D)' }}" wire:model="option_d" required />
            </div>

            <x-select label="{{ __('lang.correct_option') ?? 'الخيار الصحيح' }}" wire:model="correct_option" :options="[
                ['id' => 'A', 'name' => 'A'],
                ['id' => 'B', 'name' => 'B'],
                ['id' => 'C', 'name' => 'C'],
                ['id' => 'D', 'name' => 'D'],
            ]" option-value="id" option-label="name" placeholder="{{ __('lang.select_correct_option') ?? 'اختر الإجابة الصحيحة...' }}" required />

            <x-slot:actions>
                <x-button label="{{ __('lang.submit_suggestion') ?? 'إرسال المقترح' }}" class="btn-primary" type="submit" spinner="submit" />
            </x-slot:actions>
        </x-form>
    </div>
</div>
