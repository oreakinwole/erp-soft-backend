<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'marital_status',
        'date_of_birth',
        'nationality',
        'national_id',
        'phone_number',
        'email',
        'state_of_origin',
        'lga',
        'contact_address',
        'staff_id',
        'role',
        'department',
        'staff_category',
        'employment_type',
        'employment_date',
        'basic_salary',
        'allowances',
        'housing_allowance',
        'transport_allowance',
        'medical_allowance',
        'tin',
        'pension_provider',
        'rsa_pin',
        'insurance_provider',
        'insurance_policy_number',
        'bank_name',
        'account_number',
        'account_name',
        'offer_letter_path',
        'passport_path',
        'resume_path',
        'id_document_path',
    ];
}