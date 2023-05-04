<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'id_transaction',
        'user_id',
        //->datos de envio:
        'address',
        'city',
        'region',
        'phone',
        'payment_method',
        'amount_in_cents',
    ];
}
