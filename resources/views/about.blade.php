@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen">
    <!-- Hero Section -->
    <div class="container container-max py-16 px-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 space-y-6">
                <span class="text-[#2874f0] font-black text-xs uppercase tracking-[0.3em]">Our Legacy</span>
                <h1 class="text-5xl font-black text-slate-900 leading-tight">SoloCart: Redefining the <br><span class="text-[#2874f0]">Standard of Shopping.</span></h1>
                <p class="text-lg text-slate-500 font-medium leading-relaxed">
                    SoloCart is India's premier destination for curated minimalist excellence. Born from a vision to simplify the digital acquisition protocol, we provide a seamless bridge between elite craftsmanship and your lifestyle.
                </p>
                <div class="flex gap-4 pt-4">
                    <div class="bg-slate-50 p-6 rounded-sm border-l-4 border-[#2874f0] flex-1">
                        <h4 class="text-2xl font-black text-slate-900">50K+</h4>
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Active Users</p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-sm border-l-4 border-[#2874f0] flex-1">
                        <h4 class="text-2xl font-black text-slate-900">120+</h4>
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Global Brands</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1521333621051-403fdea1ff2e?q=80&w=1470&auto=format&fit=crop" class="w-full h-[500px] object-cover rounded-sm shadow-2xl shadow-slate-200" alt="About US">
                    <div class="absolute -bottom-6 -left-6 bg-[#2874f0] p-8 text-white rounded-sm shadow-xl hidden md:block">
                        <p class="text-3xl font-black m-0 tracking-tighter italic">EST. 2024</p>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-60">Prime Selection Protocol</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="bg-slate-50 py-24">
        <div class="container container-max px-4">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-4xl font-black text-slate-900 italic tracking-tighter">Our Core Mission</h2>
                <div class="h-1 bg-[#2874f0] w-20 mx-auto"></div>
                <p class="text-slate-500 font-medium">We don't just sell pieces; we curate experiences. Every item in our manifest is verified for functional and aesthetic superiority.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-10 rounded-sm shadow-sm border border-slate-100 text-center space-y-4 transition-transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-[#2874f0] rounded-full flex items-center justify-center mx-auto text-2xl"><i class="fas fa-gem"></i></div>
                    <h4 class="font-black text-slate-800 uppercase tracking-widest text-sm">Authenticity Protocol</h4>
                    <p class="text-xs text-slate-400 font-medium">Every product is directly sourced and certified elite by our quality council.</p>
                </div>
                <div class="bg-white p-10 rounded-sm shadow-sm border border-slate-100 text-center space-y-4 transition-transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-[#2874f0] rounded-full flex items-center justify-center mx-auto text-2xl"><i class="fas fa-shipping-fast"></i></div>
                    <h4 class="font-black text-slate-800 uppercase tracking-widest text-sm">Rapid Dispatch</h4>
                    <p class="text-xs text-slate-400 font-medium">State-of-the-art logistics ensuring your acquisition reaches you in record time.</p>
                </div>
                <div class="bg-white p-10 rounded-sm shadow-sm border border-slate-100 text-center space-y-4 transition-transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-50 text-[#2874f0] rounded-full flex items-center justify-center mx-auto text-2xl"><i class="fas fa-headset"></i></div>
                    <h4 class="font-black text-slate-800 uppercase tracking-widest text-sm">Concierge Support</h4>
                    <p class="text-xs text-slate-400 font-medium">Dedicated support line for every member of our prime shopping circle.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
