<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class BrevoMailService
{
    public static function sendOtp(string $email, string $otp): void
    {
        $response = Http::withHeaders([
            'api-key' => config('services.brevo.key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => config('mail.from.address'),
                'name'  => config('mail.from.name'),
            ],
            'to' => [
                ['email' => $email],
            ],
            'subject' => 'Your Verification Code',
            'htmlContent' => "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2>Verification Required</h2>
                    <p>Please use the following code to verify your identity:</p>
                    <div style='background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; margin: 20px 0;'>
                        {$otp}
                    </div>
                    <p>This code will expire in 5 minutes.</p>
                    <hr>
                    <p style='font-size: 12px; color: #777;'>If you did not request this, please ignore this email.</p>
                </div>
            ",
        ]);

        if (!$response->successful()) {
            // Log for internal debugging, but throw generic exception
            \Illuminate\Support\Facades\Log::error('Brevo API Failed: ' . $response->body());
            throw new Exception('Brevo email sending failed');
        }
    }
}
