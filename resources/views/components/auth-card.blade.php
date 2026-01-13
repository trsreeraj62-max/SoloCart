<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white shadow-lg rounded-lg p-6 w-full" style="max-width: 450px;">
        <h2 class="text-2xl font-bold text-center mb-4">
            {{ $title ?? '' }}
        </h2>

        {{ $slot }}
    </div>
</div>
