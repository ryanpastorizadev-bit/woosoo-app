<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class RefillOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:180'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:5'],
            'items.*.unit_price_cents' => ['nullable', 'integer', 'min:0'],
            'items.*.modifiers' => ['nullable', 'array'],
            'items.*.metadata' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
