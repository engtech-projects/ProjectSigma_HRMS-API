<?php

namespace App\Models;

use App\Enums\LoanPaymentPostingStatusType;
use App\Traits\HasApproval;
use App\Enums\LoanPaymentsType;
use App\Enums\PersonelAccessForm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class CashAdvance extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;

    protected $casts = [
        'approvals' => 'array',
        "deduction_date_start" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'employee_id',
        'department_id',
        'project_id',
        'terms_of_payment',
        'no_of_installment',
        'installment_deduction',
        'deduction_date_start',
        'amount',
        'purpose',
        'remarks',
        'request_status',
        'approvals',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
    }

    public function cashAdvancePayments(): HasMany
    {
        return $this->hasMany(CashAdvancePayments::class, 'cashadvance_id', 'id');
    }

    public function cashPaid()
    {
        $totalpaid = $this->cashAdvancePayments()->sum("amount_paid");
        if ($this->amount <= $totalpaid) {
            return true;
        }
        return false;
    }

    public function paymentWillOverpay($amount)
    {
        // $totalpaid = $this->loanPayments()->sum('amount_paid');
        $totalpaid = $this->cashAdvancePayments()->sum('amount_paid');

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

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }
}
