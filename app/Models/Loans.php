<?php

namespace App\Models;

use App\Enums\LoanPaymentPostingStatusType;
use App\Enums\LoanPaymentsType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\TermsOfPaymentType;

class Loans extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $casts = [
        "deduction_date_start" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'employee_id',
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

    public function loan_payments_employee(): HasMany
    {
        return $this->hasMany(LoanPayments::class);
    }

    public function loanPayments(): HasMany
    {
        return $this->hasMany(LoanPayments::class)->where("posting_status", LoanPaymentPostingStatusType::POSTED);
    }
    public function loan_payment_notposted(): HasMany
    {
        return $this->hasMany(LoanPayments::class)->where("posting_status", LoanPaymentPostingStatusType::NOTPOSTED);
    }

    public function getInstallmentDeductionTermAttribute()
    {
        switch ($this->terms_of_payment) {
            case TermsOfPaymentType::WEEKLY->value:
                return round($this->installment_deduction / 4);
                break;

            case TermsOfPaymentType::MONTHLY->value:
                return round($this->installment_deduction / 2);
                break;

            case TermsOfPaymentType::BIMONTHLY->value:
                return round($this->installment_deduction / 1);
                break;
        }
    }

    public function getMaxPayrollPaymentAttribute()
    {
        return $this->installment_deduction_term > $this->balance ? $this->balance : $this->installment_deduction_term;
    }

    public function getBalanceAttribute()
    {
        return $this->amount - $this->totalPaid;
    }

    public function getTotalPaidAttribute()
    {
        return $this->loanPayments()->sum("amount_paid");
    }

    public function loanPaid()
    {
        $totalpaid = $this->loanPayments()->sum('amount_paid');
        if ($this->amount <= $totalpaid) {
            return true;
        }
        return false;
    }

    public function paymentWillOverpay($paymentAmount)
    {
        $totalpaid = $this->loanPayments()->sum('amount_paid');

        if ($this->amount < $totalpaid + $paymentAmount) {
            return true;
        }
        return false;
    }

    public function loanPayment($paymentAmount, $type)
    {

        if ($this->loanPaid()) {
            return false;
        }

        if ($this->paymentWillOverpay($paymentAmount)) {
            return false;
        }

        if ($type == LoanPaymentsType::MANUAL->value) {
            $this->loanPayments()->create([
                'loans_id' => $this->id,
                'amount_paid' => $paymentAmount,
                'date_paid' => Carbon::now(),
                'payment_type' => LoanPaymentsType::MANUAL,
                'posting_status' => LoanPaymentPostingStatusType::POSTED
            ]);
        } else {
            $this->loanPayments()->create([
                'loans_id' => $this->id,
                'amount_paid' => $paymentAmount,
                'date_paid' => Carbon::now(),
                'payment_type' => LoanPaymentsType::MANUAL,
                'posting_status' => LoanPaymentPostingStatusType::NOTPOSTED
            ]);
        }

        return true;
    }
}
