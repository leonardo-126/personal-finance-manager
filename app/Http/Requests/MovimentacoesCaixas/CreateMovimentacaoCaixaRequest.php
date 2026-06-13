<?php

namespace App\Http\Requests\MovimentacoesCaixas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateMovimentacaoCaixaRequest extends FormRequest
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
        return [
            'caixa_id' => ['required', 'integer', 'exists:caixas_financeiras,id'],
            'renda_id' => ['nullable', 'integer', 'exists:rendas,id'],
            'valor'    => ['required', 'numeric'],
            'tipo'     => ['required', 'in:entrada,saida,transferencia'],
        ];
    }
}
