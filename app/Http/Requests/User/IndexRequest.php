<?php

namespace App\Http\Requests\User;

use App\Traits\BaseResponse;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'page' => 'nullable|integer',
            'perPage' => 'nullable|integer|in:5,10,20,50,100',
            'is_verified' => 'nullable|in:0,1',
            'search' => 'nullable|string',
            'role' => 'nullable|string|in:admin,user',
            'orderBy' => 'nullable|string|in:id,name,sales,telephone,invoice_count',
            'orderDirection' => 'nullable|string|in:asc,desc',
        ];
    }
}
