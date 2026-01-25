# Mail Configuration Guide for Render

To ensure OTP emails are sent correctly, you must configure the environment variables on your Render Dashboard.

## 1. Gmail SMTP (Recommended for testing/small scale)
If you are using a Gmail account to send emails:
1.  Go to your Google Account > Security.
2.  Enable **2-Step Verification**.
3.  Go to **App Passwords** (search for it in the search bar if not visible).
4.  Create a new app password (name it "SoloCart").
5.  Copy the 16-character password.

**Set these environment variables on Render:**
```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-real-gmail@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-real-gmail@gmail.com
MAIL_FROM_NAME="SoloCart"
```
*Note: `MAIL_FROM_ADDRESS` must match `MAIL_USERNAME` for Gmail to accept it.*

## 2. Mailtrap (Recommended for Development)
If you are using Mailtrap:
1.  Go to your Mailtrap Inbox.
2.  Copy the credentials.

**Set these environment variables on Render:**
```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@solocart.com
MAIL_FROM_NAME="SoloCart"
```

## 3. General Troubleshooting
- **Logs:** If emails fail, check the logs on Render. We have added detailed logging to the verification flow.
- **Cache:** OTPs are stored in the default Cache. On Render's free tier, local file cache might be lost on restart. If this is a production app, use Redis.
