<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\IndexRequest;
use App\Http\Requests\Invoice\StoreRequest;
use App\Http\Requests\Invoice\UpdateRequest;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InvoiceProduct;
use App\Traits\BaseResponse;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use BaseResponse;
    /**
     * All Invoices
     */
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

        $page = $request->input('page', 1);
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
            ->paginate($perPage = $perPage, $page = $page);


        $invoice->appends($validated);

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
     * Store Invoice.
     */
    public function store(StoreRequest $request)
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


        $validated = $request->validated();

        try {
            Db::transaction(function () use ($code, $validated) {

                Invoice::create($validated);

                $data = [];

                foreach ($validated['products'] as $product) {
                    $data[] = [
                        'invoices_code' => $code,
                        'name' => $product['name'],
                        'unit' => $product['unit'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                        'amount' => $product['amount'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                InvoiceProduct::insert($data);
            });

            DB::commit();

            return $this->createSuccess($validated);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Show Invoice
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
     * Update Invoice
     */
    public function update(UpdateRequest $request, $id)
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
            return $this->unauthorizedResponse('Invoice lunas! Tidak bisa edit.');
        }

        $validated = $request->validated();

        $invoice->update($validated);
        return $this->editSuccess($invoice);
    }

    /**
     * Destroy Invoice
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
