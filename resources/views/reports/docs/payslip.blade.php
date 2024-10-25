<div style="height: 50%; width: 100%; overflow:hidden;">
    <div class="logo_container">
        <img src="{{ asset('images/print_logo.jpg') }}" alt="logo" style="height: 65px">
        <h2 style="text-transform: uppercase; font-weight: bold;">PAYSLIP</h2>
    </div>
    <div style="display:flex; justify-content: space-between;">
        <table>
            <tr>
                <td class="font-weight:bold;" style="color:rgb(48, 85, 80);">Employee Name: <span style="color:black;">{{ $payroll->employee->fullname_first }}</span></td>
                <td></td>
                <td class="payroll_label" style="color:rgb(48, 85, 80)"><span>Rate: </span><span style="color:black;">{{ $payroll->employee->current_salarygrade_and_step }}</span></td>
                <td class="payroll_label" style="color:rgb(48, 85, 80)"><span>Total Days of Work: </span><span style="color:black;">{{ intval($payroll->regular_hours / 8) + ($payroll->regular_hours % 8 > 0 ? 0.5 : 0) }} day/s</span></td>
            </tr>
            <tr>
                <td class="payroll_label" style="color:rgb(48, 85, 80)"><span>Pay Period: </span><span style="color:black;">{{ \Carbon\Carbon::parse($payroll->payroll_record->cutoff_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($payroll->payroll_record->cutoff_end)->format('M d, Y') }}</span></td>
                <td></td>
                <td colspan="2" class="payroll_label" style="max-width: 200px; color:rgb(48, 85, 80); overflow:hidden; white-space:nowrap; line-height: 20px"><span>Section / Proj: </span><span style="color:black;">{{ $payroll->salary_charging_names }}</span></td>
            </tr>
        </table>
    </div>
    <div style="display: flex; height:256px;">
        <table style="width: 55%; height:fit-content; table-layout: fixed">
            <colgroup>
                <col style="width: 33.33%;">
                <col style="width: 33.33%;">
                <col style="width: 33.33%;">
            </colgroup>
            <tr class="table_header">
                <th style="text-align: right;">Earnings</th>
                <th style="text-align: right;">Hours</th>
                <th style="text-align: right;">Amount</th>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Basic Reg. hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_hours, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Basic Reg. OT hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_overtime, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_ot_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Rest day/sun hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->rest_hours, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->rest_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Rest day OT hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->rest_overtime, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->rest_ot_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Reg/Hol hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_holiday_hours, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_holiday_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Reg/Hol OT hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_holiday_overtime, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->regular_holiday_ot_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Spcl./Hol hrs. -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->special_holiday_hours, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->special_holiday_pay, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Adjustments -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">-</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->total_adjustment, 2) }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
                <td style="text-align: right; padding:0px; line-height:16px;"></td>
            </tr>
        </table>
        <table style="width: 45%; height:fit-content; table-layout: fixed">
            <colgroup>
                <col style="width: 50%;">
                <col style="width: 50%;">
            </colgroup>
            <tr class="table_header">
                <th style="text-align: right;">Deduction:</th>
                <th style="text-align: right;">Amount</th>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Withholding Tax -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->withholdingtax_contribution, 2) }}</td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">SSS -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->sss_employee_contribution + $payroll->sss_employee_compensation + $payroll->sss_employee_wisp, 2) }}</td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">PHIC -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->philhealth_employee_contribution, 2) }}</td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">HMDF -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->pagibig_employee_contribution, 2) }}</td>
            </tr>
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">CASH ADVANCE -</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($payroll->total_cash_advance_payments, 2) }}</td>
            </tr>
            @foreach ($payroll->loanPayments ?? [] as $loan)
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">Loan</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($loan->amount, 2) }}</td>
            </tr>
            @endforeach
            @foreach ($payroll->otherDeductionPayments ?? [] as $otherDeduction)
            <tr>
                <td class="payroll_label" style="text-align: right; padding:0px; line-height:16px;">{{ $otherDeduction->deduction->otherdeduction->otherdeduction_name }}</td>
                <td style="text-align: right; padding:0px; line-height:16px;">{{ number_format($otherDeduction->amount, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <table>
        <tr class="table_header">
            <tr class="table_header">
                <td colspan="4">Total Earnings: {{ number_format($payroll->gross_pay, 2) }}</td>
                <td>Total Deductions: {{ number_format($payroll->total_deduct, 2) }}</td>
                <td>Net Pay: {{ number_format($payroll->net_pay, 2) }}</td>
            </tr>
        </tr>
    </table>
    <div style="display:flex; justify-content: space-between;">
        <div style="padding-left:10px;">
            <p class="payroll_label">Date: {{ \Carbon\Carbon::today()->format('F j, Y') }}</p>
            <p class="payroll_label">Receive by:</p>
        </div>
    </div>
</div>
