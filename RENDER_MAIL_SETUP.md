# Render Backend Email Configuration Guide

To fix the issue where OTP emails are not sending, you must configure the environment variables on your Render Dashboard.

1. Go to your **Render Dashboard**.
2. Select your **Backend Service**.
3. Click on **Environment**.
4. Add or Update the following variables:

## Option A: Using Brevo (Recommended - Free & Reliable)
Sign up at [Brevo.com](https://www.brevo.com/), go to **SMTP & API**, and get your credentials.

| Variable | Value |
|----------|-------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp-relay.brevo.com` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | *(Your Brevo Login Email)* |
| `MAIL_PASSWORD` | *(Your Brevo SMTP Key - NOT your login password)* |
| `MAIL_ENCRYPTION` | `tls` |
| `MAIL_FROM_ADDRESS` | `no-reply@solocart.com` |
| `MAIL_FROM_NAME` | `SoloCart` |

## Option B: Using Gmail (Requires App Password)
**Note:** valid only if you have 2-Step Verification enabled and generate an [App Password](https://myaccount.google.com/apppasswords).

| Variable | Value |
|----------|-------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `465` |
| `MAIL_USERNAME` | *(Your Gmail Address)* |
| `MAIL_PASSWORD` | *(Your 16-digit Google App Password)* |
| `MAIL_ENCRYPTION` | `ssl` |
| `MAIL_FROM_ADDRESS` | `noreply@yourdomain.com` |
| `MAIL_FROM_NAME` | `SoloCart` |

## Why was it failing?
- **Registration Issue:** The system was blocking "existing" emails even if they were unverified, leaving users stuck. This has been fixed in the code.
- **Email Issue:** Render does not have a built-in mail server. You **MUST** use an external SMTP provider like Brevo or Gmail.
- **Logs:** I have added detailed logs. You can now check "Logs" in Render to see "OTP Email sent successfully" or specific error messages.
- **CRITICAL:** After updating Env Keys on Render, you MUST clear the cache or the old (Gmail) settings will stay active.
  - Run: `https://solocart-backend.onrender.com/api/system/maintenance?key=render_fix_2026&optimize=1`
