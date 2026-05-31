<?php

namespace App\Http\Requests\Fontes_renda;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Status;
use TipoFonteRenda;

class UpdateFontesRenda extends FormRequest
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
            'nome'      => ['required', 'string', 'max:255'],
            'tipo'      => ['required', Rule::enum(TipoFonteRenda::class)],
            'descricao' => ['nullable', 'string'],
            'status'    => ['required', Rule::enum(Status::class)],
        ];
    }
}
