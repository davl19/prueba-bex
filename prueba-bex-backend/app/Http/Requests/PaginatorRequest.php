<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginatorRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer',
            'sort' => 'nullable|regex:/^[a-zA-Z_-]*$/',
            'page' => 'nullable',
            'q' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'order.in' => __('order.in'),
            'per_page.integer' => __('campo.integer'),
        ];
    }

    public function withValidator($validator)
    {
        $this->merge([
            'per_page' => is_null($this->per_page) ? 999999 : intval($this->per_page),
            'page' => is_null($this->page) ? 1 : intval($this->page),
            'order' => $this->order ?? 'asc',
            'q' => $this->q,
        ]);
    }
}
