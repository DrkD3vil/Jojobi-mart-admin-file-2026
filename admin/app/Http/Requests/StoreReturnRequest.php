<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required','integer','exists:orders,id'],
            'location_id' => ['required','integer','exists:locations,id'],
            'refund_method' => ['nullable','string'],
            'note' => ['nullable','string'],
            // Submitted as a hidden field generated once per page load, so a
            // double form submission (double-click, back-button resubmit)
            // reuses the same key and gets caught by the idempotency check
            // in ReturnController::store() instead of posting a duplicate return.
            'idempotency_key' => ['nullable','string','max:100'],

            'items' => ['required','array','min:1'],
            'items.*.order_item_id' => ['required','integer','exists:order_items,id'],
            'items.*.product_id' => ['required','integer','exists:products,id'],
            'items.*.product_batch_id' => ['required','integer','exists:product_batches,id'],
            'items.*.qty' => ['required','numeric','gt:0'],
            'items.*.condition' => ['nullable','string'],
            'items.*.reason_code' => ['nullable','string'],
        ];
    }
}
