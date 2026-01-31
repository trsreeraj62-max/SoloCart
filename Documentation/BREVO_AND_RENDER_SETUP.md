# Brevo Email & Render Deployment Documentation

This document outlines the implementation details and configuration for the Brevo email service and the Render hosting environment for the SoloCart project.

---

## 📧 Brevo Email Setup

### 1. Integration Strategy
The project uses the official **Brevo (formerly Sendinblue) PHP SDK** (`getbrevo/brevo-php`) for transactional emails. This provides more reliability and better formatting options compared to standard SMTP.

### 2. Implementation Logic
A dedicated service handles all email logic:
- **Location:** `app/Services/BrevoMailService.php`
- **Key Features:**
    - **`sendMail()`:** The core method that initializes the Brevo API using the `BREVO_API_KEY` and sends emails via the Transactional Emails API.
    - **`sendOtp()`:** Sends a premium-designed OTP email for user verification.
    - **`sendPasswordResetLink()`:** Sends a security-themed password reset link.
    - **`sendOrderConfirmation()`:** Sends a detailed order summary with a blue/white premium theme.
    - **`sendOrderStatusUpdate()`:** Sends real-time status updates (Shipped, Delivered, Cancelled) with dynamic color coding.
    - **`sendOrderDelivered()`:** Sends a final delivery confirmation with the **Invoice PDF attached** as a Base64 string.

### 3. Configuration (Environment Variables)
To make Brevo work, the following variables must be set in your `.env` (local) and **Render Dashboard** (Production):

| Variable | Value | Description |
| :--- | :--- | :--- |
| `BREVO_API_KEY` | `xkeysib-...` | Your Brevo V3 API Key |
| `MAIL_HOST` | `smtp-relay.brevo.com` | SMTP host (fallback) |
| `MAIL_PORT` | `2525` | SMTP port |
| `MAIL_USERNAME` | `apikey` | Literal string "apikey" |
| `MAIL_PASSWORD` | `xsmtpsib-...` | SMTP password from Brevo |
| `MAIL_ENCRYPTION` | `tls` | Security protocol |
| `MAIL_FROM_ADDRESS` | `trsreeraj62@gmail.com` | Verified sender in Brevo |
| `MAIL_FROM_NAME` | `"SoloCart Admin"` | Display name for emails |

---

## 🚀 Render Environment Setup

### 1. Infrastructure
The backend is hosted on **Render** as a **Web Service** using a containerized environment (Docker).

### 2. Docker Setup
- **Dockerfile:** Based on `php:8.2-apache`.
    - Installs required extensions (`pdo_pgsql`, `gd`, `zip`, `bcmath`).
    - Configures Apache `DocumentRoot` to `/public`.
    - Runs `composer install --optimize-autoloader`.
- **docker-run.sh:** The entrypoint script that runs every time the container starts.
    - Dynamically updates Apache ports based on Render's `$PORT` variable.
    - Automatically runs `php artisan migrate --force`.
    - Seeders run automatically to ensure `Admin` users and default `Banners` exist.

### 3. Render Dashboard Environment Variables
Ensure these are configured in the Render Dashboard:
- `APP_ENV`: `production`
- `APP_DEBUG`: `false`
- `APP_KEY`: Generated via `php artisan key:generate --show`
- `DB_CONNECTION`: `pgsql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: (Provided by Render PostgreSQL)
- `FRONTEND_URL`: URL of your Netlify/Render frontend (for CORS and email links).

### 4. Critical Maintenance
If you update your `.env` credentials in Render and emails don't reflect the change, you must clear the Laravel config cache.
- **Auto-Fix:** Visit `https://your-backend-url.onrender.com/api/system/maintenance?key=render_fix_2026&optimize=1`
- **Manual:** Use the Render **Shell** tab and run `php artisan config:clear`.

---

## 🛠️ Files to Reference
- **Brevo Service:** `app/Services/BrevoMailService.php`
- **Deployment Script:** `docker-run.sh`
- **Container Config:** `Dockerfile`
- **Render Cheat-Sheet:** `RENDER_COMMANDS.txt`
