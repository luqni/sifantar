<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'patient_id', 
        'courier_id', 
        'status', 
        'tracking_number', 
        'total_price', 
        'delivery_address', 
        'latitude',
        'longitude',
        'estimation_arrival'
    ];

    protected $casts = [
        'estimation_arrival' => 'datetime'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryItem::class);
    }
}
