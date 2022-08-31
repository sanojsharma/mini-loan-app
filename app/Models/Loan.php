<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_amount',
        'loan_term',
        'user_id',
    ];

    public function loan_repayements()
    {
        return $this->hasMany(LoanRepayment::class);
    }
}
