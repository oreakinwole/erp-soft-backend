<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * List all payroll records with staff information
     */
    public function index(Request $request)
    {
        $query = Payroll::with('staff');

        // Filter by period
        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('period', 'like', '%' . $request->year . '%');
        }

        $payrolls = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $payrolls->map(function ($payroll) {
                return [
                    'id' => $payroll->id,
                    'staff_id' => $payroll->staff_id,
                    'staff_name' => $payroll->staff->first_name . ' ' . $payroll->staff->last_name,
                    'staff_number' => $payroll->staff->staff_id ?? $payroll->staff_id,
                    'period' => $payroll->period,
                    'gross_pay' => $payroll->gross_pay,
                    'deductions' => $payroll->deductions,
                    'bonus' => $payroll->bonus,
                    'net_pay' => $payroll->net_pay,
                    'status' => $payroll->status,
                    'processed_at' => $payroll->processed_at,
                    'paid_at' => $payroll->paid_at,
                    'created_at' => $payroll->created_at,
                ];
            }),
        ]);
    }

    /**
     * Get a single payroll record
     */
    public function show($id)
    {
        $payroll = Payroll::with('staff')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payroll->id,
                'staff_id' => $payroll->staff_id,
                'staff' => $payroll->staff,
                'period' => $payroll->period,
                'gross_pay' => $payroll->gross_pay,
                'deductions' => $payroll->deductions,
                'bonus' => $payroll->bonus,
                'net_pay' => $payroll->net_pay,
                'status' => $payroll->status,
                'processed_at' => $payroll->processed_at,
                'paid_at' => $payroll->paid_at,
                'notes' => $payroll->notes,
                'created_at' => $payroll->created_at,
            ],
        ]);
    }

    /**
     * Generate payroll for all staff for a specific period
     */
    public function generate(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:staff,id',
        ]);

        $period = $request->period;
        $staffIds = $request->staff_ids ?? Staff::pluck('id')->toArray();

        $payrolls = [];

        foreach ($staffIds as $staffId) {
            $staff = Staff::find($staffId);
            if (!$staff) continue;

            // Check if payroll already exists for this period
            $existing = Payroll::where('staff_id', $staffId)
                ->where('period', $period)
                ->first();

            if ($existing) continue;

            // Calculate payroll
            $grossPay = floatval($staff->basic_salary ?? 0);
            $housingAllowance = floatval($staff->housing_allowance ?? 0);
            $transportAllowance = floatval($staff->transport_allowance ?? 0);
            $medicalAllowance = floatval($staff->medical_allowance ?? 0);

            $totalGross = $grossPay + $housingAllowance + $transportAllowance + $medicalAllowance;

            // Calculate deductions (simple tax calculation - 10% for demo)
            $deductions = $totalGross * 0.10;

            // Calculate net pay
            $netPay = $totalGross - $deductions;

            $payroll = Payroll::create([
                'staff_id' => $staffId,
                'period' => $period,
                'gross_pay' => $totalGross,
                'deductions' => $deductions,
                'bonus' => 0,
                'net_pay' => $netPay,
                'status' => 'pending',
            ]);

            $payrolls[] = $payroll->load('staff');
        }

        return response()->json([
            'success' => true,
            'message' => 'Payroll generated successfully',
            'data' => $payrolls,
        ]);
    }

    /**
     * Process/run payroll (mark as processed)
     */
    public function process($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Payroll has already been processed',
            ], 400);
        }

        $payroll->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payroll processed successfully',
            'data' => $payroll->load('staff'),
        ]);
    }

    /**
     * Mark payroll as paid
     */
    public function markPaid($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payroll has already been marked as paid',
            ], 400);
        }

        $payroll->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payroll marked as paid successfully',
            'data' => $payroll->load('staff'),
        ]);
    }

    /**
     * Get payroll summary statistics
     */
    public function summary(Request $request)
    {
        $query = Payroll::query();

        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        if ($request->has('year')) {
            $query->where('period', 'like', '%' . $request->year . '%');
        }

        $totalPayroll = $query->sum('net_pay');
        $pendingPayments = $query->where('status', 'pending')->sum('net_pay');
        $totalPaid = $query->where('status', 'paid')->sum('net_pay');
        $lastPayrollRun = Payroll::orderBy('created_at', 'desc')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_payroll_cost' => $totalPayroll,
                'pending_payments' => $pendingPayments,
                'total_paid' => $totalPaid,
                'last_payroll_run' => $lastPayrollRun ? $lastPayrollRun->created_at->format('M d, Y') : null,
            ],
        ]);
    }

    /**
     * Aggregate payroll runs by period (month/year) across all staff
     */
    public function runs(Request $request)
    {
        $query = Payroll::query();

        // Optional filters
        if ($request->has('year')) {
            $query->where('period', 'like', '%' . $request->year . '%');
        }

        if ($request->has('period')) {
            $query->where('period', $request->period);
        }

        // Group by period and aggregate totals
        $runs = $query
            ->select([
                'period',
                DB::raw('SUM(gross_pay) as gross_pay'),
                DB::raw('SUM(deductions) as deductions'),
                DB::raw('SUM(bonus) as bonus'),
                DB::raw('SUM(net_pay) as net_pay'),
                DB::raw('MAX(created_at) as last_created_at'),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed_count"),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count"),
                DB::raw('COUNT(*) as record_count'),
            ])
            ->groupBy('period')
            ->orderBy(DB::raw('MAX(created_at)'), 'desc')
            ->get();

        $data = $runs->map(function ($run) {
            // Determine overall status for the run
            $status = 'pending';
            if (intval($run->pending_count) === 0 && intval($run->processed_count) > 0 && intval($run->paid_count) === 0) {
                $status = 'processed';
            }
            if (intval($run->paid_count) === intval($run->record_count) && intval($run->record_count) > 0) {
                $status = 'paid';
            }

            return [
                'date' => $run->last_created_at ? (new \Carbon\Carbon($run->last_created_at))->format('M d, Y') : null,
                'month' => $run->period,
                'gross_pay' => (float) $run->gross_pay,
                'deductions' => (float) $run->deductions,
                'net_pay' => (float) $run->net_pay,
                'bonus' => (float) $run->bonus,
                'status' => $status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update payroll record
     */
    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        $request->validate([
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = [];

        if ($request->has('bonus')) {
            $data['bonus'] = $request->bonus;
        }

        if ($request->has('deductions')) {
            $data['deductions'] = $request->deductions;
        }

        if ($request->has('notes')) {
            $data['notes'] = $request->notes;
        }

        // Recalculate net pay if bonus or deductions changed
        if (isset($data['bonus']) || isset($data['deductions'])) {
            $bonus = $data['bonus'] ?? $payroll->bonus;
            $deductions = $data['deductions'] ?? $payroll->deductions;
            $data['net_pay'] = $payroll->gross_pay + $bonus - $deductions;
        }

        $payroll->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Payroll updated successfully',
            'data' => $payroll->load('staff'),
        ]);
    }

    /**
     * Delete payroll record
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete paid payroll',
            ], 400);
        }

        $payroll->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payroll deleted successfully',
        ]);
    }
}
