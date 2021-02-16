<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'files' => 'required|max:20480|mimes:jpg,jpeg,png',
        ];
    }
    public function messages()
    {
        return [
            'files.required' => 'File is required.',
            'files.max'  => 'File size exceeded.',
            'files.mimes'  => 'File type not supported.',
        ];
    }
}
