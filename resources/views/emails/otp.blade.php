<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding: 40px; color: #1e293b;">
    <div style="max-width: 500px; margin: 0 auto; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h1 style="color: #6366f1; font-weight: 800; margin-bottom: 20px;">SoloCart.</h1>
        <p style="font-size: 16px; margin-bottom: 20px; color: #475569;">Hello,</p>
        <p style="font-size: 16px; margin-bottom: 10px; color: #475569;">Use the One-Time Password (OTP) below to complete your verification.</p>
        
        <div style="background: #e0e7ff; color: #4338ca; padding: 20px; border-radius: 12px; text-align: center; margin: 30px 0;">
            <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px;">{{ $otp ?? '123456' }}</span>
        </div>
        
        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">This code is valid for <strong>10 minutes</strong>. Please do not share this code with anyone.</p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">If you didn't request this, you can ignore this email.</p>
        <p style="font-size: 12px; color: #94a3b8; text-align: center;">&copy; {{ date('Y') }} SoloCart Inc. All rights reserved.</p>
    </div>
</body>
</html>
