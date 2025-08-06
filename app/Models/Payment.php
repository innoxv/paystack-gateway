<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reference',
        'amount',
        'currency',
        'payment_method',
        'phone',
        'status',
    ];
}
