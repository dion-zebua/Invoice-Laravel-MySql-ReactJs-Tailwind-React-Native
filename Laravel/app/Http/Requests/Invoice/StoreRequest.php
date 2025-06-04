<?php

namespace App\Http\Requests\Invoice;

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
            'code' => 'required|string|unique:invoices,code',
            'expire' => 'required|date|after_or_equal:today',
            'to_name' => 'required|string|max:30',
            'to_sales' => 'required|string|max:30',
            'to_address' => 'required|string|max:70',
            'to_telephone' => 'required|string|max:15|min:6',
            'to_email' => 'required|email',
            'sub_total' => 'required|integer|min:0',
            'discount' => 'required|integer|min:0',
            'total' => 'required|integer|min:0',
            'tax' => 'required|in:1,0',
            'grand_total' => 'required|integer|min:0',
            'down_payment' => 'required|integer|min:0',
            'remaining_balance' => 'required|integer|min:0',
            'status' => 'required|in:paid,unpaid',

            // Invoice Product
            'products.*.name' => 'required|string|max:50',
            'products.*.unit' => 'required|string|max:50',
            'products.*.price' => 'required|integer|min:0',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.amount' => 'required|integer|min:0',
            'products' => 'required|array|max:20',
        ];
    }
}
