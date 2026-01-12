@extends('layouts.app')

@section('content')
<div class="contact-page bg-slate-50 min-h-screen py-20">
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-black text-slate-900 mb-4">Contact Us</h1>
            <p class="text-slate-500 text-lg">Have questions? We'd love to hear from you.</p>
        </div>

        <div class="grid md:grid-cols-12 gap-8">
            <!-- Form Card -->
            <div class="md:col-span-7">
                <div class="bg-white p-10 rounded-[2rem] shadow-xl border border-slate-100">
                    <form action="#" method="POST" class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Your Name</label>
                                <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary outline-none transition" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary outline-none transition" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                            <input type="text" name="subject" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Message</label>
                            <textarea name="message" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary outline-none transition" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-primary/30 transition transform hover:-translate-y-1">
                            Send Message <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Column -->
            <div class="md:col-span-5 flex flex-col justify-between">
                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                        <h3 class="text-xl font-bold mb-6 text-slate-800">Other ways to reach us</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-blue-50 p-4 rounded-2xl text-blue-600"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400 uppercase mb-1">Email us</div>
                                    <div class="font-bold text-slate-700">support@solocart.com</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-purple-50 p-4 rounded-2xl text-purple-600"><i class="fas fa-phone"></i></div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400 uppercase mb-1">Call us</div>
                                    <div class="font-bold text-slate-700">+1 (555) 123-4567</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="bg-emerald-50 p-4 rounded-2xl text-emerald-600"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400 uppercase mb-1">Visit us</div>
                                    <div class="font-bold text-slate-700">123 Commerce St, Shopping City, SC 12345</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Socials Card -->
                    <div class="bg-slate-900 p-8 rounded-[2rem] text-white">
                        <h4 class="font-bold mb-4">Follow our journey</h4>
                        <div class="flex gap-4">
                            <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
