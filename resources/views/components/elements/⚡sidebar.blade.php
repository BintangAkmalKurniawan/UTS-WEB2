<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }
};
?>

<div>
    <aside
        class="fixed left-0 top-0 h-full w-64 z-50 bg-slate-50 flex flex-col py-8 gap-y-6 shadow-sm border-r border-slate-200"
        :class="sidebarOpen ? 'translate-x-0 duration-500' : '-translate-x-full duration-500'">
        <!-- Logo area -->
        <div class="px-8 flex flex-col gap-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#002366] flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl">account_balance</span>
                </div>
                <h1 class="text-2xl font-black text-[#00113a] uppercase tracking-tighter">MyCampus</h1>
            </div>
        </div>

        <nav class="flex-1 px-3 mt-4 space-y-1">

            <x-navlink href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
                Dashboard
            </x-navlink>

            <x-navlink href="{{ route('data-mahasiswa') }}" :active="request()->routeIs('data-mahasiswa')" icon="groups">
                Data Mahasiswa
            </x-navlink>

            <x-navlink href="{{ route('tambah-mahasiswa') }}" :active="request()->routeIs('tambah-mahasiswa')" icon="person_add">
                Tambah Mahasiswa
            </x-navlink>

            <x-navlink href="{{ route('tambah-jurusan') }}" :active="request()->routeIs('tambah-jurusan')" icon="account_balance">
                Tambah Jurusan
            </x-navlink>

            <x-navlink href="{{ route('matakuliah') }}" :active="request()->routeIs('matakuliah')" icon="menu_book">
                Mata Kuliah
            </x-navlink>

        </nav>

        <!-- Bottom CTA button (gradient) -->
        <div class="px-6">
            <button wire:click="logout"
                class="w-full bg-gradient-to-br from-[#00113a] to-[#002366] text-white py-3 rounded-xl font-manrope font-semibold text-sm hover:shadow-xl transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">logout</span>
                Logout
            </button>
        </div>
    </aside>
</div>
