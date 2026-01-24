# Admin Orders API - Complete Backend Documentation

## 📋 Overview

This document describes the **backend-only** Laravel API for the admin orders management system. The API provides complete order-product data with buyer details, product information, images, and pricing.

---

## 🔗 API Endpoints

### **1. GET /api/admin/orders**
Get all orders with complete product details (admin only)

#### **Authentication:**
```
Authorization: Bearer {admin_token}
```

#### **Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `status` | string | No | Filter by order status |
| `search` | string | No | Search by order ID, buyer name, or email |
| `page` | integer | No | Page number (default: 1) |

#### **Response Structure:**
```json
{
  "success": true,
  "message": "Admin orders retrieved",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 3,
        "status": "pending",
        "total": "1069.00",
        "address": "123 Test Street, Test City, 12345",
        "payment_method": "cod",
        "payment_status": "unpaid",
        "created_at": "2026-01-24T10:11:33.000000Z",
        "updated_at": "2026-01-24T10:11:33.000000Z",
        "cancelled_at": null,
        "delivered_at": null,
        "buyer_name": "Test Buyer",
        "buyer_email": "testbuyer@test.com",
        "user": {
          "id": 3,
          "name": "Test Buyer",
          "email": "testbuyer@test.com",
          "phone": null
        },
        "items": [
          {
            "id": 1,
            "order_id": 1,
            "product_id": 1,
            "quantity": 1,
            "price": "999.00",
            "created_at": "2026-01-24T10:11:33.000000Z",
            "updated_at": "2026-01-24T10:11:33.000000Z",
            "line_total": 999,
            "product_name": "iPhone 15 Pro",
            "product_image": "https://images.unsplash.com/photo-123...",
            "product": {
              "id": 1,
              "category_id": 1,
              "name": "iPhone 15 Pro",
              "slug": "iphone-15-pro-3973",
              "description": "Experience the power...",
              "price": "999.00",
              "stock": 50,
              "discount_percent": 0,
              "is_active": true,
              "image_url": "https://images.unsplash.com/photo-123...",
              "images": [
                {
                  "id": 1,
                  "product_id": 1,
                  "image_path": "https://images.unsplash.com/...",
                  "is_primary": true,
                  "image_url": "https://images.unsplash.com/..."
                }
              ]
            }
          }
        ]
      }
    ],
    "total": 3,
    "per_page": 20,
    "last_page": 1
  }
}
```

#### **Key Fields for Frontend:**
| Field | Location | Description |
|-------|----------|-------------|
| `id` | `data[].id` | Order ID |
| `buyer_name` | `data[].buyer_name` | Buyer's name (computed) |
| `buyer_email` | `data[].buyer_email` | Buyer's email (computed) |
| `total` | `data[].total` | Order total amount |
| `status` | `data[].status` | Order status |
| `product_name` | `data[].items[].product_name` | Product name (computed) |
| `product_image` | `data[].items[].product_image` | Product image URL (computed) |
| `price` | `data[].items[].price` | Unit price |
| `quantity` | `data[].items[].quantity` | Quantity ordered |
| `line_total` | `data[].items[].line_total` | Price × Quantity (computed) |

#### **Example Request:**
```bash
# Get all orders
curl -X GET "https://solocart-backend.onrender.com/api/admin/orders" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"

# Filter by status
curl -X GET "https://solocart-backend.onrender.com/api/admin/orders?status=pending" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"

# Search
curl -X GET "https://solocart-backend.onrender.com/api/admin/orders?search=testbuyer" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json"
```

---

### **2. POST /api/admin/orders/{id}/status**
Update order status (admin only)

#### **Authentication:**
```
Authorization: Bearer {admin_token}
```

#### **URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | Order ID |

#### **Request Body:**
```json
{
  "status": "approved"
}
```

#### **Allowed Status Values:**
- `pending` - Order placed, awaiting approval
- `approved` - Order approved by admin
- `processing` - Order being prepared
- `shipped` - Order shipped
- `delivered` - Order delivered to customer
- `cancelled` - Order cancelled

#### **Status Transition Rules (Enforced by Backend):**
- `pending` → `approved` ✅
- `approved` → `processing` ✅
- `processing` → `shipped` ✅
- `shipped` → `delivered` ✅
- Any status → `cancelled` ✅ (restores stock)
- `delivered` or `cancelled` → No changes ❌

#### **Success Response (200):**
```json
{
  "success": true,
  "message": "Order status updated to approved",
  "data": {
    "id": 1,
    "status": "approved",
    "buyer_name": "Test Buyer",
    "buyer_email": "testbuyer@test.com",
    "total": "1069.00",
    "items": [...],
    "updated_at": "2026-01-24T10:20:00.000000Z"
  }
}
```

#### **Error Responses:**

**Validation Error (422):**
```json
{
  "success": false,
  "message": {
    "status": ["The selected status is invalid."]
  }
}
```

**Order Not Found (404):**
```json
{
  "success": false,
  "message": "Order not found"
}
```

**Invalid Transition (422):**
```json
{
  "success": false,
  "message": "Invalid status transition from delivered to approved"
}
```

