<?php

namespace Factuapi\PhpSdk\Requests\Invoices;

use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceItem;
use Factuapi\PhpSdk\Dto\Invoices\RelatedInvoice;
use Factuapi\PhpSdk\Dto\Recipients\OtherRecipient;
use Factuapi\PhpSdk\Dto\Recipients\SpanishRecipient;
use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateInvoiceRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly Invoice $invoiceData,
        public readonly array $process,
        public readonly ?InvoiceId $correctInvoiceId = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        if ($this->correctInvoiceId) {
            return "/invoices/correct";
        }

        return '/invoices';
    }

    protected function defaultBody(): array
    {
        $invoice = [
            'invoice_series_code' => $this->invoiceData->invoiceId->seriesCode,
            'invoice_number' => $this->invoiceData->invoiceId->number,
            'issue_date' => $this->invoiceData->issueDate->format('Y-m-d'),
            'invoice_type' => $this->invoiceData->invoiceType->value,
            'issuer_tax_number' => $this->invoiceData->issuer->taxNumber,
            'issuer_name' => $this->invoiceData->issuer->name,
            'issuer_address' => $this->invoiceData->issuer->address,
            'issuer_post_code' => $this->invoiceData->issuer->postCode,
            'issuer_city' => $this->invoiceData->issuer->city,
            'issuer_province' => $this->invoiceData->issuer->province,
            'items' => array_map(function (InvoiceItem $invoiceItem) {
                $item = [
                    "description" => $invoiceItem->description,
                    "base_price" => $invoiceItem->basePrice,
                    "tax_type" => $invoiceItem->taxType->value,
                    "regime_key" => $invoiceItem->regimeKey->getValue(),
                    "tax_rate" => $invoiceItem->taxRate,
                    "tax_price" => $invoiceItem->taxPrice,
                    "amount" => $invoiceItem->amount,
                ];

                if ($invoiceItem->equivalenceSurchargeType) {
                    $item["equivalence_surcharge_type"] = $invoiceItem->equivalenceSurchargeType;
                }

                if ($invoiceItem->equivalenceSurchargeType) {
                    $item["equivalence_surcharge_price"] = $invoiceItem->equivalenceSurchargePrice;
                }

                if ($invoiceItem->exemptionReason) {
                    $item["exemption_reason"] = $invoiceItem->exemptionReason->value;
                }

                if ($invoiceItem->operationQualification) {
                    $item["operation_qualification"] = $invoiceItem->operationQualification->value;
                }

                return $item;
            }, $this->invoiceData->items),
            'totals' => [
                'base_price' => $this->invoiceData->totals->basePrice,
                'tax_price' => $this->invoiceData->totals->taxPrice,
                'amount' => $this->invoiceData->totals->amount,
            ],
            'item_description' => $this->invoiceData->itemDescription,
        ];

        if ($this->correctInvoiceId) {
            $invoice['previous_invoice'] = [
                'invoice_series_code' => $this->correctInvoiceId->seriesCode,
                'invoice_number' => $this->correctInvoiceId->number,
            ];
        }

        if ($this->invoiceData->recipient instanceof SpanishRecipient) {
            $invoice['recipient_tax_number'] = $this->invoiceData->recipient->taxNumber;

            $invoice['recipient_name'] = $this->invoiceData->recipient->name;
            $invoice['recipient_address'] = $this->invoiceData->recipient->address;
            $invoice['recipient_post_code'] = $this->invoiceData->recipient->postCode;
            $invoice['recipient_city'] = $this->invoiceData->recipient->city;
            $invoice['recipient_province'] = $this->invoiceData->recipient->province;
        }

        if ($this->invoiceData->recipient instanceof OtherRecipient) {
            $invoice['recipient_id_other_type']['country_code'] = $this->invoiceData->recipient->countryCode;
            $invoice['recipient_id_other_type']['id_type'] = $this->invoiceData->recipient->idType->value;
            $invoice['recipient_id_other_type']['id'] = $this->invoiceData->recipient->idNumber;

            $invoice['recipient_name'] = $this->invoiceData->recipient->name;
            $invoice['recipient_address'] = $this->invoiceData->recipient->address;
            $invoice['recipient_post_code'] = $this->invoiceData->recipient->postCode;
            $invoice['recipient_city'] = $this->invoiceData->recipient->city;
            $invoice['recipient_province'] = $this->invoiceData->recipient->province;
        }

        if ($this->invoiceData->creditNote) {
            $invoice['credit_note'] = [
                'type' => $this->invoiceData->creditNote->type->value,
                'category' => $this->invoiceData->creditNote->category->value,
                'base_price' => $this->invoiceData->creditNote->basePrice,
                'tax_price' => $this->invoiceData->creditNote->taxPrice,
            ];
        }

        if ($this->invoiceData->relatedInvoices && ! empty($this->invoiceData->relatedInvoices)) {
            $invoice['related_invoices'] = array_map(function (RelatedInvoice $relatedInvoice) {
                return [
                    "invoice_series_code" => $relatedInvoice->invoiceId->seriesCode,
                    "invoice_number" => $relatedInvoice->invoiceId->number,
                    "issue_date" => $relatedInvoice->issueDate->format('Y-m-d'),
                    "issuer_tax_number" => $relatedInvoice->issuerTaxNumber,
                ];
            }, $this->invoiceData->relatedInvoices);
        }

        return [
            'invoice' => $invoice,
            'process' => $this->process,
        ];
    }

    public function createDtoFromResponse(Response $response): CreateInvoiceResponse
    {
        return CreateInvoiceResponse::fromResponse($response->json());
    }
}
