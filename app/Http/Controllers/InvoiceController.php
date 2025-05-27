<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\InvoiceProduct;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'perPage' => 'nullable|integer|in:5,10,20,50,100',
            'search' => 'nullable|string',
            'status' => 'nullable|string|in:paid,unpaid',
            'orderBy' => 'nullable|string|in:id,users_id,code,to_name,grand_total',
            'orderDirection' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableContent($validator);
        }

        $perPage = $request->input('perPage', 10);
        $search = $request->input('search', '');
        $status = $request->input('status');
        $orderBy = $request->input('orderBy', 'id');
        $orderDirection = $request->input('orderDirection', 'desc');

        $user = Auth::user();
        $invoice = Invoice::query()->select(
            'id',
            'users_id',
            'code',
            'to_name',
            'to_telephone',
            'to_email',
            'status',
            'grand_total'
        )
            ->with('user:id,name,email,telephone')
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($user->role == 'user', function ($q) use ($user) {
                $q->where('users_id', $user->id);
            })
            ->where(function ($q) use ($search, $user) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('to_name', 'like', "%{$search}%");
                if ($user->role == 'admin') {
                    $q->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);

        $invoice->appends($validator->validate());

        if ($invoice->count() > 0) {
            return $this->dataFound($invoice, 'Invoice');
        }
        return $this->dataNotFound('Invoice');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $code = Str::upper(Str::random(7));

        $productCollect = collect($request->products);

        $productRes = $productCollect->map(function ($item) {
            $item['amount'] = ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            return $item;
        });

        $request['products'] = $productRes->toArray();
        $request['code'] = $code;
        $request['users_id'] = $user->id;

        $subTotal = $productRes->sum('amount');
        $request['sub_total'] = $subTotal;
        $request['total'] = $subTotal - $request['discount'];

        $request['grand_total'] = $request['tax'] == 1
            ? $request['total'] * 0.89
            : $request['total'];

        $request['remaining_balance'] = $request->status === 'paid'
            ? $request['grand_total']
            : $request['grand_total'] - $request['down_payment'];


        $validator = Validator::make($request->all(), [
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


            'products.*.name' => 'required|string|max:50',
            'products.*.unit' => 'required|string|max:50',
            'products.*.price' => 'required|integer|min:0',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.amount' => 'required|integer|min:0',
            'products' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableContent($validator);
        }

        try {
            Db::transaction(function () use ($request, $code, $validator) {

                $invoice = Invoice::create($validator->valid());

                $productCollect = collect($request->products);
                foreach ($request['products'] as $key => $product) {
                    InvoiceProduct::create([
                        'invoices_code' => $code,
                        'name' => $product['name'],
                        'unit' => $product['unit'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                        'amount' => $product['amount'],
                    ]);
                }
            });

            DB::commit();

            return $this->createSuccess($validator->valid());
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => true,
                'message' => $e->getMessage() . $e->getLine(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $code)
    {
        $invoice = Invoice::with('user:id,name,telephone')
            ->with('invoiceProducts:invoices_code,name,unit,price,quantity,amount')
            ->where('id', $id)
            ->where('code', $code)
            ->first();

        if (!$invoice) {
            return $this->dataNotFound('Invoice');
        }
        return $this->dataFound($invoice, 'Invoice');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->dataNotFound('Invoice');
        }

        $userLogin = Auth::user();
        if ($userLogin->role != 'admin' && $invoice->users_id != $userLogin->id) {
            return $this->unauthorizedResponse();
        }

        if ($invoice->status == 'paid') {
            return response()->json([
                'status' => 'false',
                'message' => 'Invoice lunas! Tidak bisa edit.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:paid,unpaid',
        ]);

        if ($validator->fails()) {
            return $this->unprocessableContent($validator);
        }

        $invoice->update($validator->validated());
        return $this->editSuccess($invoice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return $this->dataNotFound('Invoice');
        }

        $userLogin = Auth::user();
        if ($userLogin->role != 'admin' && $invoice->users_id != $userLogin->id) {
            return $this->unauthorizedResponse();
        }

        $invoice->delete();
        return $this->deleteSuccess();
    }
}
