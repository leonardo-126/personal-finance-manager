<?php

namespace App\Http\Requests\Faturas;

use Illuminate\Foundation\Http\FormRequest;

class ImportarFaturaRequest extends FormRequest
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
            'arquivo'      => ['required', 'file', 'max:5120', 'mimes:csv,txt,xls,xlsx'],
            'caixa_id'     => ['required', 'integer', 'exists:caixas_financeiras,id'],
            'categoria_id' => ['required', 'integer', 'exists:categorias_gastos,id'],
            'descricao'    => ['nullable', 'string', 'max:255'],
            'data_gasto'   => ['nullable', 'date'],
        ];
    }
}
