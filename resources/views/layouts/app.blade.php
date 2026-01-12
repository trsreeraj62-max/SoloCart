<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'SoloCart') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="nav-logo">SoloCart.</a>
                
                <div class="search-bar-container">
                    <form action="{{ route('products.index') }}" method="GET">
                        <input type="text" name="search" class="search-input" placeholder="Search products..." value="{{ request('search') }}">
                    </form>
                </div>
            </div>

            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link">Home</a></li>
                <li><a href="{{ route('products.index') }}" class="nav-link">Shop</a></li>
                
                @auth
                    <li><a href="{{ route('cart.index') }}" class="nav-link">Cart <span id="cart-count">({{ auth()->user()->cart?->items->sum('quantity') ?? 0 }})</span></a></li>
                    
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="profile-img">
                            @else
                                <div class="profile-img" style="background: #ddd; display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; color: #555;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            @endif
                        </a>
                    </li>
                    @if(auth()->user()->role === 'admin')
                         <li><a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Admin</a></li>
                    @endif
                @else
                    <li><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                    <li><a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="min-height: 80vh; padding-bottom: 4rem;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer style="background: #0f172a; color: white; padding: 4rem 0; margin-top: auto;">
        <div class="container grid" style="grid-template-columns: repeat(4, 1fr); gap: 2rem;">
            <div>
                <h3 class="font-bold text-xl mb-4">SoloCart.</h3>
                <p class="text-muted" style="color: #94a3b8;">Premium shopping experience delivered to your doorstep.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Shop</h4>
                <ul>
                    <li><a href="#" class="text-muted" style="color: #94a3b8;">New Arrivals</a></li>
                    <li><a href="#" class="text-muted" style="color: #94a3b8;">Best Sellers</a></li>
                    <li><a href="#" class="text-muted" style="color: #94a3b8;">Discounts</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Company</h4>
                <ul>
                    <li><a href="#" class="text-muted" style="color: #94a3b8;">About Us</a></li>
                    <li><a href="#" class="text-muted" style="color: #94a3b8;">Contact</a></li>
                    <li><a href="#" class="text-muted" style="color: #94a3b8;">Terms</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Newsletter</h4>
                <input type="email" placeholder="Your email" class="input-field" style="margin-bottom: 0.5rem;">
                <button class="btn btn-primary">Subscribe</button>
            </div>
        </div>
        <div class="container text-center mt-4 text-muted" style="color: #94a3b8;">
            &copy; {{ date('Y') }} SoloCart. All rights reserved.
        </div>
    </footer>

</body>
</html>
