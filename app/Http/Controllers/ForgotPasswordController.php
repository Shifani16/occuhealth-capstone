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

        // --- Logging before generation ---
        Log::info('--- Signed URL Generation Context ---');
        Log::info('APP_URL config: ' . config('app.url')); 
        Log::info('APP_KEY config (Generation): ' . (config('app.key') ? 'Set' : 'Not Set')); 
        Log::info('Request->isSecure() at generation: ' . ($request->isSecure() ? 'true' : 'false')); 
        Log::info('Request->root() at generation: ' . $request->root()); 
        Log::info('---------------------------------');

        // --- Modify the URL generation call ---
        $resetUrl = URL::temporarySignedRoute(
            'password.reset', 
            Carbon::now()->addMinutes(30),
            ['user' => $user->id],
            true,
            ['_', '_rd_', 'utm_source', 'utm_medium', 'utm_campaign']
        );

        Log::info('Generated URL (from Laravel, including potential ignored params): ' . $resetUrl);
        Log::info('--- End Generation Context ---');
        // --- End Modification ---


        // You might want to send an Mailable class instead of raw text for better formatting
        Mail::raw("Klik tautan berikut untuk reset password: $resetUrl", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Password Link');
        });

        return response()->json([
            'message' => 'Link verifikasi telah dikirim ke email Anda.'
        ]);
    }

    // This method receives the signed request
    public function verifyLink(Request $request)
    {
         Log::info('--- Signed URL Verification Context ---');
         Log::info('Route "password.reset" (verifyLink) reached.');
         Log::info('APP_KEY config (Verification): ' . (config('app.key') ? 'Set' : 'Not Set')); 
         Log::info('Request->isSecure() at verification: ' . ($request->isSecure() ? 'true' : 'false')); 
         Log::info('Request->root() at verification: ' . $request->root()); 

       
         Log::info('Incoming Query Params on verified link: ' . json_encode($request->query->all()));
         Log::info('Incoming Full URL on verified link: ' . $request->fullUrl());
         Log::info('------------------------------------');
         // --- End Optional Logging ---


        $userId = $request->route('user');

        return redirect('/forgotpass?user=' . $userId);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|min:8|confirmed', 
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
             return response()->json(['message' => 'User not found.'], 404);
        }

        $user->password = bcrypt($request->password); 
        $user->save();

        return response()->json([
            'message' => 'Password berhasil direset. Silakan login.'
        ]);
    }
}