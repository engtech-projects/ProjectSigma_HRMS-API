<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Payslip</title>
    </head>
    <style>
        body {
            padding: 0;
            font-family: Arial, sans-serif;
        }
        @font-face {
            font-family: 'AkagiPro-extrabold';
            src: url("{{ asset('fonts/akagipro-extrabold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'AkagiPro-bold';
            src: url("{{ asset('fonts/akagipro-bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        h2 {
            font-family: 'AkagiPro-extrabold';
        }
        .payroll_label {
            font-family: 'akagipro-bold';
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
        }
        .email {
            color: rgb(138, 210, 90);
            text-decoration: underline;
        }
        .logo_container {
            text-align: center;
            margin-top: 20px;
            margin:auto;
        }
        .table_header {
            border-bottom: 2px solid black;
            border-top: 2px solid black;
            font-weight: bold;
            font-family: 'AkagiPro-extrabold';
        }
    </style>
    <body style="width: 210mm; height: 297mm; margin: 0; padding: 0; font-family: 'AkagiPro-extrabold'">
        @foreach ($payroll_details as $payroll)
            <div style="page-break-before: always; height: 50%; width: 100%; overflow:hidden;">
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
                            <td class="payroll_label" style="color:rgb(48, 85, 80)"><span>Section / Proj: </span><span style="color:black;">{{ $payroll->employee->section->section_name ?? '' }}</span></td>
                        </tr>
                    </table>
                </div>
                <div style="display: flex;">
                    <table style="width: 55%; height:fit-content; table-layout: fixed">
                        <colgroup>
                            <col style="width: 33.33%;">
                            <col style="width: 33.33%;">
                            <col style="width: 33.33%;">
                        </colgroup>
                        <tr class="table_header">
                            <th style="text-align: center;">Earnings</th>
                            <th style="text-align: center;">Hours</th>
                            <th style="text-align: center;">Amount</th>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Basic Reg. hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_hours, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Basic Reg. OT hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_overtime, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_ot_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Rest day/sun hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->rest_hours, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->rest_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Rest day OT hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->rest_overtime, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->rest_ot_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Reg/Hol hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_holiday_hours, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_holiday_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Reg/Hol OT hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_holiday_overtime, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->regular_holiday_ot_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Spcl./Hol hrs. -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->special_holiday_hours, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->special_holiday_pay, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Adjustments -</td>
                            <td style="text-align: right; padding-right:10px;">-</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->total_adjustment, 2) }}</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                    </table>
                    <table style="width: 45%; height:fit-content; table-layout: fixed">
                        <colgroup>
                            <col style="width: 50%;">
                            <col style="width: 50%;">
                        </colgroup>
                        <tr class="table_header">
                            <th style="text-align: center;">Deduction:</th>
                            <th style="text-align: center;">Amount</th>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Withholding Tax -</td>
                            <td style="text-align: right; padding-right:10px;"></td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">Gross Pay:</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->gross_pay, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">SSS -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->sss_employee_contribution, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">PHIC -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->philhealth_employee_contribution, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">HMDF -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->pagibig_employee_contribution, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="payroll_label" style="text-align: right; padding-right:10px;">CASH ADVANCE -</td>
                            <td style="text-align: right; padding-right:10px;">{{ number_format($payroll->total_deduct, 2) }}</td>
                        </tr>
                        @foreach ($loans ?? [] as $loan)
                            <tr>
                                <td class="payroll_label" style="text-align: right; padding-right:10px;">Loan</td>
                                <td style="text-align: right; padding-right:10px;"></td>
                            </tr>
                        @endforeach
                        @foreach ($otherDeductions ?? [] as $otherDeduction)
                            <tr>
                                <td class="payroll_label" style="text-align: right; padding-right:10px;">otherDeduction</td>
                                <td style="text-align: right; padding-right:10px;"></td>
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
                    <div style="padding-right:10px;">
                        <p style="padding:0;">ROBERTO SEVILLA</p>
                        <p style="padding:0;">HR SPECIALIST / PAYROLL</p>
                    </div>
                </div>
            </div>
        @endforeach
    </body>
    {{-- <script>
        window.onload = function() {
            window.print();
        }
    </script> --}}
</html>
