<?php

namespace App\Http\Requests\Invoice;

use App\Traits\BaseResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
            'order' => 'required|date',
            'expire' => 'required|date|after_or_equal:order',
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

    public function prepareForValidation()
    {
        $productCollect = collect($this->products);

        $productRes = $productCollect->map(function ($item) {
            $item['amount'] = ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            return $item;
        });

        $this['order'] = Carbon::parse($this->order)->format('Y-m-d H:i:s');
        $this['expire'] = Carbon::parse($this->expire)->format('Y-m-d H:i:s');

        $this['products'] = $productRes->toArray();

        $subTotal = $productRes->sum('amount');
        $this['sub_total'] = $subTotal;
        $this['total'] = $subTotal - $this['discount'];

        $this['grand_total'] = $this['tax'] == 1
            ? $this['total'] * 0.89
            : $this['total'];

        $this['remaining_balance'] = $this->status === 'paid'
            ? $this['grand_total']
            : $this['grand_total'] - $this['down_payment'];
    }
}
