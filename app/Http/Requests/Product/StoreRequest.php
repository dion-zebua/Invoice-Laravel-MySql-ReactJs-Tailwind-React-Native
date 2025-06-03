<?php

namespace App\Http\Requests\Product;

use App\Traits\BaseResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    use BaseResponse;
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
            'name' => 'required|string|max:50',
            'unit' => 'required|string|max:50',
            'price' => 'required|integer|min:0',
        ];
    }
}
