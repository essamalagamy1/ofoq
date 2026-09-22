<div>
    <x-card title="{{ __('lang.site_settings') }}" shadow class="mb-3">
        <x-form wire:submit="saveUpdate">
            {{-- Basic Info --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">{{ __('lang.basic_info') }}</h3>
                <div class="grid grid-cols-1 gap-3">
                    <x-input label="{{ __('lang.name') }}" wire:model="name_ar" />
                    {{-- <x-textarea label="{{ __('lang.description') }}" wire:model="description_ar" rows="3"/> --}}
                </div>
            </div>

            @can('edit_login_message')
                {{-- Login Message --}}
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">{{ __('lang.login_message') }}</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <x-textarea label="{{ __('lang.login_message') }}" wire:model="login_message_ar" rows="3" />
                    </div>
                </div>
            @endcan

            <div class="flex justify-end">
                @can('edit_site_setting')
                    <x-button label="{{ __('lang.update') }}" class="btn btn-primary" wire:loading.attr="disabled"
                        type="submit" spinner="saveUpdate" />
                @endcan
            </div>
        </x-form>
    </x-card>
</div>
