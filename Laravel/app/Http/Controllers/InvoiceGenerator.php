<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Traits\BaseResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class InvoiceGenerator extends Controller
{
    use BaseResponse;

    public function generate($id, $code, $action)
    {
        $invoice = Invoice::where('id', $id)
            ->where('code', $code)
            ->with('invoiceProducts')
            ->first();

        if (!$invoice) {
            return $this->dataNotFound('Invoice');
        }

        $qrCode = GenerateQrCodeController::getQrCode(env('APP_URL_FRONTEND') . "invoice/$id/$code/");

        $pdf = Pdf::loadView('pdf.invoice', [
            'data' => $invoice,
            'qrCode' => $qrCode,
        ])
            // ->setWarnings(true)
            ->setOptions([
                'isRemoteEnabled' => true,
            ])
            ->setHttpContext(stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]));
        $random = Str::random(5);

        return $pdf->$action("invoice-$id-$code-$random.pdf", [
            true
        ]);
    }

    /**
     * Stream Data Invoice
     */
    public function stream($id, $code)
    {

        return $this->generate($id, $code, 'stream');
    }

    /**
     * Download Data Invoice
     */
    public function download($id, $code)
    {
        return $this->generate($id, $code, 'download');
    }

    /**
     * Show Data Invoice
     */
    public function show($id, $code)
    {
        $invoice = Invoice::where('id', $id)
            ->where('code', $code)
            ->with('invoiceProducts')
            ->with('user')
            ->first();

        if (!$invoice) {
            return $this->dataNotFound('Invoice');
        }
        return $this->dataFound($invoice, 'Invoice');
    }
}
