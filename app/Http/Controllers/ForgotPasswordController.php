<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // *** Logging APP_KEY and URL before generation ***
        Log::info('--- Signed URL Generation Start ---');
        Log::info('APP_URL config: ' . config('app.url')); // Check base URL used
        Log::info('APP_KEY config (Generation): ' . config('app.key')); // Check key used for signing
        Log::info('---------------------------------');

        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(30),
            ['user' => $user->id]
        );

        // *** Logging generated URL ***
        Log::info('Generated URL: ' . $resetUrl);
        Log::info('---------------------------------');

        Mail::raw("Klik tautan berikut untuk reset password: $resetUrl", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Password Link');
        });

        return response()->json([
            'message' => 'Link verifikasi telah dikirim ke email Anda.'
        ]);
    }

    public function verifyLink(Request $request)
    {
        // *** Logging everything BEFORE validation ***
        Log::info('--- Signed URL Verification Start ---');
        Log::info('APP_URL config (Verification): ' . config('app.url')); // Check base URL during verification (less critical here)
        Log::info('APP_KEY config (Verification): ' . config('app.key')); // *** THIS IS THE KEY USED FOR VERIFICATION ***
        Log::info('Incoming Full URL: ' . $request->fullUrl()); // Exact URL received by Laravel
        Log::info('Incoming Scheme: ' . $request->getScheme()); // Should be https
        Log::info('Incoming Host: ' . $request->getHost()); // Should be your domain
        Log::info('Incoming Path: ' . $request->path()); // Should be verify-reset/{user_id}
        Log::info('Incoming Query String: ' . $request->getQueryString()); // Includes expires, signature, user
        Log::info('Signature received: ' . $request->query('signature'));
        Log::info('Expires received: ' . $request->query('expires'));
        Log::info('User ID received: ' . $request->route('user')); // Get user from route parameter

        $isValid = $request->hasValidSignature();
        Log::info('Result of hasValidSignature(): ' . ($isValid ? 'true' : 'false'));
        Log::info('-------------------------------------');

        if (! $isValid) { // Use the variable
            abort(403, 'Link tidak valid atau sudah kedaluwarsa.');
        }

        return redirect('/forgotpass?user=' . $request->user);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|min:8',
        ]);

        $user = User::find($request->user_id);
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil direset. Silakan login.'
        ]);
    }
}
