<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Product;
use App\Traits\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use BaseResponse;
    /**
     * All Products.
     */
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search', '');
        $orderBy = $request->input('orderBy', 'id');
        $orderDirection = $request->input('orderDirection', 'desc');

        $user = Auth::user();
        $product = Product::query()->select('id', 'users_id', 'name', 'unit', 'price')
            ->with('user:id,name')
            ->when($user->role == 'user', function ($q) use ($user) {
                $q->where('users_id', $user->id);
            })
            ->where(function ($q) use ($search) {
                $q->Where('name', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            })
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage = $perPage, $page = $page);

        $product->appends($validated);

        if ($product->count() > 0) {
            return $this->dataFound($product, 'Produk');
        }
        return $this->dataNotFound('Produk');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store Product
     */
    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $validated['users_id'] = $user->id;

        $product = Product::create($validated);
        return $this->createSuccess($product);
    }

    /**
     * Show Product
     */
    public function show($id)
    {

        $product = Product::find($id);

        if (!$product) {
            return $this->dataNotFound('Produk');
        }

        $userLogin = Auth::user();
        if ($userLogin->role != 'admin' && $product->users_id != $userLogin->id) {
            return $this->unauthorizedResponse();
        }

        return $this->dataFound($product, 'Produk');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update Product
     */
    public function update(UpdateRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->dataNotFound('Produk');
        }

        $userLogin = Auth::user();
        if ($userLogin->role != 'admin' && $product->users_id != $userLogin->id) {
            return $this->unauthorizedResponse();
        }

        $validated = $request->validated();

        $product->update($validated);
        return $this->editSuccess($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->dataNotFound('Produk');
        }

        $userLogin = Auth::user();
        if ($userLogin->role != 'admin' && $product->users_id != $userLogin->id) {
            return $this->unauthorizedResponse();
        }

        $product->delete();
        return $this->deleteSuccess();
    }
}
