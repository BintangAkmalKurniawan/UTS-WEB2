<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Jurusan;
use App\Models\Matakuliah;
use Livewire\WithPagination;

new #[Layout('layouts.afterLogin')] class extends Component {
    use WithPagination;
    public $nama_matakuliah;
    public $id_jurusan;
    public $sks;

    public function store()
    {
        $this->validate([
            'nama_matakuliah' => 'required|string|min:3',
            'id_jurusan' => 'required|exists:jurusan,id',
            'sks' => 'required|numeric|digits_between:1,3',
        ]);

        Matakuliah::create([
            'nama_matakuliah' => $this->nama_matakuliah,
            'id_jurusan' => $this->id_jurusan,
            'sks' => $this->sks,
        ]);

        return redirect()->to('/admin/matakuliah')->with('success', 'Mata Kuliah Berhasil Ditambahkan');
    }

    public function with()
    {
        return [
            'jurusan' => Jurusan::select('id', 'nama_jurusan')->get(),
            'dataMataKuliah' => Matakuliah::with('jurusan:id,nama_jurusan')->oldest()->paginate(5),
        ];
    }
};
?>

<div>
    <main class="py-30 min-h-screen">

        <!-- Content Area -->
        <div class="max-w-7xl mx-auto w-ful">

            <!-- Page Header / Hero Section -->
            <section class="space-y-4 max-w-3xl mb-5">
                <p class="text-[#758dd5] font-semibold tracking-[0.2em] text-xs uppercase mb-2">Learning
                    MyCampus
                </p>
                <h3 class="text-5xl font-black font-headline text-[#00113a] leading-tight tracking-tighter">
                    Manajemen Mata Kuliah
                </h3>
            </section>

            <!-- Grid Layout for Form and Decorative Element -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Form Section (Bento Card Style) -->
                <div class="lg:col-span-2 bg-white rounded-xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="material-symbols-outlined text-[#435b9f]">post_add</span>
                        <h4 class="font-headline font-bold text-xl text-[#00113a]">Registrasi Mata Kuliah Baru</h4>
                    </div>
                    <form class="space-y-6" wire:submit.prevent="store">
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-[#444650] px-1">Nama Mata
                                    Kuliah</label>
                                <input type="text" placeholder="Ex: Kecerdasan Buatan" wire:model="nama_matakuliah"
                                    class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#435b9f]/30 focus:bg-white transition-all font-body text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-[#444650] px-1">Beban
                                    SKS</label>
                                <select wire:model="sks"
                                    class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#435b9f]/30 focus:bg-white transition-all font-body text-sm">
                                    <option value="">Pilih SKS</option>
                                    <option value="1">1 SKS</option>
                                    <option value="2">2 SKS</option>
                                    <option value="3">3 SKS</option>
                                    <option value="4">4 SKS</option>
                                    <option value="5">5 SKS</option>
                                    <option value="6">6 SKS</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-xs font-bold uppercase tracking-widest text-[#444650] px-1">Jurusan</label>
                                <select wire:model="id_jurusan"
                                    class="w-full bg-[#e6e8ea] border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#435b9f]/30 focus:bg-white transition-all font-body text-sm">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusan as $jur)
                                        <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }}</option>
                                    @endforeach

                                </select>
                            </div>

                        </div>
                        <div class="pt-4 flex justify-end">
                            <button type="submit"
                                class="bg-gradient-to-br from-[#00113a] to-[#002366] text-white px-8 py-3 rounded-lg font-bold flex items-center gap-2 hover:shadow-xl transition-all active:scale-95">
                                <span>Simpan Mata Kuliah</span>
                                <span class="material-symbols-outlined text-sm">send</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Section: Daftar Mata Kuliah -->
            <section class="space-y-6 mt-20">
                <div class="flex items-center justify-between">
                    <h4 class="font-headline font-bold text-2xl text-[#00113a]">Daftar Mata Kuliah Terdaftar</h4>
                    <button class="text-[#435b9f] font-bold text-sm flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-sm">download</span>
                        Export PDF
                    </button>
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
                                    Nama Mata Kuliah</th>
                                <th
                                    class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30">
                                    SKS</th>
                                <th
                                    class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30">
                                    Jurusan</th>
                                <th
                                    class="px-6 py-5 text-xs font-bold uppercase tracking-widest text-[#444650] border-b border-[#c5c6d2]/30 text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#c5c6d2]/20">
                            <!-- Data Mata Kuliah -->
                            @forelse ($dataMataKuliah as $matkul)
                                <tr class="bg-white/40 hover:bg-white/80 transition-colors group">
                                    <td class="px-6 py-5 font-headline font-bold text-[#00113a]">CS-{{ $matkul->id }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-medium text-[#191c1e]">{{ $matkul->nama_matakuliah }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#e6e8ea] text-[#00113a]">{{ $matkul->sks }}
                                            SKS</span>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-[#444650]">{{ $matkul->jurusan->nama_jurusan }}
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-100 transition-opacity">
                                            <button class="p-2 hover:bg-white rounded-lg text-[#435b9f] transition-all"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit_note</span>
                                            </button>
                                            <button
                                                class="p-2 hover:bg-[#ffdad6] rounded-lg text-[#ba1a1a] transition-all"
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

                <!-- Pagination -->
                <div class="flex items-center justify-between px-2 text-sm text-[#444650]">
                    <span>Menampilkan {{ $dataMataKuliah->firstItem() }} sampai {{ $dataMataKuliah->lastItem() }} dari
                        {{ $dataMataKuliah->total() }} mata kuliah</span>
                    <div>
                        {{ $dataMataKuliah->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
