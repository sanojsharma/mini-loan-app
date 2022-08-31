<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'repayment_id',
        'amount_paid',
        'transaction_date',
        'paid_by',
        'status'
    ];

}
