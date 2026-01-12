# SoloCart Implementation Plan

## 1. Project Overview
SoloCart is a complete E-Commerce application using Laravel (Backend API + Blade Frontend) and MariaDB/MySQL.
It features a full shopping flow, admin panel, analytics, and token-based authentication (Sanctum).

## 2. Directory Structure & Architecture
The project follows standard Laravel MVC with a dual-layer controller strategy:
- **API Controllers** (`app/Http/Controllers/Api`): Handle JSON responses for mobile/external apps and internal AJAX calls.
- **Web Controllers** (`app/Http/Controllers`): Handle Blade view rendering and form submissions (calling services or API logic internally).

### Key Directories
- `app/Models`: Eloquent models with strict relationships.
- `app/Http/Controllers/Api`: API logic.
- `app/Http/Controllers`: Blade page logic.
- `resources/views`: Blade templates.
    - `layouts`: `app.blade.php`, `admin.blade.php`.
    - `pages`: User facing pages.
    - `admin`: Admin facing pages.
    - `components`: Reusable UI components.

## 3. Database Schema (Migrations)
We will create the following tables.

1. **users**: Modified to include `role`, `phone`, `profile_photo`, `last_login_at`.
2. **categories**: `name`, `slug`, `image`.
3. **products**: `category_id`, `name`, `slug`, `description`, `price`, `stock`, `discount_percent`.
4. **product_images**: `product_id`, `image_path`, `is_primary`.
5. **banners**: `image_path`, `title`, `link`, `type` (hero/carousel).
6. **carts**: `user_id`, `session_id`.
7. **cart_items**: `cart_id`, `product_id`, `quantity`.
8. **orders**: `user_id`, `status` (timeline), `total`, `address`, `payment_method`, `payment_status`.
9. **order_items**: `order_id`, `product_id`, `quantity`, `price`.
10. **discounts**: `code`, `type`, `value`, `valid_until`.
11. **contact_messages**: `name`, `email`, `message`, `reply`.
12. **payments**: `order_id`, `amount`, `method`, `status`.
13. **analytics_logs**: `metric`, `value`, `date`.

## 4. Implementation Steps

### Phase 1: Foundation (Database & Models)
- Modify `User` migration.
- Create migrations for all other tables.
- Generate Models with `hasMany`, `belongsTo` relationships.

### Phase 2: Route & Controller Skeleton
- Setup `routes/api.php` and `routes/web.php`.
- Generate Controllers.

### Phase 3: Blade Frontend (User)
- Design Layouts (Navbar, Footer).
- Implement Home, Product List, Product Detail.
- Implement Cart & Checkout.

### Phase 4: Authentication & Profiles
- Login/Register with OTP logic.
- Profile editing & Image upload.

### Phase 5: Admin Panel
- Dashboard with Charts (Chart.js).
- Product/Category Management.
- Order Management with Email Triggers.

### Phase 6: Refinement
- Email templates.
- Middleware (Admin check).
- Final Polish.

