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
     * Send Password Reset Link
     */
    public static function sendPasswordResetLink($email, $link)
    {
        return self::sendMail(
            $email,
            'Reset Your Password – SoloCart',
            "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #ef4444; padding: 20px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-style: italic;'>SoloCart</h1>
                </div>
                <div style='padding: 30px; line-height: 1.6;'>
                    <h3 style='color: #1e293b;'>Reset Your Password</h3>
                    <p>Hello,</p>
                    <p>You requested to reset your password.</p>
                    <p>Click the button below to create a new password. If you did not request this, please ignore this email.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$link}' style='background-color: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Reset Password</a>
                    </div>

                    <p style='font-size: 14px; color: #64748b;'>This link will expire in 15 minutes.</p>
                    <p>Thank you,<br>SoloCart Team</p>
                </div>
                <div style='background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #94a3b8;'>
                    &copy; 2026 SoloCart Industries • Security Team
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
    /**
     * Send Premium Order Confirmation Email
     */
    public static function sendOrderConfirmation($user, $order)
    {
        $itemsHtml = '';
        foreach ($order->items as $item) {
            $price = number_format($item->price, 2);
            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; color: #334155;'>{$item->product->name} (x{$item->quantity})</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right; color: #334155; font-weight: bold;'>₹{$price}</td>
                </tr>
            ";
        }

        $total = number_format($order->total, 2);
        
        return self::sendMail(
            $user->email,
            "Order Confirmed — #{$order->order_number}",
            "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background-color: #ffffff;'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); padding: 30px; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 24px; letter-spacing: 1px; font-weight: 800; font-style: italic;'>SoloCart</h1>
                    <p style='color: #e0e7ff; margin: 5px 0 0; font-size: 14px;'>Premium Lifestyle Essentials</p>
                </div>
                
                <!-- Body -->
                <div style='padding: 40px 30px;'>
                    <h2 style='color: #1e293b; margin-top: 0; font-size: 20px;'>Order Confirmed!</h2>
                    <p style='color: #64748b; line-height: 1.6;'>Hi {$user->name},</p>
                    <p style='color: #64748b; line-height: 1.6;'>Thank you for your purchase. We've received your order and are getting it ready for shipment.</p>
                    
                    <div style='margin: 30px 0; background-color: #f8fafc; border-radius: 8px; padding: 20px;'>
                        <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                            <thead>
                                <tr>
                                    <th style='text-align: left; color: #94a3b8; font-weight: 600; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;'>Item</th>
                                    <th style='text-align: right; color: #94a3b8; font-weight: 600; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;'>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$itemsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style='padding-top: 15px; font-weight: 700; color: #1e293b;'>Total Amount</td>
                                    <td style='padding-top: 15px; text-align: right; font-weight: 900; color: #4f46e5; font-size: 16px;'>₹{$total}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div style='text-align: center; margin-top: 30px;'>
                         <a href='" . env('FRONTEND_URL', 'https://solocart.onrender.com') . "/order-details.html?id={$order->id}' style='background-color: #0f172a; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;'>Track Your Order</a>
                    </div>
                </div>

                <!-- Footer -->
                <div style='background-color: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;'>
                    <p style='margin: 0; font-size: 12px; color: #94a3b8;'>&copy; 2026 SoloCart Industries. All rights reserved.</p>
                </div>
            </div>
            ",
            $user->name
        );
    }

    /**
     * Send Order Status Update Email
     */
    public static function sendOrderStatusUpdate($user, $order)
    {
        $status = ucfirst(str_replace('_', ' ', $order->status));
        $color = match($order->status) {
            'shipped' => '#3b82f6', // blue
            'delivered' => '#10b981', // green
            'cancelled' => '#ef4444', // red
            default => '#f59e0b' // orange
        };

        return self::sendMail(
            $user->email,
            "Order Status Updated: {$status} — #{$order->order_number}",
            "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background-color: #ffffff;'>
                 <div style='background-color: #0f172a; padding: 25px; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 22px; font-style: italic;'>SoloCart</h1>
                </div>

                <div style='padding: 40px 30px; text-align: center;'>
                    <div style='width: 60px; height: 60px; background-color: {$color}15; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;'>
                        <div style='width: 15px; height: 15px; background-color: {$color}; border-radius: 50%;'></div>
                    </div>
                    
                    <h2 style='color: #1e293b; margin: 0 0 10px;'>Status Update</h2>
                    <p style='color: #64748b; font-size: 16px; margin: 0 0 30px;'>Your order <strong>#{$order->order_number}</strong> is now <strong style='color: {$color}; text-transform: uppercase;'>{$status}</strong>.</p>
                    
                    <a href='" . env('FRONTEND_URL', 'https://solocart.onrender.com') . "/order-details.html?id={$order->id}' style='background-color: border: 1px solid #e2e8f0; color: #475569; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; border: 2px solid #e2e8f0; display: inline-block;'>View Order Details</a>
                </div>

                <div style='background-color: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;'>
                     Questions? <a href='mailto:support@solocart.com' style='color: #4f46e5; text-decoration: none;'>Contact Support</a>
                </div>
            </div>
            ",
            $user->name
        );
    }

    /**
     * Send Order Delivered Email with Invoice
     */
    public static function sendOrderDelivered($user, $order, $pdfContent)
    {
        return self::sendMail(
            $user->email,
            "Order Delivered & Invoice — #{$order->order_number}",
            "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background-color: #ffffff;'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 24px; font-weight: 800; font-style: italic;'>SoloCart</h1>
                    <p style='color: #d1fae5; margin: 5px 0 0; font-size: 14px;'>Delivered Successfully</p>
                </div>
                
                <!-- Body -->
                <div style='padding: 40px 30px; text-align: center;'>
                    <img src='https://cdn-icons-png.flaticon.com/512/6459/6459980.png' style='width: 80px; margin-bottom: 20px;' alt='Package'>
                    
                    <h2 style='color: #1e293b; margin-top: 0;'>Arrived Safely!</h2>
                    <p style='color: #64748b; line-height: 1.6;'>Hi {$user->name},</p>
                    <p style='color: #64748b; line-height: 1.6;'>Your order <strong>#{$order->order_number}</strong> has been delivered. We hope you enjoy your purchase!</p>
                    
                    <div style='margin: 30px 0; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px;'>
                        <p style='margin: 0; color: #166534; font-size: 14px; font-weight: 600;'>📄 Invoice Attached</p>
                        <p style='margin: 5px 0 0; color: #15803d; font-size: 12px;'>A copy of your invoice is attached to this email for your records.</p>
                    </div>

                    <a href='" . env('FRONTEND_URL', 'https://solocart.onrender.com') . "/order-details.html?id={$order->id}' style='background-color: #10b981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 30px; font-weight: bold; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);'>Rate Your Experience</a>
                </div>

                <!-- Footer -->
                <div style='background-color: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;'>
                    <p style='margin: 0; font-size: 12px; color: #94a3b8;'>&copy; 2026 SoloCart Industries.</p>
                </div>
            </div>
            ",
            $user->name,
            [
                'content' => $pdfContent,
                'name' => "Invoice-{$order->order_number}.pdf"
            ]
        );
    }
}
