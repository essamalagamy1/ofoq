<?php

namespace App\Livewire\Dashboard\SiteSetting;

use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Title('site_settings')]
class UpdateSiteSetting extends Component
{
    use Toast;

    public $setting;

    public $name_ar;

    public $name_en;

    public $description_ar;

    public $description_en;

    public $login_message_ar;

    public $login_message_en;

    public function mount(): void
    {
        $this->authorize('show_site_setting');
        $this->setting = SiteSetting::getSetting();

        $this->name_ar = $this->setting->getTranslation('name', 'ar');
        $this->name_en = $this->setting->getTranslation('name', 'en');
        $this->description_ar = $this->setting->getTranslation('description', 'ar');
        $this->description_en = $this->setting->getTranslation('description', 'en');
        $this->login_message_ar = $this->setting->getTranslation('login_message', 'ar');
        $this->login_message_en = $this->setting->getTranslation('login_message', 'en');

        view()->share('breadcrumbs', $this->breadcrumbs());
    }

    public function breadcrumbs(): array
    {
        return [
            [
                'label' => __('lang.site_settings'),
                'icon' => 'o-cog-6-tooth',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'login_message_ar' => 'nullable|string',
            'login_message_en' => 'nullable|string',
        ];
    }

    public function saveUpdate(): void
    {
        $this->authorize('edit_site_setting');
        $this->validate();

        $data = [
            'name' => [
                'ar' => $this->name_ar,
                'en' => $this->name_en,
            ],
            'description' => [
                'ar' => $this->description_ar,
                'en' => $this->description_en,
            ],
            'login_message' => [
                'ar' => $this->login_message_ar,
                'en' => $this->login_message_en,
            ],
        ];

        if ($this->setting->exists) {
            $this->setting->update($data);
        } else {
            SiteSetting::create($data);
        }

        $this->success(__('lang.updated_successfully', ['attribute' => __('lang.site_settings')]));
    }

    public function render(): View
    {
        return view('livewire.dashboard.site-setting.update-site-setting');
    }
}
