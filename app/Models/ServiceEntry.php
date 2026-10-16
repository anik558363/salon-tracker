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
        return $this->belongsTo(Customer::class);
    }

    // Ei relationship-ti ensure koro
    public function items()
    {
        return $this->hasMany(ServiceEntryItem::class, 'service_entry_id');
    }
}
