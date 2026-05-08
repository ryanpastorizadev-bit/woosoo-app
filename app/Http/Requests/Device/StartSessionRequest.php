<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class StartSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_name' => ['nullable', 'string', 'max:120'],
            'table_id' => ['required', 'integer', 'min:1'],
            'table_name' => ['required', 'string', 'max:120'],
        ];
    }
}
