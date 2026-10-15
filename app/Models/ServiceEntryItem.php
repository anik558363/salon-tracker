<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceEntryItem extends Model
{
    protected $fillable = [
        'service_entry_id',
        'service_id',
        'price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceEntry()
    {
        return $this->belongsTo(ServiceEntry::class);
    }
}
