@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-12">
    <div class="container container-max px-4">
        
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="bg-white p-10 rounded-sm shadow-sm border border-slate-100 h-full flex flex-col justify-between">
                    <div class="space-y-6">
                        <span class="text-[#2874f0] font-black text-xs uppercase tracking-[0.3em]">Communication Protocol</span>
                        <h2 class="text-4xl font-black text-slate-900 tracking-tighter italic">Connect With US.</h2>
                        <p class="text-slate-500 font-medium">Have questions or feedback? Our operative team is standing by to assist with your acquisition inquiries.</p>
                        
                        <div class="space-y-8 pt-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded flex items-center justify-center text-[#2874f0] mt-1"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Central Ops</p>
                                    <p class="text-sm font-bold text-slate-800">102 Sky Tower, Tech Park Road,<br>Bangalore, KA 560001</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded flex items-center justify-center text-[#2874f0] mt-1"><i class="fas fa-phone"></i></div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Direct Line</p>
                                    <p class="text-sm font-bold text-slate-800">+91 1800-SOLO-CART</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded flex items-center justify-center text-[#2874f0] mt-1"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Digital Signal</p>
                                    <p class="text-sm font-bold text-slate-800">ops@solocart.premium</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-10 border-t border-slate-50 mt-10">
                        <p class="text-[10px] font-black uppercase text-slate-300 tracking-widest mb-4">Social Signal Nodes</p>
                        <div class="flex gap-4">
                            <a href="#" class="w-10 h-10 bg-[#2874f0] text-white rounded flex items-center justify-center hover:bg-[#1266ec] transition-colors"><i class="fab fa-facebook-f text-sm"></i></a>
                            <a href="#" class="w-10 h-10 bg-[#2874f0] text-white rounded flex items-center justify-center hover:bg-[#1266ec] transition-colors"><i class="fab fa-instagram text-sm"></i></a>
                            <a href="#" class="w-10 h-10 bg-[#2874f0] text-white rounded flex items-center justify-center hover:bg-[#1266ec] transition-colors"><i class="fab fa-twitter text-sm"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="bg-white p-10 rounded-sm shadow-sm border border-slate-100">
                    <form id="contactForm" action="/api/contact" method="POST" class="space-y-6">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Your Name</label>
                                <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-medium focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all">
                            </div>
                            <div class="col-md-6">
                                <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Email Address</label>
                                <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-medium focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Subject</label>
                            <input type="text" name="subject" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-medium focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Message Protocol</label>
                            <textarea name="message" rows="6" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-medium focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all" placeholder="Enter your query here..."></textarea>
                        </div>
                        <button type="submit" class="bg-[#2874f0] text-white px-10 py-4 rounded-sm text-sm font-black uppercase tracking-widest hover:bg-[#1266ec] w-full md:w-auto shadow-lg shadow-blue-100">
                            Process Transmission
                        </button>
                    </form>
                </div>
            </div>

            <script>
            document.getElementById('contactForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerText;
                
                btn.disabled = true;
                btn.innerText = 'Transmitting...';
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.showToast(data.message || "Message Received. We will get back to you soon!", 'success');
                        form.reset();
                    } else {
                        window.showToast(data.message || "Transmission failed", 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showToast("An error occurred during transmission", 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
            });
            </script>
        </div>

        <!-- Mini Map Placeholder / Banner -->
        <div class="mt-20 h-80 rounded-sm overflow-hidden shadow-sm relative grayscale hover:grayscale-0 transition-all duration-700">
            <img src="https://images.unsplash.com/photo-1423666639041-f56000c27a9a?q=80&w=1470&auto=format&fit=crop" class="w-full h-full object-cover" alt="Support Banner">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 to-transparent flex items-end p-12">
                <p class="text-white text-3xl font-black italic tracking-tighter tracking-widest">GLOBAL OPERATIVE SUPPORT</p>
            </div>
        </div>

    </div>
</div>
@endsection
