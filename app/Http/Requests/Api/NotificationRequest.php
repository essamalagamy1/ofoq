<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

class NotificationRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'notification_id' => 'required|exists:notifications,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
