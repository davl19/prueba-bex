<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:50',
            'email'     => 'nullable|string|email|max:50|unique:visits,email',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => __('required_field'),
            'name.max'            => __('visits.name.max_name_size'),
            'email.max'           => __('visits.email.max_name_size'),
            'latitude.required'   => __('required_field'),
            'latitude.numeric'    => __('visits.latitude.numeric'),
            'longitude.required'  => __('required_field'),
            'longitude.numeric'   => __('visits.longitude.numeric'),
        ];
    }

}
