# Brevo Email Setup Guide

## ✅ What's Configured

SoloCart now uses **Brevo Email API** (HTTP-based) instead of SMTP to send verification emails. This bypasses Render's SMTP port blocks.

---

## 🔑 Required Environment Variables on Render

Add these to your **Render Dashboard → Environment Variables**:

```env
BREVO_API_KEY=your_actual_brevo_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SoloCart"
```

---

## 📧 How to Get Your Brevo API Key

1. **Sign up** at [Brevo.com](https://www.brevo.com) (free tier: 300 emails/day)
2. Go to **Settings** → **API Keys**
3. Click **Generate a new API key**
4. Copy the key (starts with `xkeysib-...`)
5. Paste it into Render's `BREVO_API_KEY` environment variable

---

## 🧪 Test Email Functionality

### Method 1: Via API Endpoint (Recommended)

```bash
curl "https://solocart-backend.onrender.com/api/test-email?email=your@email.com"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Email sent successfully to your@email.com",
  "driver": "smtp"
}
```

### Method 2: Via Laravel Tinker (on Render Shell)

```bash
php artisan tinker
>>> App\Services\BrevoMailService::sendOtp('test@example.com', '123456');
```

---

## 🛠 How It Works

### Traditional SMTP (❌ Blocked on Render)
```
Laravel → SMTP (Port 587) → Brevo → Email
          ^^^ BLOCKED BY RENDER FIREWALL
```

### Brevo HTTP API (✅ Works on Render)
```
Laravel → HTTP API (Port 443) → Brevo → Email
          ^^^ HTTPS - ALWAYS ALLOWED
```

---

## 🐛 Troubleshooting

### Error: "Email service not configured"
**Cause:** `BREVO_API_KEY` not set in Render  
**Fix:** Add the environment variable and redeploy

### Error: "Email service authentication failed"
**Cause:** Invalid Brevo API key  
**Fix:** Double-check your API key in Brevo dashboard

### Error: "Invalid email address"
**Cause:** Malformed recipient email  
**Fix:** Validate email format on frontend

---

## 📊 Check Logs on Render

All email activity is logged for debugging:

1. Go to **Render Dashboard**
2. Select your **backend service**
3. Click **Logs**
4. Search for:
   - `"Attempting to send OTP"` - Email send initiated
   - `"OTP Email Sent Successfully"` - Email delivered
   - `"Brevo API Failed"` - Error occurred

---

## 🔒 Security Notes

- ✅ **API key is server-side only** (never exposed to frontend)
- ✅ **OTP stored in cache** (expires in 10 minutes)
- ✅ **Rate limiting** should be added (TODO: implement rate limiter on `/api/register`)
- ✅ **Email validation** happens before OTP generation

---

## 🚀 Deployment Checklist

- [ ] Brevo account created
- [ ] API key generated
- [ ] `BREVO_API_KEY` added to Render environment variables
- [ ] `MAIL_FROM_ADDRESS` set to verified sender email
- [ ] `MAIL_FROM_NAME` set (e.g., "SoloCart")
- [ ] Tested with `/api/test-email` endpoint
- [ ] Registration flow tested end-to-end

---

## 📝 Files Modified

- `app/Services/BrevoMailService.php` - Enhanced with validation & logging
- `app/Http/Controllers/Api/AuthController.php` - Uses BrevoMailService
- `config/services.php` - Brevo configuration

---

## ✨ Features

✅ **Production-ready** error handling  
✅ **Detailed logging** for debugging  
✅ **Beautiful HTML emails** with branded design  
✅ **10-minute OTP expiration**  
✅ **Works perfectly on Render** (no SMTP needed)
