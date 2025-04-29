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

        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(30),
            ['user' => $user->id]
        );
    
        Log::info('Generated password reset link: ' . $resetUrl);

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
        if (! $request->hasValidSignature()) {
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
