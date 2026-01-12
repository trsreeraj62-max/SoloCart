@extends('layouts.app')

@section('content')
<div class="about-page">
    <!-- Hero Section -->
    <section class="py-20 bg-white">
        <div class="container text-center">
            <h1 class="text-5xl font-black mb-6 text-slate-900">About <span class="text-primary">SoloCart</span></h1>
            <p class="text-xl text-slate-500 max-w-3xl mx-auto leading-relaxed">
                We believe that shopping should be more than just a transaction—it should be an experience of discovering quality and excellence.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 bg-slate-50">
        <div class="container grid md:grid-cols-2 gap-16 items-center">
            <div>
                <div class="bg-primary/10 text-primary font-bold px-4 py-1 rounded-full w-fit mb-4">Our Mission</div>
                <h2 class="text-4xl font-bold mb-6">Revolutionizing Online Shopping</h2>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                    To provide a seamless, secure, and enjoyable platform for discovering premium products. Only at SoloCart do we prioritize quality over quantity. We curate our collections to ensure that every product you see meets our high standards of excellence.
                </p>
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-6 bg-white rounded-2xl shadow-sm">
                        <div class="text-primary text-2xl mb-2 font-black">99%</div>
                        <div class="text-sm font-bold text-slate-400 uppercase">Happy Customers</div>
                    </div>
                    <div class="p-6 bg-white rounded-2xl shadow-sm">
                        <div class="text-primary text-2xl mb-2 font-black">10k+</div>
                        <div class="text-sm font-bold text-slate-400 uppercase">Premium Products</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=800" alt="Our Story Image" class="w-full">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white p-8 rounded-3xl shadow-xl hidden lg:block">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 p-3 rounded-2xl text-green-600"><i class="fas fa-check-circle text-2xl"></i></div>
                        <div>
                            <div class="font-bold text-slate-800">Verified Quality</div>
                            <div class="text-sm text-slate-500">Industry leading standards</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="py-20 bg-white">
        <div class="container">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Why SoloCart?</h2>
                <p class="text-slate-500">Core values that drive our passion</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 rounded-3xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-medal text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Quality First</h3>
                    <p class="text-slate-500">We never compromise on the quality of our products. Every item is hand-selected.</p>
                </div>
                <div class="text-center p-8 rounded-3xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                    <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-heart text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Customer Focus</h3>
                    <p class="text-slate-500">Your satisfaction is our top priority, always. Our support team is here 24/7.</p>
                </div>
                <div class="text-center p-8 rounded-3xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Secure Shopping</h3>
                    <p class="text-slate-500">Your data is always safe with our secure payments and advanced encryption.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .about-page h1, .about-page h2, .about-page h3 { margin: 0; }
    .about-page .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }
    .mx-auto { margin-left: auto; margin-right: auto; }
</style>
@endsection
