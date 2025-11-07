<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    /**
     * Login with email and password, return Sanctum token.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wrong Password/Email',
                'data' => []
            ], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ]);
    }

    /**
     * Send a 6-digit code to user's email and store hashed token.
     */
    public function forgot(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            // Avoid leaking user existence
            return response()->json(['status' => 'success', 'message' => 'If the email exists, a code has been sent.']);
        }

        $code = (string) random_int(100000, 999999);
        $hashed = Hash::make($code);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            ['token' => $hashed, 'created_at' => now()]
        );

        // Simple mail; default mailer is 'log' so this will be logged in storage
        Mail::raw("Your ERP Hayok verification code is: {$code}", function ($message) use ($validated) {
            $message->to($validated['email'])->subject('Password Reset Code');
        });

        // For local/dev, return the code to ease testing
        return response()->json([
            'status' => 'success',
            'message' => 'Verification code sent',
            'data' => [
                'dev_code' => $code,
            ],
        ]);
    }

    /**
     * Verify 6-digit code for given email.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();
        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Invalid code or email'], 422);
        }

        // Expire after 60 minutes
        $expired = isset($record->created_at) && now()->diffInMinutes($record->created_at) > 60;
        if ($expired || !Hash::check($validated['code'], $record->token)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired code'], 422);
        }

        return response()->json(['status' => 'success', 'message' => 'Code verified']);
    }

    /**
     * Reset password using email + verified code.
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();
        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Invalid code or email'], 422);
        }
        $expired = isset($record->created_at) && now()->diffInMinutes($record->created_at) > 60;
        if ($expired || !Hash::check($validated['code'], $record->token)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired code'], 422);
        }

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return response()->json(['status' => 'success', 'message' => 'Password reset successful']);
    }
}