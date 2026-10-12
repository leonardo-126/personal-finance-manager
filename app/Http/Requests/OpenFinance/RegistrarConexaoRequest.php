<?php

namespace App\Http\Requests\OpenFinance;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarConexaoRequest extends FormRequest
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
            // Id da conexão devolvido pelo widget do agregador (itemId na Pluggy).
            'external_id' => ['required', 'string', 'max:255'],
        ];
    }
}
