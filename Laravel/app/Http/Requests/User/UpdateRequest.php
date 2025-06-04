<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Traits\BaseResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;

class UpdateRequest extends FormRequest
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
        $id = $this->route('id');
        $user = User::find($id);

        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $id,
            'sales' => 'required|string|max:50',
            'logo' => (!File::exists($user->logo['path']) ? "required" : 'nullable') . '|image|mimes:jpeg,jpg,png,webp|max:3072',
            'telephone' => 'required|string|min:6|max:15',
            'address' => 'required|string|max:100',
            'payment_methode' => 'required|string|max:100',
            'payment_name' => 'required|string|max:100',
            'payment_number' => 'required|string|max:100',
        ];
    }
}
