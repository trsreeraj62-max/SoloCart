<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SoloCart') }} — Flipkart Style Premium</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --fk-blue: #2874f0;
            --fk-yellow: #ffe500;
            --fk-bg: #f1f3f6;
        }
        body {
            background-color: var(--fk-bg);
            font-family: 'Inter', sans-serif;
            color: #212121;
        }
        .container-max {
            max-width: 1280px;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Premium Toast Minimalist */
        #toast-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .fk-toast {
            background: #212121;
            color: white;
            padding: 14px 24px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            animation: toastIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            border-left: 4px solid #2874f0;
        }
        .fk-toast.error { border-left-color: #ff6161; }
        .fk-toast.success { border-left-color: #388e3c; }
        
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100px); }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div id="toast-container"></div>

    <script>
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `fk-toast ${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span>`;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'toastOut 0.4s ease-in forwards';
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        };

        // Flash Messages
        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif
        @if(session('info')) showToast("{{ session('info') }}", 'success'); @endif
        @if(session('status')) showToast("{{ session('status') }}", 'success'); @endif
        
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast("{{ $error }}", 'error');
            @endforeach
        @endif
    </script>

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Main Content - No global container to allow full-width sections --}}
    <main class="min-h-[80vh]">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
