<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    public $email;
    public $password;

    public function authenticate()
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($validated)) {
            return redirect()->to('/admin/dashboard')->with('success', 'Login Berhasil');
        }

        return redirect()->to('/')->with('error', 'Login Gagal');
    }
};
?>

<div
    class="bg-[#f7f9fb] font-['Inter'] text-[#191c1e] antialiased overflow-hidden selection:bg-[#dbe1ff] selection:text-[#00174a]">

    <!-- main container full screen -->
    <main class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[#f7f9fb]/90 backdrop-blur-sm z-10"></div>
            <img alt="Scholarly background" class="w-full h-full object-cover opacity-10 grayscale"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTrrkdc73KzNAtK9ppAAwGj2sF4kDZ0iES7CHrSS1PY6OKQqeGfO1vL7kx756UYQP_OR3XIt9naiYfADoXldgtWjxBNzxPHqQX3w1AhSoA64TrchwAmB89KArDEyrnul9sWBPC5HXtvGIT6RExotTu0jOjJs6Z_flnaWGILFW2C0jw-x1fw-5llYJNEZmIfkZ7ykISu7xawDfHH5omwMDtBt9pA2gCFT9x6tlCq7L64dp4nWEUvD9ceLhFEAXFMjvarzePFvWv5W8">
        </div>

        <!-- Main card -->
        <div
            class="relative z-20 w-full max-w-[1200px] grid md:grid-cols-2 gap-0 shadow-2xl rounded-xl overflow-hidden bg-white">

            <!-- LEFT PANEL -->
            <div class="hidden md:flex flex-col justify-between p-16 bg-[#00113a] text-white relative">
                <div class="absolute inset-0 opacity-20 pointer-events-none">
                    <div
                        class="absolute top-[-10%] left-[-10%] w-[120%] h-[120%] bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-[#002366] via-transparent to-transparent">
                    </div>
                </div>
                <div class="relative">
                    <h1 class="font-headline text-3xl font-extrabold tracking-tighter mb-8">MyCampus</h1>
                    <div class="space-y-6">
                        <h2 class="font-headline text-5xl font-bold leading-tight tracking-tight">E-Learning
                        </h2>
                        <p class="text-[#758dd5] max-w-sm text-lg leading-relaxed">Unlock the power of knowledge.</p>
                    </div>
                </div>
                <div class="relative mt-12">
                    {{-- Count Mahasiswa --}}
                    <p class="text-sm font-medium tracking-wide text-white/60">More than 10,000 active students.
                    </p>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="p-8 md:p-16 flex flex-col justify-center bg-white">
                <div class="mb-12">
                    <h3 class="font-headline text-3xl font-bold text-[#00113a] tracking-tight mb-2">Welcome Back</h3>
                    <p class="text-[#444650] font-medium">Sign in to your account.</p>
                </div>


                <form class="space-y-6" wire:submit.prevent="authenticate">
                    <!-- Email field -->
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-[#444650] tracking-wide" for="email">Email
                            Address</label>
                        <input
                            class="w-full bg-[#e6e8ea] border-none px-4 py-3.5 rounded-lg focus:ring-2 focus:ring-[#435b9f] focus:bg-white transition-all duration-300 placeholder:text-[#8590a5]/40"
                            id="email" placeholder="scholar@noir.edu" type="text" wire:model="email">
                        @error('email')
                            <span class="text-red-400">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password field -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-semibold text-[#444650] tracking-wide"
                                for="password">Password</label>
                        </div>
                        <div class="relative">
                            <input
                                class="w-full bg-[#e6e8ea] border-none px-4 py-3.5 rounded-lg focus:ring-2 focus:ring-[#435b9f] focus:bg-white transition-all duration-300 pr-12"
                                id="password" placeholder="••••••••" type="password" wire:model="password">
                            @error('password')
                                <span class="text-red-400">{{ $message }}</span>
                            @enderror
                            <button
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[#444650] hover:text-[#00113a]"
                                type="button" id="togglePassword">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button
                        class="w-full bg-gradient-to-br from-[#00113a] to-[#002366] text-white py-4 rounded-xl font-headline font-bold text-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#435b9f] focus:ring-offset-2">
                        Sign In
                    </button>
                </form>

            </div>
        </div>
    </main>

    <script>
        (function() {
            const toggleBtn = document.getElementById('togglePassword');
            if (toggleBtn) {
                const passwordInput = document.getElementById('password');
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    const iconSpan = toggleBtn.querySelector('.material-symbols-outlined');
                    if (iconSpan) {
                        iconSpan.textContent = type === 'password' ? 'visibility' : 'visibility_off';
                    }
                });
            }
        })();
    </script>
</div>
