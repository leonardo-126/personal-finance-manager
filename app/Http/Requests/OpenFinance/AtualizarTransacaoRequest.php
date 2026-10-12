<?php

namespace App\Http\Requests\OpenFinance;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarTransacaoRequest extends FormRequest
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
            // "importada" só é alcançada pelo fluxo de importação.
            'status' => ['required', 'in:pendente,ignorada'],
        ];
    }
}