#### **Example Request:**
```bash
curl -X POST "https://solocart-backend.onrender.com/api/admin/orders/1/status" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"status":"approved"}'
```

---

## 🗄️ Database Relationships

### **Order Model:**
```php
// Relationships
public function user() // BelongsTo User
public function items() // HasMany OrderItem
public function payment() // HasOne Payment

// Constants
Order::STATUS_PENDING
Order::STATUS_APPROVED
Order::STATUS_PROCESSING
Order::STATUS_SHIPPED
Order::STATUS_DELIVERED
Order::STATUS_CANCELLED
```

### **OrderItem Model:**
```php
// Relationships
public function order() // BelongsTo Order
public function product() // BelongsTo Product
```

### **Product Model:**
```php
// Relationships
public function images() // HasMany ProductImage
public function category() // BelongsTo Category
public function orderItems() // HasMany OrderItem

// Accessors
$product->image_url // Returns first image or fallback avatar
```

### **User Model:**
```php
// Relationships
public function orders() // HasMany Order
```

---

## 📊 Data Flow

### **GET /api/admin/orders Flow:**
1. Controller receives request
2. Loads orders with eager loading:
   - `user` (buyer details)
   - `items.product.images` (product details with images)
3. Applies filters (status, search)
4. Paginates results (20 per page)
5. Transforms data to add computed fields:
   - `buyer_name`
   - `buyer_email`
   - `line_total` (for each item)
   - `product_name` (for each item)
   - `product_image` (for each item)
6. Returns JSON response

### **POST /api/admin/orders/{id}/status Flow:**
1. Controller validates status value
2. Finds order with relationships
3. Calls `OrderService->updateStatus()`
4. Service validates status transition
5. Service updates timestamps (`cancelled_at`, `delivered_at`)
6. Service restores stock (if cancelled)
7. Service sends email notification
8. Reloads order with fresh data
9. Adds computed fields
10. Returns updated order

---

## ✅ Frontend Integration

### **Display Orders Table:**
```javascript
// GET /api/admin/orders
const response = await fetch('/api/admin/orders', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const { data } = await response.json();

// Access data:
data.data.forEach(order => {
  console.log(order.id); // Order ID
  console.log(order.buyer_name); // Buyer name
  console.log(order.buyer_email); // Buyer email
  console.log(order.total); // Total amount
  console.log(order.status); // Order status
  
  order.items.forEach(item => {
    console.log(item.product_name); // Product name
    console.log(item.product_image); // Product image URL
    console.log(item.price); // Unit price
    console.log(item.quantity); // Quantity
    console.log(item.line_total); // Price × Quantity
  });
});
```

### **Update Order Status:**
```javascript
// POST /api/admin/orders/{id}/status
const response = await fetch(`/api/admin/orders/${orderId}/status`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({ status: 'approved' })
});

const result = await response.json();

if (result.success) {
  console.log('Status updated!', result.data);
} else {
  console.error('Error:', result.message);
}
```

---

## 🔒 Security & Validation

### **Authorization:**
- All endpoints require `auth:sanctum` middleware
- All endpoints require `admin` middleware (checks `role === 'admin'`)

### **Validation:**
- Status values are strictly validated against allowed constants
- Status transitions are enforced by `OrderService`
- Proper HTTP status codes for all error cases

### **Error Handling:**
- 401: Unauthenticated
- 403: Unauthorized (not admin)
- 404: Order not found
- 422: Validation error or invalid status transition
- 500: Server error

---

## 🧪 Testing

### **Test Data Created:**
You already have 3 test orders:
- Order ID: 1, 2, 3
- Buyer: Test Buyer (testbuyer@test.com)
- Product: iPhone 15 Pro
- Status: pending
- Total: 1069.00

### **Test Admin Login:**
```
Email: admin@store.com
Password: admin123

OR

Email: trsreeraj07@gmail.com
Password: [your password]
```

### **Test the API:**
```bash
# 1. Login
curl -X POST "https://solocart-backend.onrender.com/api/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@store.com","password":"admin123"}'

# 2. Get orders (use token from step 1)
curl -X GET "https://solocart-backend.onrender.com/api/admin/orders" \
  -H "Authorization: Bearer YOUR_TOKEN"

# 3. Update status
curl -X POST "https://solocart-backend.onrender.com/api/admin/orders/1/status" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status":"approved"}'
```

---

## 📝 Summary

**What the Backend Provides:**

✅ Complete order data with buyer details  
✅ Product names, images, prices for each item  
✅ Line totals (price × quantity) pre-calculated  
✅ Proper pagination (20 orders per page)  
✅ Status filtering and search  
✅ Status update with validation  
✅ Stock restoration on cancellation  
✅ Email notifications (OrderStatusMail)  
✅ Timestamps for cancelled/delivered  
✅ Proper error messages and HTTP codes  

**Frontend can directly display:**
- Order ID, Total, Status, Date
- Buyer Name, Buyer Email
- Product Name, Product Image, Price, Quantity, Line Total
- Update status with dropdown/buttons

**No frontend processing required!** All data is ready to display.
