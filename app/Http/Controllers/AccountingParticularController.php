<?php

namespace App\Http\Controllers;

use App\Models\SigmaServices\AccountingParticular;
use App\Http\Requests\StoreAccountingParticularRequest;
use App\Http\Requests\UpdateAccountingParticularRequest;

class AccountingParticularController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => AccountingParticular::paginate(),
            'success' => true,
            'message' => 'Successfully fetched data.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountingParticularRequest $request)
    {
        $validatedData = $request->validated();
        if (AccountingParticular::create($validatedData)) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully Created.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountingParticular $payroll_particular_term)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountingParticularRequest $request, AccountingParticular $payroll_particular_term)
    {
        $validatedData = $request->validated();
        $payroll_particular_term->fill($validatedData);
        if ($payroll_particular_term->save()) {
            return response()->json([
                'data' => $payroll_particular_term,
                'success' => true,
                'message' => 'Successfully update.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountingParticular $payroll_particular_term)
    {
        $payroll_particular_term->delete();
        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted.'
        ]);
    }
}
