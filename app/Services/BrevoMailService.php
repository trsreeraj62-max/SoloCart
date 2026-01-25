<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BrevoMailService
{
    /**
     * Send OTP verification email via Brevo API (HTTP-based, bypasses SMTP)
     * 
     * @param string $email Recipient email address
     * @param string $otp 6-digit verification code
     * @throws Exception if email sending fails
     */
    public static function sendOtp(string $email, string $otp): void
    {
        // Validate API key exists
        $apiKey = config('services.brevo.key');
        if (empty($apiKey)) {
            Log::error('Brevo API Key Missing', [
                'config_check' => 'BREVO_API_KEY not set in environment'
            ]);
            throw new Exception('Email service not configured. Please contact support.');
        }

        // Validate sender configuration
        $senderEmail = config('mail.from.address');
        $senderName = config('mail.from.name', 'SoloCart');
        
        if (empty($senderEmail)) {
            Log::error('Mail From Address Missing');
            throw new Exception('Email service configuration incomplete.');
        }

        Log::info('Attempting to send OTP via Brevo', [
            'to' => $email,
            'sender' => $senderEmail,
            'otp_length' => strlen($otp)
        ]);

        try {
            $response = Http::timeout(10)->withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'email' => $senderEmail,
                    'name'  => $senderName,
                ],
                'to' => [
                    ['email' => $email],
                ],
                'subject' => 'Your SoloCart Verification Code',
                'htmlContent' => view('emails.otp', ['otp' => $otp])->render(),
            ]);

            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = $response->body();
                
                Log::error('Brevo API Request Failed', [
                    'status_code' => $statusCode,
                    'response_body' => $errorBody,
                    'recipient' => $email
                ]);

                // Check for specific Brevo error codes
                if ($statusCode === 401) {
                    throw new Exception('Email service authentication failed. Invalid API key.');
                } elseif ($statusCode === 400) {
                    throw new Exception('Invalid email address or request format.');
                } else {
                    throw new Exception('Email service temporarily unavailable. Please try again.');
                }
            }

            Log::info('OTP Email Sent Successfully', [
                'to' => $email,
                'message_id' => $response->json('messageId') ?? 'N/A'
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Brevo API Connection Timeout', [
                'error' => $e->getMessage(),
                'recipient' => $email
            ]);
            throw new Exception('Email service connection failed. Please try again later.');
        } catch (Exception $e) {
            // Re-throw if already handled
            if (str_contains($e->getMessage(), 'Email service')) {
                throw $e;
            }
            
            // Log unexpected errors
            Log::error('Unexpected Brevo Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Failed to send verification email. Please try again.');
        }
    }
}
