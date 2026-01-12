@extends('layouts.app')

@section('content')
<div class="profile-page py-16 bg-slate-50 min-h-screen">
    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 1.5rem;">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Account Settings</h1>
            <p class="text-slate-500 font-medium">Manage your personal information and profile picture</p>
        </div>

        @if (session('status'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-600 rounded-2xl font-bold text-center">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Avatar Section -->
                <div class="p-10 border-b border-slate-50 bg-slate-50/50 text-center">
                    <div class="relative inline-block group">
                        <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white mb-4 transition group-hover:opacity-90">
                            @if(auth()->user()->profile_photo)
                                <img id="avatar-preview" src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="w-full h-full object-cover">
                            @else
                                <div id="avatar-placeholder" class="w-full h-full flex items-center justify-center bg-primary/10 text-primary text-4xl font-black">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <img id="avatar-preview" src="" class="w-full h-full object-cover hidden">
                            @endif
                        </div>
                        <label for="profile_photo" class="absolute bottom-4 right-0 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center shadow-lg cursor-pointer hover:scale-110 transition translate-y-2">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </label>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    <!-- Form Fields -->
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:ring-2 focus:ring-primary outline-none transition font-bold text-slate-700" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                            <input type="email" value="{{ auth()->user()->email }}" class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-6 py-4 outline-none font-bold text-slate-400 cursor-not-allowed" disabled>
                            <p class="text-[10px] text-slate-400 font-bold italic ml-1">* Email cannot be changed</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                            <input type="text" value="{{ auth()->user()->phone ?? 'Not provided' }}" class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-6 py-4 outline-none font-bold text-slate-400 cursor-not-allowed" disabled>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Account Role</label>
                            <div class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-6 py-4 font-bold text-slate-400 flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i> {{ ucfirst(auth()->user()->role) }}
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-50 w-full pt-4"></div>

                    <div class="flex items-center justify-between pt-4">
                        <div class="text-slate-400 text-sm font-medium">Looking to change password? Please contact support.</div>
                        <button type="submit" class="py-4 px-10 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:shadow-slate-900/30 transition uppercase tracking-widest text-xs">
                            Update Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const placeholder = document.getElementById('avatar-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
