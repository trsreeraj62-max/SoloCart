@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Executive Dashboard</h1>
            <p class="text-slate-500 font-medium">Real-time overview of SoloCart performance</p>
        </div>
        <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-xl border border-slate-100">
            <div class="p-2 bg-white rounded-lg shadow-sm">
                <i class="far fa-calendar-alt text-blue-600"></i>
            </div>
            <div class="pr-4">
                <p class="text-[10px] uppercase font-bold text-slate-400 leading-none mb-1">Current Date</p>
                <p class="text-sm font-bold text-slate-700 leading-none">{{ now()->format('D, M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Body -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Analysis Sidebar -->
        <div class="lg:col-span-3 space-y-4">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest px-2">Revenue Focus</h3>
            
            <!-- Daily Revenue Card -->
            <div class="group relative bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-blue-500 transition-all cursor-pointer">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition">
                    <i class="fas fa-sun text-4xl text-blue-600"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 mb-1 uppercase">Today's Revenue</p>
                <div class="text-2xl font-black text-slate-800">${{ number_format($stats['daily_revenue'], 2) }}</div>
                <div class="mt-2 flex items-center text-xs text-green-600 font-bold bg-green-50 w-fit px-2 py-0.5 rounded-full">
                    <i class="fas fa-arrow-up mr-1"></i> Live
                </div>
            </div>

            <!-- Monthly Revenue Card -->
            <div class="group relative bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-purple-500 transition-all cursor-pointer">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition">
                    <i class="fas fa-calendar-check text-4xl text-purple-600"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 mb-1 uppercase">Monthly Sales</p>
                <div class="text-2xl font-black text-slate-800">${{ number_format($stats['monthly_revenue'], 2) }}</div>
                <div class="mt-2 text-xs text-slate-500 font-medium">Total for {{ now()->format('F') }}</div>
            </div>

            <!-- Yearly Revenue Card -->
            <div class="group relative bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-orange-500 transition-all cursor-pointer">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition">
                    <i class="fas fa-chart-line text-4xl text-orange-600"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 mb-1 uppercase">Annual Target</p>
                <div class="text-2xl font-black text-slate-800">${{ number_format($stats['yearly_revenue'], 2) }}</div>
                <div class="mt-2 text-xs text-slate-500 font-medium">Year {{ now()->year }} Progress</div>
            </div>
            
            <div class="pt-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest px-2 mb-4">Store Health</h3>
                <div class="bg-indigo-900 rounded-2xl p-5 text-white shadow-lg overflow-hidden relative">
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                         <i class="fas fa-rocket text-8xl rotate-12"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-white/20 p-2 rounded-lg"><i class="fas fa-users"></i></div>
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-200">Customers</span>
                        </div>
                        <div class="text-3xl font-black mb-1">{{ $stats['users_count'] }}</div>
                        <p class="text-indigo-300 text-xs font-medium">Registered members</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Quick Stats Grid Bar -->
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                 <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
                     <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg"><i class="fas fa-shopping-cart"></i></div>
                     <div>
                         <p class="text-[10px] uppercase font-bold text-slate-400">Total Orders</p>
                         <p class="text-lg font-bold text-slate-800 leading-tight">{{ $stats['orders_count'] }}</p>
                     </div>
                 </div>
                 <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4">
                     <div class="p-3 bg-blue-50 text-blue-600 rounded-lg"><i class="fas fa-box"></i></div>
                     <div>
                         <p class="text-[10px] uppercase font-bold text-slate-400">Inventory</p>
                         <p class="text-lg font-bold text-slate-800 leading-tight">{{ $stats['products_count'] }} Items</p>
                     </div>
                 </div>
                 <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center gap-4 col-span-2 lg:col-span-1">
                     <div class="p-3 bg-amber-50 text-amber-600 rounded-lg"><i class="fas fa-shield-alt"></i></div>
                     <div>
                         <p class="text-[10px] uppercase font-bold text-slate-400">System Status</p>
                         <p class="text-lg font-bold text-slate-800 leading-tight">All Online</p>
                     </div>
                 </div>
            </div>

            <!-- Revenue Analytics Chart -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Growth Trends</h3>
                        <p class="text-sm text-slate-400 font-medium">Daily revenue insights (Last 30 days)</p>
                    </div>
                </div>
                <div class="h-[400px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueData->pluck('date')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($revenueData->pluck('revenue')) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 10,
                    pointHoverBackgroundColor: '#4f46e5',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 14, weight: 'bold' },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '$ ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 7,
                            color: '#94a3b8',
                            font: { size: 11, weight: '600' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 11, weight: '600' },
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
