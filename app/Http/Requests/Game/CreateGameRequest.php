<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class CreateGameRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255']
        ];
    }
}
