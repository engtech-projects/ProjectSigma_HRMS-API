<?php

namespace App\Models;

use App\Enums\LoanPaymentPostingStatusType;
use App\Enums\LoanPaymentsType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class OtherDeduction extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    protected $fillable = [
        'id',
        'employee_id',
        'otherdeduction_name',
        'total_amount',
        'terms_of_payment',
        'otherdeduction_name',
        'no_of_installments',
        'installment_amount',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function otherDeductionPayment(): HasMany
    {
        return $this->hasMany(OtherDeductionPayments::class, 'otherdeduction_id', 'id');
    }

    public function cashPaid()
    {
        $totalpaid = $this->otherDeductionPayment()->sum("amount_paid");
        if ($this->total_amount <= $totalpaid) {
            return true;
        }
        return false;
    }

    public function paymentWillOverpay($amount)
    {
        $totalpaid = $this->otherDeductionPayment()->sum('amount_paid');

        if ($this->total_amount < $totalpaid + $amount) {
            return true;
        }
        return false;
    }

    public function cashAdvance($paymentAmount, $type)
    {
        if ($this->cashPaid()) {
            return false;
        }

        if ($this->paymentWillOverpay($paymentAmount)) {
            return false;
        }

        if ($type == LoanPaymentsType::MANUAL->value) {
            $this->otherDeductionPayment()->create([
                'otherdeduction_id' => $this->id,
                'amount_paid' => $paymentAmount,
                'date_paid' => Carbon::now(),
                'payment_type' => LoanPaymentsType::MANUAL,
                'posting_status' => LoanPaymentPostingStatusType::POSTED
            ]);
        } else {
            $this->otherDeductionPayment()->create([
                'otherdeduction_id' => $this->id,
                'amount_paid' => $paymentAmount,
                'date_paid' => Carbon::now(),
                'payment_type' => LoanPaymentsType::MANUAL,
                'posting_status' => LoanPaymentPostingStatusType::NOTPOSTED
            ]);
        }

        return true;
    }
}
