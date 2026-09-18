<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

class SendCodeRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'email:dns', 'exists:users,email'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
