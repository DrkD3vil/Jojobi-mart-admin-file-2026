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
