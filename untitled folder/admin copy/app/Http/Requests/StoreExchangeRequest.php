<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExchangeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required','integer','exists:orders,id'],
            'location_id' => ['required','integer','exists:locations,id'],
            'note' => ['nullable','string'],

            // RETURN lines
            'return_lines' => ['required','array','min:1'],
            'return_lines.*.order_item_id' => ['required','integer','exists:order_items,id'],
            'return_lines.*.product_id' => ['required','integer','exists:products,id'],
            'return_lines.*.product_batch_id' => ['required','integer','exists:product_batches,id'],
            'return_lines.*.qty' => ['required','numeric','gt:0'],
            'return_lines.*.unit_price' => ['required','numeric','gte:0'],

            // ISSUE lines (replacement items)
            'issue_lines' => ['required','array','min:1'],
            'issue_lines.*.product_id' => ['required','integer','exists:products,id'],
            'issue_lines.*.product_batch_id' => ['required','integer','exists:product_batches,id'],
            'issue_lines.*.qty' => ['required','numeric','gt:0'],
            'issue_lines.*.unit_price' => ['required','numeric','gte:0'],
        ];
    }
}
