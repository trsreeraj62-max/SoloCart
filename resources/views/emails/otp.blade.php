<!DOCTYPE html>
<html>
<head>
    <title>Your Verification Code</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="color: #6366f1; text-align: center;">SoloCart</h2>
        <p>Hello,</p>
        <p>Please use the following verification code to access your account:</p>
        
        <div style="background-color: #f3f4f6; padding: 15px; text-align: center; border-radius: 6px; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #1e293b; margin: 20px 0;">
            {{ $otp }}
        </div>
        
        <p>This code will expire in 10 minutes.</p>
        <p>If you didn't request this, you can safely ignore this email.</p>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <p style="font-size: 12px; color: #64748b; text-align: center;">&copy; {{ date('Y') }} SoloCart. All rights reserved.</p>
    </div>
</body>
</html>
