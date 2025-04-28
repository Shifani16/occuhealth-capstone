<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;

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

        Mail::raw("Klik tautan berikut untuk reset password: $resetUrl", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Reset Password Link');
        });

        return response()->json([
            'message' => 'Link verifikasi telah dikirim ke email Anda.'
        ]);
    }

    public function verifyLink()
    {
        return redirect('/forgotpass'); 
    }
}
