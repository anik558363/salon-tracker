<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'customer_id',
        'entry_date',
        'total_amount',
        'note',
    ];

    // entry_date ke date/Carbon hishebe cast kora
    protected $casts = [
        'entry_date'   => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(ServiceEntryItem::class);
    }
}
