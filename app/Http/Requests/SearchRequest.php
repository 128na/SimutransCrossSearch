<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'word' => 'nullable|string|max:100',
            'type' => 'nullable|in:and,or',
            'paks' => 'nullable|array',
            'paks.*' => 'required|in:64,128,128-japan',
        ];
    }
}
