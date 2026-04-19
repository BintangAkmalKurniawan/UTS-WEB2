<?php

use Livewire\Component;

new class extends Component {
    public $nama;

    public function mount()
    {
        $this->nama = auth()->user()?->name;
    }
};
?>

<div>
    <header
        class="fixed top-0 right-0 w-full z-40 bg-slate-50/80 backdrop-blur-xl flex justify-between items-center px-12 h-20 ml-64 border-b border-slate-200/50">
        <!-- Search bar -->
        <div class="max-w-xl"
            :class="sidebarOpen ? 'lg:pl-64 pl-64 transition-all duration-500' : 'lg:pl-0 pl-0 transition-all duration-500'">
            <button @click="sidebarOpen = !sidebarOpen"
                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>

        <!-- Right side: notifications + profile -->
        <div class="flex items-center gap-6">
            <div class="h-8 w-[1px] bg-[#c5c6d2]/30 mx-2"></div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-[#00113a] tracking-tight">
                        {{ $this->nama ? '' . $this->nama . '' : 'admin' }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest">Institution Lead</p>
                </div>
                <img alt="Administrator profile avatar"
                    class="w-10 h-10 rounded-full object-cover border-2 border-[#002366]/20"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAb8criqmgnLYO7bCfbgPdgJv_RF-FrcSGsl5eEgP7S2U14LAAjL0gxoIXdnqCqXE55CEOrbUV5ryZek1rm1qOOcsKEYjQPPnEpGnEieCBhRZ18XGN9WIBlMxS8yEfM5OUp8Xx-SvR5OSW_CQAjzzvgGkSU-YTa2Cy-0pcAhUeBe-d4Bmx3psItILph-sml2xAUsZyExkmjQUGsEOcJVsz0OEIaxW7eogCmIM3E07IS4f8VWFAKNzr-5WZx_Vd7OVSRG27hRQkDlfU">
            </div>
        </div>
    </header>
</div>
