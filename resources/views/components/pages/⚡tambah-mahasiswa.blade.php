<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Mahasiswa;
use App\Models\Jurusan;

new #[Layout('layouts.afterLogin')] class extends Component {
    public $nama_mahasiswa;
    public $nim;
    public $id_jurusan;
    public $jurusan = [];

    public function store()
    {
        $this->validate([
            'nama_mahasiswa' => 'required|string|min:3',
            'nim' => 'required|numeric|digits_between:8,15',
            'id_jurusan' => 'required|exists:jurusan,id',
        ]);

        Mahasiswa::create([
            'nama' => $this->nama_mahasiswa,
            'nim' => $this->nim,
            'id_jurusan' => $this->id_jurusan,
        ]);

        return redirect()->to('/admin/mahasiswa')->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    public function mount()
    {
        $this->jurusan = Jurusan::select('nama_jurusan', 'id')->get();
    }
};
?>

<div>
    <main class="flex-1 flex flex-col min-w-0">
        <!-- Content Area: Registration Form -->
        <div class="mt-30 max-w-7xl w-full mx-auto">

            <!-- Header Section -->
            <div class="mb-12">
                <p class="text-[#758dd5] font-semibold tracking-[0.2em] text-xs uppercase mb-2">Learning
                    MyCampus
                </p>
                <h1 class="text-5xl font-extrabold tracking-tight text-[#00113a] mb-4 leading-tight font-headline">
                    Registrasi<br />Mahasiswa Baru
                </h1>
            </div>

            <!-- Bento Form Grid -->
            <form wire:submit.prevent="store" class="grid grid-cols-1 gap-8 items-start">

                <!-- Left Column: Photo Upload + Info Card -->
                {{-- <div class="col-span-12 lg:col-span-4 space-y-8">
                    <!-- Photo upload card -->
                    <div class="bg-white rounded-xl p-8 shadow-sm border border-slate-100 transition-all">
                        <label class="block text-sm font-semibold text-[#00113a] mb-6 uppercase tracking-wider">Foto
                            Profil</label>
                        <div class="flex flex-col items-center">
                            <div
                                class="w-48 h-48 rounded-xl bg-[#f2f4f6] flex flex-col items-center justify-center border-2 border-dashed border-[#c5c6d2]/50 group hover:border-[#435b9f] transition-colors cursor-pointer overflow-hidden relative">
                                <span class="material-symbols-outlined text-4xl text-[#8590a5] mb-2">add_a_photo</span>
                                <span class="text-xs text-[#8590a5] font-medium">Unggah Foto</span>
                                <input type="file"
                                    class="absolute inset-0 bg-[#00113a]/5 opacity-0 group-hover:opacity-100 transition-opacity">
                                </input>
                            </div>
                            <p class="mt-4 text-xs text-[#8590a5] text-center px-4 leading-relaxed">
                                Gunakan foto formal dengan latar belakang polos. Maksimal 2MB (JPG/PNG).
                            </p>
                        </div>
                    </div>
                </div> --}}

                <!-- Right Column: Main Form Fields -->
                <div class="col-span-12 lg:col-span-8 bg-white rounded-xl p-10 shadow-sm border border-slate-100">
                    <div class="grid grid-cols-2 gap-x-10 gap-y-8">
                        <!-- Nama Lengkap -->
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">Nama Lengkap</label>
                            <input type="text" placeholder="Contoh: Adrian Syahputra" wire:model="nama_mahasiswa"
                                class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:bg-white focus:ring-2 focus:ring-[#435b9f]/30 transition-all placeholder:text-[#8590a5]/60">
                        </div>
                        <!-- NIM -->
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">NIM (Student ID)</label>
                            <input type="text" placeholder="2024001001" wire:model="nim"
                                class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:bg-white focus:ring-2 focus:ring-[#435b9f]/30 transition-all placeholder:text-[#8590a5]/60">
                        </div>

                        <!-- Jurusan -->
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">Jurusan</label>
                            <select wire:model="id_jurusan"
                                class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:bg-white focus:ring-2 focus:ring-[#435b9f]/30 transition-all text-[#191c1e]">
                                <option value="">Pilih Jurusan</option>
                                @foreach ($jurusan as $jur)
                                    <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <!-- Email Akademik -->
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">Email Akademik</label>
                            <input type="email" placeholder="adrian@scholar.ac.id"
                                class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:bg-white focus:ring-2 focus:ring-[#435b9f]/30 transition-all placeholder:text-[#8590a5]/60">
                        </div>
                        <!-- Nomor Telepon -->
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">Nomor Telepon</label>
                            <input type="tel" placeholder="+62 812 3456 7890"
                                class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:bg-white focus:ring-2 focus:ring-[#435b9f]/30 transition-all placeholder:text-[#8590a5]/60">
                        </div> --}}
                        <!-- Semester with increment/decrement buttons -->
                        {{-- <div class="col-span-1">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">Semester</label>
                            <div class="flex items-center space-x-4">
                                <button type="button"
                                    class="w-10 h-10 rounded-lg bg-[#e6e8ea] flex items-center justify-center text-[#00113a] hover:bg-[#d8dadc] transition-colors font-bold text-lg"
                                    id="semesterMinus">-</button>
                                <span class="text-lg font-bold text-[#00113a] w-8 text-center"
                                    id="semesterValue">1</span>
                                <button type="button"
                                    class="w-10 h-10 rounded-lg bg-[#e6e8ea] flex items-center justify-center text-[#00113a] hover:bg-[#d8dadc] transition-colors font-bold text-lg"
                                    id="semesterPlus">+</button>
                            </div>
                        </div>
                        <!-- Status Mahasiswa Toggle -->
                        <div class="col-span-1">
                            <label class="block text-sm font-semibold text-[#00113a] mb-2">Status Mahasiswa</label>
                            <div class="flex items-center space-x-2 bg-[#e6e8ea] p-1 rounded-lg w-fit">
                                <button type="button"
                                    class="px-4 py-1.5 rounded-md bg-white shadow-sm text-xs font-bold text-[#00113a] transition-all"
                                    id="statusActiveBtn">Aktif</button>
                                <button type="button"
                                    class="px-4 py-1.5 rounded-md text-xs font-medium text-slate-500 transition-all"
                                    id="statusInactiveBtn">Cuti</button>
                            </div>
                            <input type="hidden" id="statusField" value="active">
                        </div> --}}
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-16 flex items-center justify-end space-x-6 border-t-0 pt-0">
                        <button type="button"
                            class="text-[#00113a] font-semibold hover:opacity-70 transition-opacity">Batalkan</button>
                        <button type="submit"
                            class="bg-gradient-to-br from-[#00113a] to-[#002366] text-white px-10 py-3.5 rounded-lg font-bold shadow-md hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center">
                            <span class="mr-2">Simpan Data Mahasiswa</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
