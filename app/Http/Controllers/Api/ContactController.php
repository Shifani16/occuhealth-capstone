<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactFormMail; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function sendContactForm(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::to('iniguebukanelo@gmail.com') 
                ->send(new ContactFormMail($validatedData)); 

            Log::info('Contact form email sent successfully', ['email' => $validatedData['email']]);


            return response()->json([
                'message' => 'Pesan Anda berhasil dikirim!',
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to send contact form email', [
                'email' => $validatedData['email'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Gagal mengirim pesan. Mohon coba lagi nanti.',
                'status' => 'error',
                
            ], 500);
        }
    }
}
