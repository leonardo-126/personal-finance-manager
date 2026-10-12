<?php

namespace App\Http\Requests\OpenFinance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportarTransacoesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'transacao_ids'   => ['required', 'array', 'min:1'],
            'transacao_ids.*' => ['integer'],
            // Opcional quando a conta já tem um caixa vinculado.
            'caixa_id'        => ['nullable', 'integer', Rule::exists('caixas_financeiras', 'id')->where('user_id', $userId)->whereNull('deleted_at')],
            'categoria_id'    => ['required', 'integer', Rule::exists('categorias_gastos', 'id')->where('user_id', $userId)->whereNull('deleted_at')],
            'descricao'       => ['nullable', 'string', 'max:255'],
            'data_gasto'      => ['nullable', 'date'],
        ];
    }
}
