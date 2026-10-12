<?php

namespace App\Http\Requests\OpenFinance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtualizarContaRequest extends FormRequest
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
        return [
            'caixa_id' => [
                'present',
                'nullable',
                'integer',
                Rule::exists('caixas_financeiras', 'id')->where('user_id', $this->user()->id)->whereNull('deleted_at'),
            ],
        ];
    }
}
