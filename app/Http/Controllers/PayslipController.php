<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index()
    {
        // $pdf = Pdf::loadView('reports.docs.payslip');
        // return $pdf->stream();
        return view('reports.docs.payslip');
    }
}
