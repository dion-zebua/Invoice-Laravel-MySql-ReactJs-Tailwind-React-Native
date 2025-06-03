<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataCount extends Controller
{
    use BaseResponse;

    /**
     * All Data Count
     */
    public function allDataCount()
    {
        $auth = Auth::user();

        $user = User::count();
        $product = Product::when($auth->role == 'user', function ($q) use ($auth) {
            $q->where('users_id', $auth->id);
        })->count();
        $invoice = Invoice::when($auth->role == 'user', function ($q) use ($auth) {
            $q->where('users_id', $auth->id);
        })->count();

        $data = [
            "user" => $auth->role == 'admin' ? $user : 0,
            "product" => $product,
            "invoice" => $invoice,
        ];

        return $this->dataFound($data, "Total data ditemukan.");
    }
}
