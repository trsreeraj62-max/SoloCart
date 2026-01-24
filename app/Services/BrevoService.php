<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiKey;
    protected $senderEmail;
    protected $senderName;

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY');
        $this->senderEmail = env('MAIL_FROM_ADDRESS', 'no-reply@solocart.com');
        $this->senderName = env('MAIL_FROM_NAME', 'SoloCart');
    }

    /**
     * Send OTP via Brevo HTTP API
     *
     * @param string $toEmail
     * @param string $otp
     * @param string|null $toName
     * @return bool
     */
    public function sendOtp($toEmail, $otp, $toName = null)
    {
        if (empty($this->apiKey)) {
            Log::error('Brevo API Key is missing.');
            return false;
        }

        $url = 'https://api.brevo.com/v3/smtp/email';
        
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(10)->post($url, [
                'sender' => [
                    'name' => $this->senderName,
                    'email' => $this->senderEmail
                ],
                'to' => [
                    [
                        'email' => $toEmail,
                        'name' => $toName ?? 'User'
                    ]
                ],
                'subject' => 'Your Verification Code',
                'htmlContent' => $this->getOtpHtml($otp),
                'textContent' => "Your Verification Code is: {$otp}. It expires in 10 minutes."
            ]);

            if ($response->successful()) {
                Log::info("Brevo API: Email sent to {$toEmail}");
                return true;
            }

            Log::error('Brevo API Failed: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Brevo Service Exception: ' . $e->getMessage());
            return false;
        }
    }

    protected function getOtpHtml($otp)
    {
        return "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2>Verification Required</h2>
                <p>Please use the following code to verify your identity:</p>
                <div style='background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; margin: 20px 0;'>
                    {$otp}
                </div>
                <p>This code will expire in 10 minutes.</p>
                <hr>
                <p style='font-size: 12px; color: #777;'>If you did not request this, please ignore this email.</p>
            </div>
        ";
    }
}
