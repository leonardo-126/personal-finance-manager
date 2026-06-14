<?php

namespace App\Http\Requests\Gastos;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGastoRequest extends FormRequest
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
            'caixa_id'     => ['required', 'integer', 'exists:caixas_financeiras,id'],
            'categoria_id' => ['required', 'integer', 'exists:categorias_gastos,id'],
            'valor_total'  => ['required', 'numeric'],
            'descricao'    => ['nullable', 'string'],
            'data_gasto'   => ['required', 'date'],
        ];
    }
}
