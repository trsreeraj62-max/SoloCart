<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoloCart Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .sidebar { min-height: 100vh; }
    </style>
</head>
<body class="bg-slate-100">
    <div class="flex admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-slate-900 text-white p-6 fixed h-full overflow-y-auto z-10">
             <div class="flex items-center gap-2 mb-8">
                 <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center font-bold">S</div>
                 <h1 class="font-bold text-2xl tracking-tight">SoloCart.</h1>
             </div>

             <a href="{{ route('home') }}" target="_blank" class="block mb-6 w-full text-center bg-slate-800 hover:bg-slate-700 text-slate-300 py-2 rounded transition border border-slate-700">
                <i class="fas fa-store mr-2"></i> View Store
             </a>

             <nav>
                 <ul class="space-y-2">
                     <li>
                         <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400' }}">
                             <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-800 transition {{ request()->routeIs('admin.products.index') ? 'bg-blue-600 text-white' : 'text-slate-400' }}">
                             <i class="fas fa-box w-5"></i> Products
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-800 transition {{ request()->routeIs('admin.orders.index') ? 'bg-blue-600 text-white' : 'text-slate-400' }}">
                             <i class="fas fa-shopping-cart w-5"></i> Orders
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-800 transition {{ request()->routeIs('admin.users.index') ? 'bg-blue-600 text-white' : 'text-slate-400' }}">
                             <i class="fas fa-users w-5"></i> Users
                         </a>
                     </li>
                     <div class="pt-4 pb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Marketing</div>
                     <li>
                         <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-800 transition {{ request()->routeIs('admin.banners.index') ? 'bg-blue-600 text-white' : 'text-slate-400' }}">
                             <i class="fas fa-images w-5"></i> Banners
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('admin.discounts.index') }}" class="flex items-center gap-3 p-3 rounded hover:bg-slate-800 transition {{ request()->routeIs('admin.discounts.index') ? 'bg-blue-600 text-white' : 'text-slate-400' }}">
                             <i class="fas fa-tags w-5"></i> Promotions
                         </a>
                     </li>
                     
                     <li class="mt-8 pt-6 border-t border-slate-800">
                         <form action="{{ route('logout') }}" method="POST">
                             @csrf
                             <button type="submit" class="w-full flex items-center justify-center gap-2 p-3 rounded bg-red-600 hover:bg-red-700 text-white font-bold transition">
                                 <i class="fas fa-sign-out-alt"></i> Logout
                             </button>
                         </form>
                     </li>
                 </ul>
             </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1 ml-64 p-8 min-h-screen">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
