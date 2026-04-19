<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Jurusan;

new #[Layout('layouts.afterLogin')] class extends Component {
    use WithPagination;

    public $nama_jurusan;
    public $akreditasi;

    public function cancel()
    {
        $this->nama_jurusan = '';
        $this->akreditasi = '';
    }

    public function store()
    {
        $this->validate([
            'nama_jurusan' => 'required|string|min:3',
            'akreditasi' => 'required|string|in:A,B,AB,C',
        ]);

        Jurusan::create([
            'nama_jurusan' => $this->nama_jurusan,
            'akreditasi' => $this->akreditasi,
        ]);

        return redirect()->to('/admin/jurusan')->with('success', 'Jurusan Berhasil Ditambahkan');
    }

    public function with(): array
    {
        return [
            'jurusans' => Jurusan::select('id', 'nama_jurusan', 'akreditasi')
                ->withSum('matakuliah', 'sks')
                ->latest('id')
                ->paginate(5),
        ];
    }
};
?>

<div>
    <main class="flex-1 flex flex-col min-w-0 bg-[#f7f9fb]">

        <!-- Editorial Content Area -->
        <div class="my-30 max-w-7xl mx-auto w-full">

            <!-- Hero Title -->
            <div class="mb-10">
                <p class="text-[#758dd5] font-semibold tracking-[0.2em] text-xs uppercase mb-2">Learning
                    MyCampus
                </p>
                <h1 class="text-5xl font-extrabold tracking-tight text-[#00113a] mb-4 font-headline">Tambah
                    Jurusan</h1>
            </div>

            <!-- Main Asymmetric Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start mb-10">

                <!-- Form Section (Focus) -->
                <div class="lg:col-span-7 bg-white rounded-xl p-8 lg:p-10 shadow-sm border border-slate-100">
                    <form class="space-y-8" wire:submit.prevent="store">
                        <div class="space-y-6">
                            <!-- Department Name -->
                            <div class="group">
                                <label
                                    class="block text-xs font-bold uppercase tracking-widest text-[#444650] mb-2 ml-1">Department
                                    Name</label>
                                <input type="text" placeholder="e.g. Digital Humanities &amp; Arts"
                                    wire:model="nama_jurusan"
                                    class="w-full bg-[#e6e8ea] border-none border-b-2 border-transparent focus:border-[#435b9f] focus:ring-0 focus:bg-white transition-all px-4 py-4 rounded-lg text-[#00113a] font-medium placeholder:text-slate-400">
                            </div>

                            <!-- Faculty and Head of Department -->
                            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                                <div class="group">
                                    <label
                                        class="block text-xs font-bold uppercase tracking-widest text-[#444650] mb-2 ml-1">Faculty</label>
                                    <select wire:model="akreditasi"
                                        class="w-full bg-[#e6e8ea] border-none border-b-2 border-transparent focus:border-[#435b9f] focus:ring-0 focus:bg-white transition-all px-4 py-4 rounded-lg text-[#00113a] font-medium">
                                        <option value="">Pilih Akreditasi</option>
                                        <option value="A">A (sangat baik)</option>
                                        <option value="AB">AB (baik)</option>
                                        <option value="B">B (cukup baik)</option>
                                        <option value="C">C (kurang baik)</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="pt-4 flex items-center justify-between">
                            <button type="button" wire:click="cancel"
                                class="text-[#00113a] font-semibold text-sm hover:opacity-70 transition-opacity">Discard
                                Changes</button>
                            <button type="submit"
                                class="bg-gradient-to-br from-[#00113a] to-[#002366] text-white px-8 py-4 rounded-lg font-bold text-sm hover:shadow-lg transition-all active:scale-95">
                                Create Department
                            </button>
                        </div>
                    </form>
                </div>


            </div>
            <div class="bg-[#eceef0] rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/50">
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30">
                                Kode</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30">
                                Nama Jurusan</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30">
                                SKS</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30">
                                Akreditasi</th>
                            <th
                                class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30 text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#c5c6d2]/20">
                        <!-- Data Mata Kuliah -->
                        @forelse ($jurusans as $jur)
                            <tr class="bg-white/40 hover:bg-white/80 transition-colors group"
                                wire:key="{{ $jur->id }}">
                                <td class="px-6 py-5 font-headline font-bold text-[#00113a]">JUR-{{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-[#191c1e]">{{ $jur->nama_jurusan }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#e6e8ea] text-[#00113a]">
                                        {{ $jur->matakuliah_sum_sks ?? 0 }} SKS
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-[#444650]">{{ $jur->akreditasi }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-100 transition-opacity">
                                        <button class="p-2 hover:bg-white rounded-lg text-[#435b9f] transition-all"
                                            title="Edit">
                                            <span class="material-symbols-outlined text-lg">edit_note</span>
                                        </button>
                                        <button class="p-2 hover:bg-[#ffdad6] rounded-lg text-[#ba1a1a] transition-all"
                                            title="Delete">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-[#444650]">
                                    Data mata kuliah belum ada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between px-2 text-sm text-[#444650]">
                <span>
                    Menampilkan {{ $jurusans->firstItem() ?? 0 }} sampai {{ $jurusans->lastItem() ?? 0 }} dari
                    {{ $jurusans->total() }} jurusan
                </span>
                <div>
                    {{ $jurusans->links() }}
                </div>
            </div>
        </div>

        <!-- Horizontal Progress Horizon Line (light theme) -->
        <div class="fixed top-0 left-0 w-full h-[2px] bg-[#e0e3e5] z-[60]">
            <div class="h-full bg-[#00113a] w-1/3 transition-all"></div>
        </div>
    </main>
</div>
