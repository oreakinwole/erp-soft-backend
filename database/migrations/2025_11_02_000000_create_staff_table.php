<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('national_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('staff_id')->nullable();
            $table->string('role')->nullable();
            $table->string('department')->nullable();
            $table->string('staff_category')->nullable();
            $table->date('employment_date')->nullable();
            $table->decimal('basic_salary', 12, 2)->nullable();
            $table->string('allowances')->nullable();
            $table->decimal('housing_allowance', 12, 2)->nullable();
            $table->decimal('transport_allowance', 12, 2)->nullable();
            $table->string('tin')->nullable();
            $table->string('pension_provider')->nullable();
            $table->string('rsa_pin')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('offer_letter_path')->nullable();
            $table->string('passport_path')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('id_document_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};