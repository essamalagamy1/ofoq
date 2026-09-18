@php use App\Services\FileService; @endphp
<div>
	<x-card title="{{ __('lang.personal_info') }}" shadow separator class="mb-3">

		<form wire:submit.prevent="updateProfile" class="flex flex-col gap-4">
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
				<x-input label="{{__('lang.name')}}" wire:model="name"/>
				
                <x-phone-input 
                    phoneProperty="phone" 
                    keyProperty="phone_key" 
                    label="{{ __('lang.mobile_number') ?? 'رقم الجوال' }}" 
                />



			</div>
			<div class="text-center">
				<x-button class="btn btn-primary" variant="primary" type="submit" spinner="updateProfile" wire:loading.attr="disabled">{{ __('lang.save') }}</x-button>
			</div>
		</form>
	</x-card>

	@if(auth()->user()->requires_password)
		<x-card title="{{ __('lang.password') }}" shadow separator class="mb-3">
			<form wire:submit.prevent="updatePassword" class="flex flex-col gap-4">
				<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
					<x-input type="password" label="{{__('lang.old_password')}}" wire:model="old_password"/>
					<x-input type="password" label="{{__('lang.new_password')}}" wire:model="password"/>
					<x-input type="password" label="{{__('lang.confirm_password')}}" wire:model="password_confirmation"/>
				</div>
				<div class="text-center">
					<x-button class="btn btn-primary" variant="primary" type="submit" spinner="updatePassword" wire:loading.attr="disabled">{{ __('lang.save') }}</x-button>
				</div>
			</form>
		</x-card>
	@endif
</div>