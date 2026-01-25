<?php

namespace App\Services;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailSender;
use Brevo\Client\Model\SendSmtpEmailAttachment;
use GuzzleHttp\Client;

class BrevoMailService
{
    /**
     * Get Brevo API Instance
     */
    protected static function getApi()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        return new TransactionalEmailsApi(
            new Client(),
            $config
        );
    }

    /**
     * Send OTP using Brevo Transactional Email API (Static Helper)
     */
    public static function sendOtp($email, $otp)
    {
        return self::sendMail(
            $email,
            'Your OTP Code — SoloCart',
            "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #2874f0; padding: 20px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-style: italic;'>SoloCart</h1>
                </div>
                <div style='padding: 30px; line-height: 1.6;'>
                    <h3 style='color: #1e293b;'>Identity Verification</h3>
                    <p>To access your account, please enter the following One-Time Password (OTP):</p>
                    <div style='background-color: #f8fafc; padding: 20px; text-align: center; border-radius: 4px; margin: 20px 0;'>
                        <span style='font-size: 32px; font-weight: 900; letter-spacing: 5px; color: #2874f0;'>{$otp}</span>
                    </div>
                    <p style='font-size: 14px; color: #64748b;'>This code expires in 5 minutes. If you did not request this code, please ignore this email.</p>
                </div>
                <div style='background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #94a3b8;'>
                    &copy; 2026 SoloCart Industries • Secure Gateway
                </div>
            </div>
            "
        );
    }

    /**
     * Generic Send Mail Method with proper Model types for SDK compatibility
     */
    public static function sendMail($toEmail, $subject, $htmlContent, $toName = 'User', $attachment = null)
    {
        try {
            $api = self::getApi();

            $sendSmtpEmail = new SendSmtpEmail();
            $sendSmtpEmail->setSubject($subject);
            $sendSmtpEmail->setHtmlContent($htmlContent);
            
            $sender = new SendSmtpEmailSender();
            $sender->setEmail(env('MAIL_FROM_ADDRESS', 'noreply@solocart.com'));
            $sender->setName(env('MAIL_FROM_NAME', 'SoloCart'));
            $sendSmtpEmail->setSender($sender);

            $receiver = new SendSmtpEmailTo();
            $receiver->setEmail($toEmail);
            $receiver->setName($toName);
            $sendSmtpEmail->setTo([$receiver]);

            if ($attachment) {
                // attachment should be ['content' => base64, 'name' => 'filename.pdf']
                $brevoAttachment = new SendSmtpEmailAttachment();
                $brevoAttachment->setContent($attachment['content']);
                $brevoAttachment->setName($attachment['name']);
                $sendSmtpEmail->setAttachment([$brevoAttachment]);
            }

            return $api->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            \Log::error('Brevo API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
