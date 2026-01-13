<nav class="bg-[#2874f0] text-white py-2 sticky top-0 z-[1000] shadow-md">
    <div class="container container-max flex items-center justify-between">
        
        <!-- Left: Brand + Nav Links -->
        <div class="flex items-center gap-10">
            <a href="{{ route('home') }}" class="flex flex-col leading-none no-underline">
                <span class="text-2xl font-black italic tracking-tighter text-white">SoloCart</span>
                <span class="text-[10px] italic font-bold text-[#ffe500] flex items-center gap-1">
                    Explore <span class="text-white">Plus</span> <i class="fas fa-plus text-[8px]"></i>
                </span>
            </a>

            <ul class="hidden md:flex items-center gap-6 m-0 p-0 list-none">
                <li><a href="{{ route('home') }}" class="text-sm font-bold text-white no-underline hover:text-[#ffe500] transition-colors font-sans">Home</a></li>
                <li><a href="{{ route('products.index') }}" class="text-sm font-bold text-white no-underline hover:text-[#ffe500] transition-colors font-sans">Shop</a></li>
                <li><a href="{{ route('about') }}" class="text-sm font-bold text-white no-underline hover:text-[#ffe500] transition-colors font-sans">About</a></li>
                <li><a href="{{ route('contact') }}" class="text-sm font-bold text-white no-underline hover:text-[#ffe500] transition-colors font-sans">Contact</a></li>
            </ul>
        </div>

        <!-- Right: Actions (Cart then Profile) -->
        <div class="flex items-center gap-8">
            
            <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}" class="flex items-center text-white no-underline hover:text-[#ffe500] transition-colors group">
                <div class="relative">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    @auth
                        @php $cartCount = auth()->user()->cart?->items->sum('quantity') ?? 0; @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-[#ff6161] text-white text-[9px] font-black h-4 w-4 rounded-full flex items-center justify-center border border-[#2874f0]">
                                {{ $cartCount }}
                            </span>
                        @endif
                    @endauth
                </div>
            </a>

            <!-- Profile Circle Dropdown -->
            @auth
                <div class="relative">
                    <button id="profileDropdownBtn" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center border border-white/20 transition-all overflow-hidden focus:outline-none">
                        <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover">
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="profileDropdownMenu" class="absolute right-0 top-full pt-2 hidden opacity-0 translate-y-2 transition-all duration-300 w-52 z-[1001]">
                        <div class="bg-white rounded-sm shadow-2xl border border-slate-100 py-2 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-50 bg-slate-50/50">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Authenticated</p>
                                <p class="text-[11px] font-black text-slate-900 truncate m-0">{{ auth()->user()->name }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-slate-700 no-underline hover:bg-slate-50 transition-colors">
                                <i class="fas fa-user-circle text-[#2874f0] text-sm"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">My Profile</span>
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-slate-700 no-underline hover:bg-slate-50 transition-colors">
                                <i class="fas fa-box text-[#2874f0] text-sm"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">My Orders</span>
                            </a>
                            <div class="border-t border-slate-50 mt-1">
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-rose-500 hover:bg-rose-50 transition-colors border-0 bg-transparent cursor-pointer">
                                        <i class="fas fa-power-off text-sm"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-left">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="bg-white text-[#2874f0] px-8 py-1.5 rounded-sm font-bold text-sm shadow-sm transition-all hover:bg-[#f1f3f6] no-underline focus:outline-none">
                    Login
                </a>
            @endauth

            <button class="md:hidden text-white text-xl focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('profileDropdownBtn');
    const menu = document.getElementById('profileDropdownMenu');

    if (btn && menu) {
        // Toggle on click
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'translate-y-2');
                    menu.classList.add('opacity-100', 'translate-y-0');
                }, 10);
            } else {
                closeDropdown();
            }
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                closeDropdown();
            }
        });

        function closeDropdown() {
            menu.classList.remove('opacity-100', 'translate-y-0');
            menu.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 300);
        }
    }
});
</script>
