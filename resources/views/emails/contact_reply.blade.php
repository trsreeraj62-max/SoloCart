<!DOCTYPE html>
<html>
<head>
    <title>Reply to your message</title>
</head>
<body>
    <h1>Hello {{ $contactMessage->name }},</h1>
    <p>Thank you for contacting us regarding "<strong>{{ $contactMessage->subject }}</strong>".</p>
    
    <p>Here is our reply:</p>
    
    <div style="padding: 15px; background-color: #f9f9f9; border-left: 4px solid #3498db;">
        {!! nl2br(e($replyContent)) !!}
    </div>

    <p>Best regards,<br>
    SoloCart Support Team</p>
</body>
</html>
