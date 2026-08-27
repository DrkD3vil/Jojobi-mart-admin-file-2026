<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'expense_date' => ['required','date'],
            'expense_category_id' => ['nullable','exists:expense_categories,id'],
            'location_id' => ['nullable','exists:locations,id'],

            'title' => ['required','string','max:190'],
            'description' => ['nullable','string'],

            'vendor_name' => ['nullable','string','max:190'],
            'payment_method' => ['nullable','string','max:50'],
            'reference_no' => ['nullable','string','max:190'],

            'amount' => ['required','numeric','min:0'],
            'currency' => ['nullable','string','max:10'],

            'receipt_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'remove_receipt' => ['nullable','boolean'],
        ];
    }
}
