<?php

namespace App\Http\Requests\GastosItens;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AtribuirPessoaItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // pessoa_id nulo = desatribuir. A posse da pessoa é conferida na Action.
        return [
            'pessoa_id' => ['nullable', 'integer', 'exists:pessoas,id'],
        ];
    }
}
