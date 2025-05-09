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

       
        Log::info('--- Signed URL Generation Start ---');
        Log::info('APP_URL config: ' . config('app.url'));
        Log::info('APP_KEY config (Generation): ' . config('app.key')); 
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
