@extends('layouts.admin')

@section('content')
    <h1 class="font-bold text-3xl mb-8">Dashboard</h1>
    
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card p-6 border-l-4" style="border-left: 4px solid var(--primary);">
            <h3 class="text-muted text-sm font-bold uppercase tracking-wider">Total Revenue</h3>
            <p class="text-3xl font-bold mt-2 text-primary">${{ number_format($stats['daily_revenue'], 2) }}</p>
            <small class="text-xs text-muted">Today's earnings</small>
        </div>
        <div class="card p-6 border-l-4" style="border-left: 4px solid var(--secondary);">
            <h3 class="text-muted text-sm font-bold uppercase tracking-wider">Total Orders</h3>
            <p class="text-3xl font-bold mt-2">{{ $stats['orders_count'] }}</p>
            <small class="text-xs text-muted">Lifetime orders</small>
        </div>
        <div class="card p-6">
            <h3 class="text-muted text-sm font-bold uppercase tracking-wider">Total Products</h3>
            <p class="text-3xl font-bold mt-2">{{ $stats['products_count'] }}</p>
        </div>
        <div class="card p-6">
            <h3 class="text-muted text-sm font-bold uppercase tracking-wider">Registered Users</h3>
            <p class="text-3xl font-bold mt-2">{{ $stats['users_count'] }}</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="card p-6">
        <h3 class="font-bold mb-4">Revenue Analytics (Last 7 Days)</h3>
        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const labels = {!! json_encode($revenueData->pluck('date')) !!};
        const data = {!! json_encode($revenueData->pluck('revenue')) !!};
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: data,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
