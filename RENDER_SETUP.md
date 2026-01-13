# 🚀 Render Environment Variables Configuration

## ⚠️ IMPORTANT: Update These on Render Dashboard

After pushing the code changes, you MUST update the environment variables on Render for the backend to work correctly with the frontend.

---

## Steps to Update Render Environment Variables

### 1. Go to Render Dashboard
- Navigate to: https://dashboard.render.com
- Select your service: **solocart-backend**

### 2. Go to Environment Tab
- Click on **Environment** in the left sidebar
- Click **Add Environment Variable** for each new variable

### 3. Add/Update These Variables

#### Required New Variables:

```env
FRONTEND_URL=https://polite-bombolone-b0c069.netlify.app
```

#### Update Existing Variables:

```env
SANCTUM_STATEFUL_DOMAINS=polite-bombolone-b0c069.netlify.app
SESSION_DOMAIN=.netlify.app
```

#### Verify These Exist:

```env
APP_URL=https://solocart-backend.onrender.com
APP_ENV=production
APP_DEBUG=false
```

---

## Complete Environment Variables List for Render

Copy and paste these into Render (update database credentials as needed):

```env
APP_NAME=SoloCart
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://solocart-backend.onrender.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database (Render PostgreSQL - Auto-filled by Render)
DB_CONNECTION=pgsql
DB_HOST=YOUR_RENDER_DB_HOST
DB_PORT=5432
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD

# Session & Cache
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail Config (Update with your SMTP credentials)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=YOUR_MAIL_USERNAME
MAIL_PASSWORD=YOUR_MAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@solocart.com
MAIL_FROM_NAME=SoloCart

# Frontend Integration (CRITICAL - ADD THESE)
FRONTEND_URL=https://polite-bombolone-b0c069.netlify.app

# Sanctum Config (CRITICAL - UPDATE THESE)
SANCTUM_STATEFUL_DOMAINS=polite-bombolone-b0c069.netlify.app
SESSION_DOMAIN=.netlify.app
```

---

## After Adding Variables

### 1. Save Changes
Click **Save Changes** button in Render dashboard

### 2. Trigger Redeploy
The push to GitHub will automatically trigger a redeploy, OR you can manually trigger it:
- Click **Manual Deploy** → **Deploy latest commit**

### 3. Wait for Deployment
- Monitor the deployment logs
- Wait for "Build successful" message
- Service will automatically restart with new environment variables

### 4. Verify Deployment
Check the logs for any errors:
```bash
# In Render logs, you should see:
✓ Laravel application cache cleared
✓ Configuration cache cleared
✓ Route cache cleared
✓ View cache cleared
```

---

## Testing After Deployment

### 1. Test Backend Health
Open in browser:
```
https://solocart-backend.onrender.com/api/health
```

Expected response:
```json
{
  "status": "ok",
  "timestamp": "2026-01-13T..."
}
```

### 2. Test CORS from Frontend
Open browser console on `https://polite-bombolone-b0c069.netlify.app` and run:

```javascript
fetch('https://solocart-backend.onrender.com/api/products')
    .then(r => r.json())
    .then(console.log)
    .catch(console.error);
```

**Expected**: Should return product data without CORS errors

### 3. Check Response Headers
In Network tab, verify the response includes:
```
Access-Control-Allow-Origin: https://polite-bombolone-b0c069.netlify.app
```

---

## Troubleshooting

### Issue: CORS Error Still Appears
**Solution**: 
1. Verify environment variables are saved on Render
2. Check Render logs for any configuration errors
3. Manually redeploy the service
4. Clear browser cache and try again

### Issue: 500 Internal Server Error
**Solution**:
1. Check Render logs for detailed error
2. Verify database connection is working
3. Ensure APP_KEY is set
4. Run migrations if needed (Render should auto-run)

### Issue: Changes Not Reflecting
**Solution**:
1. Verify the latest commit is deployed (check commit hash in Render)
2. Manually trigger a redeploy
3. Clear Laravel cache (automatic in Dockerfile)

---

## Quick Checklist

- [ ] Added `FRONTEND_URL` environment variable on Render
- [ ] Updated `SANCTUM_STATEFUL_DOMAINS` to frontend domain
- [ ] Added `SESSION_DOMAIN=.netlify.app`
- [ ] Saved changes on Render
- [ ] Deployment completed successfully
- [ ] Tested API health endpoint
- [ ] Tested CORS from frontend
- [ ] Verified no errors in Render logs

---

**Status**: Code changes pushed to GitHub ✅  
**Next**: Update Render environment variables ⏳  
**Last Updated**: 2026-01-13
