<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceEntry extends Model
{
    protected $fillable = [
        'employee_id',
        'customer_id',
        'entry_date',
        'total_amount',
        'note',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceEntryItem::class);
    }
}
