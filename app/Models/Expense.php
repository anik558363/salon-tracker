<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'expense_category_id',
        'user_id',
        'amount',
        'expense_date',
        'note',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // ExpenseController এবং Blade ভিউতে এই নাম খোঁজা হচ্ছে:
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // LedgerController এর জন্য এটিও রাখা ভালো:
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
