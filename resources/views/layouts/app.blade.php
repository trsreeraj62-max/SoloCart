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
                <li><a href="{{ route('about') }}" class="nav-link">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="nav-link">Contact</a></li>
                
                @auth
                    <li>
                        <a href="{{ route('cart.index') }}" class="nav-link" style="display: flex; align-items: center; position: relative; padding: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                            <span id="cart-count" style="position: absolute; top: -5px; right: -8px; background: var(--secondary); color: white; border-radius: 50%; height: 18px; min-width: 18px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;">
                                {{ auth()->user()->cart?->items->sum('quantity') ?? 0 }}
                            </span>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown" id="userDropdown">
                        <a href="#" onclick="toggleDropdown(event)" class="flex items-center gap-2">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="profile-img">
                            @else
                                <div class="profile-img" style="background: #ddd; display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; color: #555;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            @endif
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">My Profile</a>
                            <a href="{{ route('orders.index') }}" class="dropdown-item">My Orders</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">Admin Dashboard</a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </div>
                    </li>

                    <script>
                        function toggleDropdown(e) {
                            e.preventDefault();
                            document.querySelector('.dropdown').classList.toggle('active');
                        }

                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(e) {
                            const dropdown = document.querySelector('.dropdown');
                            if (dropdown && !dropdown.contains(e.target)) {
                                dropdown.classList.remove('active');
                            }
                        });
                    </script>
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
