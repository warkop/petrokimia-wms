<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'nomor_lambung'    => ['required', Rule::unique('alat_berat', 'nomor_lambung')->ignore(request()->id)],
        ];
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'nomor_lambung'     => 'Nomor Lambung',
            'nomor_polisi'      => 'Nomor Polisi',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi!',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }

        $this->replace($input);
    }
}
