@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Order Management</h1>
            <p class="text-slate-500 font-medium">Coordinate logistics and monitor fulfillment status</p>
        </div>
        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative group w-full md:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Order ID..." class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all font-bold text-slate-700">
            <i class="fas fa-search absolute left-5 top-4 text-slate-300 group-focus-within:text-primary transition"></i>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500 text-white p-3 rounded-xl shadow-lg shadow-emerald-500/10 flex items-center gap-3 animate-in slide-in-from-top duration-300 max-w-fit mb-4">
        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs"><i class="fas fa-check"></i></div>
        <p class="font-bold text-sm m-0">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-500 text-white p-3 rounded-xl shadow-lg shadow-rose-500/10 flex items-center gap-3 animate-in slide-in-from-top duration-300 max-w-fit mb-4">
        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs"><i class="fas fa-times"></i></div>
        <p class="font-bold text-sm m-0">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Content Card -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Reference</th>
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Customer Identity</th>
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Logistics Status</th>
                        <th class="p-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Transaction</th>
                        <th class="p-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Timeline</th>
                        <th class="p-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Operational Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition duration-300 group">
                        <td class="p-6">
                            <span class="font-black text-slate-900 bg-slate-100 px-3 py-1 rounded-lg text-sm">#{{ $order->id }}</span>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 text-primary rounded-full flex items-center justify-center font-black text-xs">
                                    {{ substr($order->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 leading-none mb-1">{{ $order->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $order->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black tracking-widest uppercase
                                {{ $order->status == 'delivered' ? 'bg-emerald-100 text-emerald-600' : 
                                  ($order->status == 'cancelled' ? 'bg-rose-100 text-rose-600' : 
                                  ($order->status == 'shipped' ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600')) }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                        </td>
                        <td class="p-6 text-right">
                            <div class="font-black text-slate-900 tracking-tighter text-lg italic">${{ number_format($order->total, 2) }}</div>
                            <div class="text-[9px] text-slate-300 font-black uppercase">{{ $order->payment_method }} Paid</div>
                        </td>
                        <td class="p-6 text-right">
                            <div class="text-sm font-bold text-slate-600">{{ $order->created_at->format('d M, Y') }}</div>
                            <div class="text-[10px] text-slate-400 font-medium">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex items-center justify-end gap-3 transition duration-300">
                                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <select name="status" class="bg-white border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-primary/20" onchange="this.form.submit()">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $order->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="packed" {{ $order->status == 'packed' ? 'selected' : '' }}>Packed</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="out_for_delivery" {{ $order->status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                                <a href="{{ route('orders.show', $order->id) }}" target="_blank" class="w-8 h-8 bg-slate-100 text-slate-400 rounded-lg flex items-center justify-center hover:bg-primary hover:text-white transition">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($orders->count() == 0)
        <div class="p-20 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
                <i class="fas fa-inbox text-3xl"></i>
            </div>
            <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">No orders found matching your criteria</p>
        </div>
        @endif

        @if($orders->hasPages())
        <div class="p-8 border-t border-slate-50 bg-slate-50/50">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
