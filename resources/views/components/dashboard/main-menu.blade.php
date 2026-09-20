<x-menu activate-by-route active-bg-color="font-black text-primary bg-primary/10 dark:bg-primary/20"
    class="menu-vertical">

    @role('super_admin')
        <x-menu-item title="{{ __('lang.home') ?? 'الرئيسية' }}" icon-classes="text-primary" icon="o-home"
            link="{{ route('admin.dashboard') }}" />
        <x-menu-separator />

        <x-menu-title title="{{ __('lang.users_management') ?? 'إدارة النظام' }}" />
        <x-menu-item title="{{ __('lang.students') ?? 'الطلاب' }}" icon-classes="text-primary" icon="o-users"
            link="{{ route('admin.students') }}" />
        <x-menu-item title="{{ __('lang.teachers') ?? 'المعلمين' }}" icon-classes="text-primary" icon="o-academic-cap"
            link="{{ route('admin.teachers') }}" />

        <x-menu-separator />
        <x-menu-title title="{{ __('lang.academic_content') ?? 'المحتوى الأكاديمي' }}" />
        <x-menu-item title="{{ __('lang.questions') ?? 'بنك الأسئلة' }}" icon-classes="text-primary" icon="o-document-text"
            link="{{ route('admin.questions') }}" />

        <x-menu-separator />
        <x-menu-title title="{{ __('lang.system_settings') ?? 'الإعدادات' }}" />
        <x-menu-item title="{{ __('lang.badges') ?? 'الشارات' }}" icon-classes="text-primary" icon="o-star"
            link="{{ route('admin.badges') }}" />
        <x-menu-item title="{{ __('lang.cycles') ?? 'الدورات' }}" icon-classes="text-primary" icon="o-arrow-path"
            link="{{ route('admin.cycles') }}" />
    @endrole

    @role('teacher')
        <x-menu-item title="{{ __('lang.home') ?? 'الرئيسية' }}" icon-classes="text-primary" icon="o-home"
            link="{{ route('teacher.dashboard') }}" />
        <x-menu-separator />

        <x-menu-title title="{{ __('lang.academic_content') ?? 'المحتوى الأكاديمي' }}" />
        <x-menu-item title="{{ __('lang.questions') ?? 'بنك الأسئلة' }}" icon-classes="text-primary" icon="o-document-text"
            link="{{ route('teacher.questions') }}" />
        <x-menu-item title="{{ __('lang.suggestions') ?? 'مشاركات الآباء' }}" icon-classes="text-primary"
            icon="o-chat-bubble-left-ellipsis" link="{{ route('teacher.suggestions') }}" />
    @endrole

    @role('parent')
        <x-menu-item title="{{ __('lang.home') ?? 'الرئيسية' }}" icon-classes="text-primary" icon="o-home"
            link="{{ route('parent.dashboard') }}" />
        <x-menu-separator />

        <x-menu-item title="{{ __('lang.opinion') ?? 'شارك برأيك' }}" icon-classes="text-primary" icon="o-pencil-square"
            link="{{ route('parent.opinion') }}" />
    @endrole

</x-menu>
