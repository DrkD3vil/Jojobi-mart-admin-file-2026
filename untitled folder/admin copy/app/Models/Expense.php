<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_no','expense_date',
        'expense_category_id','location_id','created_by',
        'title','description',
        'vendor_name','payment_method','reference_no',
        'amount','currency',
        'receipt_image','meta',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:4',
        'meta' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_image ? asset('storage/'.$this->receipt_image) : null;
    }
}
