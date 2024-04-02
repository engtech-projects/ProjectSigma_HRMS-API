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

class CashAdvance extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $casts = [
        'approvals' => 'array'
    ];

    protected $fillable = [
        'id',
        'employee_id',
        'department_id',
        'project_id',
        'amount_requested',
        'amount_approved',
        'purpose',
        'terms_of_cash_advance',
        'remarks',
        'request_status',
        'approvals',
        'released_by',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function cashAdvancePayments(): HasMany
    {
        return $this->hasMany(CashAdvancePayments::class, 'cashadvance_id', 'id');
    }

    public function cashPaid()
    {
        $totalpaid = $this->cashAdvancePayments()->sum("amount_paid");
        if ($this->amount_requested <= $totalpaid) {
            return true;
        }
        return false;
    }

    public function paymentWillOverpay($amount)
    {
        $totalpaid = $this->loanPayments()->sum('amount_paid');

        if ($this->amount_requested < $totalpaid + $amount) {
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
            $this->cashAdvancePayments()->create([
                'cashadvance_id' => $this->id,
                'amount_paid' => $paymentAmount,
                'date_paid' => Carbon::now(),
                'payment_type' => LoanPaymentsType::MANUAL,
                'posting_status' => LoanPaymentPostingStatusType::POSTED
            ]);
        } else {
            $this->cashAdvancePayments()->create([
                'cashadvance_id' => $this->id,
                'amount_paid' => $paymentAmount,
                'date_paid' => Carbon::now(),
                'payment_type' => LoanPaymentsType::MANUAL,
                'posting_status' => LoanPaymentPostingStatusType::NOTPOSTED
            ]);
        }

        return true;
    }
}
