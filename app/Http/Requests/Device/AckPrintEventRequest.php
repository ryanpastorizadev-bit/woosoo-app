<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class AckPrintEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
