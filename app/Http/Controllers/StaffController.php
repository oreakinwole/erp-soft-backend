<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    /**
     * List all staff.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Staff::orderBy('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Show a single staff by ID.
     */
    public function show($id)
    {
        $staff = Staff::findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $staff]);
    }

    /**
     * Store a newly created staff in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:50',
            'maritalStatus' => 'nullable|string|max:50',
            'dateOfBirth' => 'nullable|string',
            'nationality' => 'nullable|string|max:255',
            'nationalId' => 'nullable|string|max:255',
            'phoneNumber' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'stateOfOrigin' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'contactAddress' => 'nullable|string|max:1024',
            'staffId' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'staffCategory' => 'nullable|string|max:255',
            'employmentType' => 'nullable|string|max:255',
            'employmentDate' => 'nullable|string',
            'basicSalary' => 'nullable|numeric',
            'allowances' => 'nullable|string|max:1024',
            'housingAllowance' => 'nullable|numeric',
            'transportAllowance' => 'nullable|numeric',
            'medicalAllowance' => 'nullable|numeric',
            'tin' => 'nullable|string|max:255',
            'pensionProvider' => 'nullable|string|max:255',
            'rsaPin' => 'nullable|string|max:255',
            'insuranceProvider' => 'nullable|string|max:255',
            'insurancePolicyNumber' => 'nullable|string|max:255',
            'bankName' => 'nullable|string|max:255',
            'accountNumber' => 'nullable|string|max:255',
            'accountName' => 'nullable|string|max:255',
            // Files
            'offerLetter' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png',
            'resume' => 'nullable|file|mimes:pdf,doc,docx',
            'id' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        $staff = new Staff();
        $staff->first_name = $validated['firstName'];
        $staff->last_name = $validated['lastName'];
        $staff->middle_name = $validated['middleName'] ?? null;
        $staff->gender = $validated['gender'] ?? null;
        $staff->marital_status = $validated['maritalStatus'] ?? null;
        $staff->date_of_birth = self::parseDate($validated['dateOfBirth'] ?? null);
        $staff->nationality = $validated['nationality'] ?? null;
        $staff->national_id = $validated['nationalId'] ?? null;
        $staff->phone_number = $validated['phoneNumber'] ?? null;
        $staff->email = $validated['email'] ?? null;
        $staff->state_of_origin = $validated['stateOfOrigin'] ?? null;
        $staff->lga = $validated['lga'] ?? null;
        $staff->contact_address = $validated['contactAddress'] ?? null;
        $staff->staff_id = $validated['staffId'] ?? null;
        $staff->role = $validated['role'] ?? null;
        $staff->department = $validated['department'] ?? null;
        $staff->staff_category = $validated['staffCategory'] ?? null;
        $staff->employment_type = $validated['employmentType'] ?? null;
        $staff->employment_date = self::parseDate($validated['employmentDate'] ?? null);
        $staff->basic_salary = $validated['basicSalary'] ?? null;
        $staff->allowances = $validated['allowances'] ?? null;
        $staff->housing_allowance = $validated['housingAllowance'] ?? null;
        $staff->transport_allowance = $validated['transportAllowance'] ?? null;
        $staff->medical_allowance = $validated['medicalAllowance'] ?? null;
        $staff->tin = $validated['tin'] ?? null;
        $staff->pension_provider = $validated['pensionProvider'] ?? null;
        $staff->rsa_pin = $validated['rsaPin'] ?? null;
        $staff->insurance_provider = $validated['insuranceProvider'] ?? null;
        $staff->insurance_policy_number = $validated['insurancePolicyNumber'] ?? null;
        $staff->bank_name = $validated['bankName'] ?? null;
        $staff->account_number = $validated['accountNumber'] ?? null;
        $staff->account_name = $validated['accountName'] ?? null;
        $staff->save();

        // Handle file uploads, store under public disk by staff id
        $basePath = 'public/staff_docs/' . $staff->id;

        if ($request->hasFile('offerLetter')) {
            $path = $request->file('offerLetter')->store($basePath);
            $staff->offer_letter_path = Storage::url($path);
        }
        if ($request->hasFile('passport')) {
            $path = $request->file('passport')->store($basePath);
            $staff->passport_path = Storage::url($path);
        }
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store($basePath);
            $staff->resume_path = Storage::url($path);
        }
        if ($request->hasFile('id')) {
            $path = $request->file('id')->store($basePath);
            $staff->id_document_path = Storage::url($path);
        }

        $staff->save();

        return response()->json([
            'status' => 'success',
            'data' => $staff,
        ], 201);
    }

    /**
     * Update an existing staff record.
     */
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'firstName' => 'nullable|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:50',
            'maritalStatus' => 'nullable|string|max:50',
            'dateOfBirth' => 'nullable|string',
            'nationality' => 'nullable|string|max:255',
            'nationalId' => 'nullable|string|max:255',
            'phoneNumber' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'stateOfOrigin' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'contactAddress' => 'nullable|string|max:1024',
            'staffId' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'staffCategory' => 'nullable|string|max:255',
            'employmentType' => 'nullable|string|max:255',
            'employmentDate' => 'nullable|string',
            'basicSalary' => 'nullable|numeric',
            'allowances' => 'nullable|string|max:1024',
            'housingAllowance' => 'nullable|numeric',
            'transportAllowance' => 'nullable|numeric',
            'medicalAllowance' => 'nullable|numeric',
            'tin' => 'nullable|string|max:255',
            'pensionProvider' => 'nullable|string|max:255',
            'rsaPin' => 'nullable|string|max:255',
            'insuranceProvider' => 'nullable|string|max:255',
            'insurancePolicyNumber' => 'nullable|string|max:255',
            'bankName' => 'nullable|string|max:255',
            'accountNumber' => 'nullable|string|max:255',
            'accountName' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            switch ($key) {
                case 'firstName': $staff->first_name = $value; break;
                case 'lastName': $staff->last_name = $value; break;
                case 'middleName': $staff->middle_name = $value; break;
                case 'gender': $staff->gender = $value; break;
                case 'maritalStatus': $staff->marital_status = $value; break;
                case 'dateOfBirth': $staff->date_of_birth = self::parseDate($value); break;
                case 'nationality': $staff->nationality = $value; break;
                case 'nationalId': $staff->national_id = $value; break;
                case 'phoneNumber': $staff->phone_number = $value; break;
                case 'email': $staff->email = $value; break;
                case 'stateOfOrigin': $staff->state_of_origin = $value; break;
                case 'lga': $staff->lga = $value; break;
                case 'contactAddress': $staff->contact_address = $value; break;
                case 'staffId': $staff->staff_id = $value; break;
                case 'role': $staff->role = $value; break;
                case 'department': $staff->department = $value; break;
                case 'staffCategory': $staff->staff_category = $value; break;
                case 'employmentType': $staff->employment_type = $value; break;
                case 'employmentDate': $staff->employment_date = self::parseDate($value); break;
                case 'basicSalary': $staff->basic_salary = $value; break;
                case 'allowances': $staff->allowances = $value; break;
                case 'housingAllowance': $staff->housing_allowance = $value; break;
                case 'transportAllowance': $staff->transport_allowance = $value; break;
                case 'medicalAllowance': $staff->medical_allowance = $value; break;
                case 'tin': $staff->tin = $value; break;
                case 'pensionProvider': $staff->pension_provider = $value; break;
                case 'rsaPin': $staff->rsa_pin = $value; break;
                case 'insuranceProvider': $staff->insurance_provider = $value; break;
                case 'insurancePolicyNumber': $staff->insurance_policy_number = $value; break;
                case 'bankName': $staff->bank_name = $value; break;
                case 'accountNumber': $staff->account_number = $value; break;
                case 'accountName': $staff->account_name = $value; break;
            }
        }

        $staff->save();

        return response()->json(['status' => 'success', 'data' => $staff]);
    }

    private static function parseDate($value)
    {
        if (!$value) return null;
        // Accept YYYY-MM-DD or DD/MM/YYYY
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $value, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }
        return null;
    }
}