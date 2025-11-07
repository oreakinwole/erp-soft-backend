<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollConfigController extends Controller
{
    /**
     * Return the list of allowance types from config.
     */
    public function allowanceTypes(Request $request)
    {
        $types = config('payroll.allowance_types', []);

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }
}