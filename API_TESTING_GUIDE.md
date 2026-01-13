# 🚀 SoloCart API Testing Manifest

Use this guide to test the core API protocols. Use **Postman**, **Insomnia**, or **cURL** to transmit signals.

## 🔐 1. Authentication Protocol

### [POST] Register Account
- **URL**: `https://solocart-backend.onrender.com/api/register`
- **Payload**:
```json
{
    "name": "Alex Mercer",
    "email": "alex@blackwatch.com",
    "password": "protocol_secured",
    "phone": "9988776655"
}
```
- **Expect**: `200 OK` + `otp_debug` (if in dev mode).

### [POST] Verify OTP Signal
- **URL**: `https://solocart-backend.onrender.com/api/otp/verify`
- **Payload**:
```json
{
    "user_id": 1,
    "otp": "654321"
}
```
- **Expect**: `200 OK` + `access_token`. **Save this token for future requests.**

---

## 🛒 2. Acquisition (Cart) Manifest

### [GET] Retrieve Manifest
- **URL**: `https://solocart-backend.onrender.com/api/cart`
- **Headers**: `Authorization: Bearer {{YOUR_TOKEN}}`
- **Expect**: Current items in digital container.

### [POST] Synchronize Item (Add)
- **URL**: `https://solocart-backend.onrender.com/api/cart/add`
- **Payload**:
```json
{
    "product_id": 5,
    "quantity": 1
}
```

---

## 📦 3. Deployment (Orders)

### [POST] Standard Deployment (Cart Checkout)
- **URL**: `https://solocart-backend.onrender.com/api/checkout/cart`
- **Payload**:
```json
{
    "address": "Sector 7, Midgar City",
    "payment_method": "cod"
}
```

### [GET] Audit Trail (Order List)
- **URL**: `https://solocart-backend.onrender.com/api/orders`
- **Expect**: Historically placed orders with status codes.

---

## 🔍 4. Global Signal Nodes (Public)

- **[GET] Products**: `/api/products?search=phone&min_price=10000`
- **[GET] Categories**: `/api/categories`
- **[GET] Banners**: `/api/banners`
- **[POST] Contact**: `/api/contact` (Fields: name, email, subject, message)

---

### 🛠️ Admin Override (Requires Admin Token)
- **[GET] Analytics**: `/api/admin/analytics`
- **[POST] Update Order Status**: `/api/admin/orders/{id}/status` (Body: {"status": "shipped"})
