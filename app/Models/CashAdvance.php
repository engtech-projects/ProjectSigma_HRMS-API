<?php

namespace App\Models;

use App\Enums\LoanPaymentPostingStatusType;
use App\Traits\HasApproval;
use App\Enums\LoanPaymentsType;
use App\Enums\PersonelAccessForm;
use App\Models\Traits\StatusScope;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
    use StatusScope;
    use ModelHelpers;

    protected $casts = [
        'approvals' => 'array',
        "deduction_date_start" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'employee_id',
        'department_id',
        'project_id',
        'terms_of_payment', // Not Sure but Removable. deduct every payroll
        'installment_deduction',
        'amount',
        'deduction_date_start',
        'purpose',
        'remarks',
        'request_status',
        'created_by',
        'approvals',
    ];

    protected $appends = [
        "total_paid",
        "balance",
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

    public function cashAdvancePaymentsPosted(): HasMany
    {
        return $this->hasMany(CashAdvancePayments::class, 'cashadvance_id', 'id')->isPosted();
    }

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(Users::class, "created_by", "id");
    }

    public function getBalanceAttribute()
    {
        return round($this->amount - $this->totalPaid, 2);
    }

    public function getTotalPaidAttribute()
    {
        return round($this->cashAdvancePaymentsPosted()->sum("amount_paid"), 2);
    }

    public function cashPaid()
    {
        $totalpaid = $this->cashAdvancePaymentsPosted()->sum("amount_paid");
        if ($this->amount <= $totalpaid) {
            return true;
        }
        return false;
    }

    public function paymentWillOverpay($amount)
    {
        // $totalpaid = $this->loanPayments()->sum('amount_paid');
        $totalpaid = $this->cashAdvancePaymentsPosted()->sum('amount_paid');

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
    public function scopeRequestStatusApproved(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_APPROVED);
    }

    public function payroll_detail_deduction(): MorphOne
    {
        return $this->morphOne(PayrollDetailDeduction::class, 'deduction');
    }

    public function payroll_details_charging(): MorphOne
    {
        return $this->morphOne(PayrollDetailDeduction::class, 'charge');
    }
}
