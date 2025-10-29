<?php

namespace Factuapi\PhpSdk\Resources;

use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;
use Factuapi\PhpSdk\Requests\Invoices\CancelInvoiceRequest;
use Factuapi\PhpSdk\Requests\Invoices\CreateInvoiceRequest;
use Saloon\Http\BaseResource;

class Invoices extends BaseResource
{
    public function create(
        Invoice $invoice,
        array $process,
    ): CreateInvoiceResponse {
        $request = new CreateInvoiceRequest(
            $invoice,
            $process,
        );

        return $this->connector->send($request)->dto();
    }

    public function correct(
        InvoiceId $previousInvoiceId,
        Invoice $invoice,
        array $process,
    ): CreateInvoiceResponse {
        $request = new CreateInvoiceRequest(
            invoiceData: $invoice,
            process: $process,
            correctInvoiceId: $previousInvoiceId,
        );

        return $this->connector->send($request)->dto();
    }

    public function cancel(
        InvoiceId $invoiceId,
        array $process,
    ): CreateInvoiceResponse {
        $request = new CancelInvoiceRequest(
            invoiceId: $invoiceId,
            process: $process,
        );

        return $this->connector->send($request)->dto();
    }
}
