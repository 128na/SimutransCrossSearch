<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PakSlug;
use App\Enums\SiteName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageSearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => 'present|max:20',
            'paks' => 'present|array|min:0',
            'paks.*' => ['required', Rule::enum(PakSlug::class)],
            'sites' => 'present|array|min:0',
            'sites.*' => ['required', Rule::enum(SiteName::class)],
        ];
    }
}
