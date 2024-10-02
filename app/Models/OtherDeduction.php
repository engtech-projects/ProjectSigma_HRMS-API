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

    protected $casts = [
        "deduction_date_start" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'employee_id',
        'otherdeduction_name',
        'terms_of_payment', // Not Sure but Removable. deduct every payroll
        'installment_deduction',
        'amount',
        'deduction_date_start',
    ];

    protected $appends = [
        "total_paid",
        "balance",
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function otherDeductionPayment(): HasMany
    {
        return $this->hasMany(OtherDeductionPayments::class, 'otherdeduction_id', 'id');
    }

    public function otherDeductionPaymentPosted(): HasMany
    {
        return $this->hasMany(OtherDeductionPayments::class, 'otherdeduction_id', 'id')->isPosted();
    }

    public function getBalanceAttribute()
    {
        return floatval($this->amount - $this->totalPaid);
    }

    public function getTotalPaidAttribute()
    {
        return $this->otherDeductionPaymentPosted()->sum("amount_paid");
    }

    public function cashPaid()
    {
        $totalpaid = $this->otherDeductionPaymentPosted()->sum("amount_paid");
        if ($this->amount <= $totalpaid) {
            return true;
        }
        return false;
    }

    public function paymentWillOverpay($amount)
    {
        $totalpaid = $this->otherDeductionPaymentPosted()->sum('amount_paid');

        if ($this->amount < $totalpaid + $amount) {
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

    public function getCreatedAtHumanAttribute()
    {
        return Carbon::parse($this->created_at)->format("F j, Y");
    }

    public function getDeductionStartHumanAttribute()
    {
        return Carbon::parse($this->deduction_date_start)->format("F j, Y");
    }
}
