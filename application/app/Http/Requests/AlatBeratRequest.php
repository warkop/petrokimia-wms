<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlatBeratRequest extends FormRequest
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
        $rules = [
            'nomor_lambung'    => ['required', Rule::unique('alat_berat', 'nomor_lambung')->ignore(\Request::instance()->id)],
            'nomor_polisi'     => ['required', Rule::unique('alat_berat', 'nomor_polisi')->ignore(\Request::instance()->id)],
        ];
        
        return $rules;
    }
}
