<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoloCart Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-layout" style="display: flex; min-height: 100vh;">
        <!-- Sidebar -->
        <aside class="sidebar" style="width: 260px; background: #0f172a; color: white; padding: 2rem; position: sticky; top: 0; h-screen: 100vh;">
             <div class="flex items-center gap-2 mb-8">
                 <h1 class="font-bold text-2xl">SoloCart.</h1>
                 <span class="text-xs bg-primary px-2 py-1 rounded">Admin</span>
             </div>
             <nav>
                 <ul style="display: flex; flex-direction: column; gap: 0.5rem;">
                     <li><a href="{{ route('admin.dashboard') }}" class="block p-3 rounded hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-slate-400' }}">Dashboard</a></li>
                     <li><a href="{{ route('admin.products.index') }}" class="block p-3 rounded hover:bg-white/10 {{ request()->routeIs('admin.products.index') ? 'bg-primary text-white' : 'text-slate-400' }}">Products</a></li>
                     <li><a href="{{ route('admin.orders.index') }}" class="block p-3 rounded hover:bg-white/10 {{ request()->routeIs('admin.orders.index') ? 'bg-primary text-white' : 'text-slate-400' }}">Orders</a></li>
                     <li><a href="{{ route('admin.users.index') }}" class="block p-3 rounded hover:bg-white/10 {{ request()->routeIs('admin.users.index') ? 'bg-primary text-white' : 'text-slate-400' }}">Users</a></li>
                     <li class="mt-8">
                         <form action="{{ route('logout') }}" method="POST">
                             @csrf
                             <button type="submit" class="block w-full text-left p-3 rounded hover:bg-red-900/50 text-red-400 pointer" style="background: none; border: none; cursor: pointer;">
                                 Logout
                             </button>
                         </form>
                     </li>
                 </ul>
             </nav>
        </aside>

        <!-- Main -->
        <main class="main-content" style="flex: 1; padding: 2rem; background: #f1f5f9; overflow-y: auto;">
            @yield('content')
        </main>
    </div>
</body>
</html>
