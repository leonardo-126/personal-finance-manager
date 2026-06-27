<?php

namespace App\Http\Requests\Faturas;

use Illuminate\Foundation\Http\FormRequest;

class PreviewFaturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'arquivo' => ['required', 'file', 'max:5120', 'mimes:csv,txt,xls,xlsx'],
        ];
    }
}
