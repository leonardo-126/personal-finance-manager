<?php

namespace App\Http\Requests\Rendas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRenda extends FormRequest
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
            'fonte_renda_id'   => ['required', 'integer', 'exists:fontes_renda,id'],
            'valor'            => ['required', 'numeric'],
            'data_recebimento' => ['required', 'date'],
            'descricao'        => ['nullable', 'string'],
        ];
    }
}
