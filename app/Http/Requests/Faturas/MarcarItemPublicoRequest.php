<?php

namespace App\Http\Requests\Faturas;

use Illuminate\Foundation\Http\FormRequest;

class MarcarItemPublicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'meu' => ['required', 'boolean'],
        ];
    }
}
