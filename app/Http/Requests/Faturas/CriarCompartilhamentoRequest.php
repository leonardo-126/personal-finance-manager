<?php

namespace App\Http\Requests\Faturas;

use Illuminate\Foundation\Http\FormRequest;

class CriarCompartilhamentoRequest extends FormRequest
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
            'pessoa_id' => ['required', 'integer'],
        ];
    }
}
