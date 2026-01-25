# 🔗 Frontend-Backend Integration Guide

## Overview
This guide explains how to connect the SoloCart frontend (Render) with the backend API (Render).

## URLs
- **Backend API**: `https://solocart-backend.onrender.com`
- **Frontend**: `https://solocart-frontend.onrender.com`

---

## Backend Configuration

### 1. Environment Variables (Already Configured)

The following environment variables must be set on Render:

```env
APP_URL=https://solocart-backend.onrender.com
FRONTEND_URL=https://solocart-frontend.onrender.com
SANCTUM_STATEFUL_DOMAINS=solocart-frontend.onrender.com
SESSION_DOMAIN=.onrender.com
```

### 2. CORS Configuration

File: `config/cors.php`

```php
'allowed_origins' => ['https://solocart-frontend.onrender.com'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => false,
```

✅ **Status**: Configured and fixed (removed syntax error)

---

## Frontend Configuration

### 1. API Base URL

In your frontend JavaScript files, configure the API base URL:

```javascript
const API_BASE_URL = 'https://solocart-backend.onrender.com';

// Example API call
async function fetchProducts() {
    const response = await fetch(`${API_BASE_URL}/api/products`);
    const data = await response.json();
    return data;
}
```

### 2. Authentication Headers

For authenticated requests (using Sanctum):

```javascript
// Get CSRF token first
await fetch(`${API_BASE_URL}/sanctum/csrf-cookie`, {
    credentials: 'include'
});

// Then make authenticated requests
const response = await fetch(`${API_BASE_URL}/api/cart`, {
    method: 'GET',
    credentials: 'include',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    }
});
```

### 3. All API Endpoints

#### 🌍 Public
- `GET /api/health` - Check API status
- `GET /api/home-data` - Get all home page data (banners, categories, featured/latest products)
- `GET /api/products` - List all products (supports pagination & filters)
- `GET /api/products/{id}` - Get product details
- `GET /api/products/{id}/similar` - Get similar products
- `GET /api/categories` - List all categories
- `GET /api/banners` - List active banners
- `POST /api/contact` - Submit contact form

#### 🔐 Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login with email/password (requests OTP)
- `POST /api/otp/verify` - Verify OTP to complete login
- `POST /api/otp/resend` - Resend OTP

#### 👤 User (Requires Authentication)
- `GET /api/user` - Get current user profile
- `POST /api/logout` - Logout (invalidate token)

#### 🛒 Cart
- `GET /api/cart` - Get current user's cart
- `POST /api/cart/add` - Add item to cart
- `POST /api/cart/update` - Update cart item quantity
- `PUT /api/cart/{id}` - Update cart item quantity (alternate)
- `POST /api/cart/remove` - Remove item from cart
- `DELETE /api/cart/{id}` - Remove item from cart (alternate)
- `POST /api/cart/clear` - Clear entire cart

#### 💳 Checkout
- `POST /api/checkout/cart` - Checkout entire cart
- `POST /api/checkout/single` - Direct checkout for single product

#### 📦 Orders
- `GET /api/orders` - List user history
- `GET /api/orders/{id}` - Get order details
- `POST /api/orders/{id}/cancel` - Cancel order (if eligible)
- `POST /api/orders/{id}/return` - Return order (if delivered)
- `GET /api/orders/{id}/invoice` - Download order invoice

#### 🛡️ Admin (Requires Admin Role)
- `GET /api/admin/analytics` - Get dashboard analytics
- `GET /api/admin/orders` - List all system orders
- `POST /api/admin/orders/{id}/status` - Update order status (pending, shipped, delivered, cancelled)

---

## Testing the Connection

### 1. Test CORS

Open browser console on your frontend and run:

```javascript
fetch('https://solocart-backend.onrender.com/api/products')
    .then(r => r.json())
    .then(console.log)
    .catch(console.error);
```

**Expected**: Should return product data without CORS errors.

### 2. Test API Health

```javascript
fetch('https://solocart-backend.onrender.com/api/health')
    .then(r => r.json())
    .then(console.log);
```

### 3. Check Network Tab

- Open DevTools → Network tab
- Make a request from frontend
- Check response headers for:
  - `Access-Control-Allow-Origin: https://solocart-frontend.onrender.com`
  - Status code: 200 (not 403 or 500)

---

## Common Issues & Solutions

### Issue 1: CORS Error
**Error**: `Access to fetch at '...' from origin '...' has been blocked by CORS policy`

**Solution**: 
- Verify `config/cors.php` has correct frontend URL
- Ensure Render environment variables are set
- Clear Laravel cache: `php artisan config:clear`

### Issue 2: 401 Unauthorized
**Error**: API returns 401 for authenticated routes

**Solution**:
- Ensure you're sending `credentials: 'include'` in fetch requests
- Get CSRF cookie before making authenticated requests
- Check SANCTUM_STATEFUL_DOMAINS is set correctly

### Issue 3: 500 Server Error
**Error**: API returns 500 Internal Server Error

**Solution**:
- Check Render logs for detailed error
- Ensure database migrations have run
- Verify all environment variables are set on Render

---

## Deployment Checklist

### Backend (Render)
- [x] CORS configuration updated
- [x] Environment variables set (APP_URL, FRONTEND_URL, SANCTUM_STATEFUL_DOMAINS)
- [ ] Database migrations run
- [ ] Seeder run (if needed)
- [ ] Verify API endpoints are accessible

### Frontend (Render)
- [ ] API_BASE_URL configured correctly
- [ ] All API calls use correct endpoints
- [ ] Authentication flow implemented
- [ ] Error handling for API failures
- [ ] Environment variables set (if using build process)

---

## Next Steps

1. **Update Render Environment Variables**:
   - Go to Render Dashboard → Your Service → Environment
   - Add `FRONTEND_URL=https://solocart-frontend.onrender.com`
   - Add `SANCTUM_STATEFUL_DOMAINS=solocart-frontend.onrender.com`
   - Add `SESSION_DOMAIN=.onrender.com`

2. **Push Backend Changes**:
   ```bash
   git add .
   git commit -m "Configure CORS and Sanctum for Render frontend"
   git push origin main
   ```

3. **Update Frontend**:
   - Configure API_BASE_URL in your frontend code
   - Test all API integrations
   - Deploy to Render

4. **Test Integration**:
   - Visit frontend URL
   - Test product listing
   - Test cart functionality
   - Test checkout process
   - Test authentication flow

---

**Last Updated**: 2026-01-21  
**Status**: Backend configured, ready for frontend integration
