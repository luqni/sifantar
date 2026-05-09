<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryItem extends Model
{
    protected $fillable = ['delivery_id', 'medicine_id', 'medicine_name', 'quantity', 'price_at_time'];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
