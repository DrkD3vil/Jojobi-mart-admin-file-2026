<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from_location_id' => ['required','integer','exists:locations,id'],
            'to_location_id'   => ['required','integer','exists:locations,id','different:from_location_id'],
            'note'             => ['nullable','string','max:500'],

            'lines' => ['required','array','min:1'],
            'lines.*.product_id'        => ['required','integer','exists:products,id'],
            'lines.*.product_batch_id'  => ['required','integer','exists:product_batches,id'],
            'lines.*.qty'               => ['required','numeric','gt:0'],
            'lines.*.unit'              => ['nullable','string','max:20'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $lines = $this->input('lines', []);
            $batches = array_filter(array_map(fn($l)=> $l['product_batch_id'] ?? null, $lines));

            // Optional: block duplicates (removes double counting mistakes)
            if (count($batches) !== count(array_unique($batches))) {
                $v->errors()->add('lines', 'Duplicate batch detected. Please merge quantities into one line.');
            }
        });
    }
}
