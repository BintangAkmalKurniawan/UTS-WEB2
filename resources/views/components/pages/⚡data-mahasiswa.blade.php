<?php

use Livewire\Component;
use App\Models\Mahasiswa;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $perPage = 5;

    public function with(): array
    {
        return [
            'mahasiswa' => Mahasiswa::with('jurusan:id,nama_jurusan')->latest()->paginate($this->perPage),
        ];
    }

    public function render()
    {
        return $this->view()->layout('layouts.afterLogin')->title('Data Mahasiswa');
    }
};
?>
<div>
    <div class="bg-[#f7f9fb] text-[#191c1e] antialiased">
        <main class="pt-20 min-h-screen">
            <!-- Main Content Canvas: Data Mahasiswa Table -->
            <div class=" mt-5 max-w-7xl mx-auto w-full">
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                    <div>
                        <p class="text-[#758dd5] font-semibold tracking-[0.2em] text-xs uppercase mb-2">Student
                            MyCampus
                        </p>
                        <h2 class="text-5xl font-extrabold font-manrope tracking-tight text-[#00113a]">Data Mahasiswa
                        </h2>
                    </div>
                    <a href="{{ route('tambah-mahasiswa') }}"
                        class="bg-gradient-to-br from-[#00113a] to-[#002366] text-white px-8 py-3 rounded-md font-manrope font-semibold text-sm hover:shadow-lg transition-all flex items-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                        Tambah Mahasiswa
                    </a>
                </div>

                <!-- Table Container (light card style) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">

                    <!-- Filters & Search Toolbar -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 p-6 justify-between items-center border-b border-[#eceef0]">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="relative flex-1 sm:w-64">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757682] text-sm">filter_list</span>
                                <select
                                    class="w-full bg-[#f2f4f6] border-none rounded-lg py-2.5 pl-10 pr-4 text-xs font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#435b9f]/30 appearance-none">
                                    <option>All Departments</option>
                                    <option>Informatics Engineering</option>
                                    <option>Visual Communication Design</option>
                                    <option>Information Systems</option>
                                </select>
                            </div>
                            <div class="relative flex-1 sm:w-64">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757682] text-sm">sort_by_alpha</span>
                                <select
                                    class="w-full bg-[#f2f4f6] border-none rounded-lg py-2.5 pl-10 pr-4 text-xs font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#435b9f]/30 appearance-none">
                                    <option>Sort by Name</option>
                                    <option>Sort by NIM</option>
                                    <option>Sort by Semester</option>
                                </select>
                            </div>
                            <div class="relative flex-1 sm:w-64">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757682] text-sm">sort_by_alpha</span>
                                <select wire:model.live="perPage"
                                    class="w-full bg-[#f2f4f6] border-none rounded-lg py-2.5 pl-10 pr-4 text-xs font-semibold text-[#191c1e] focus:ring-2 focus:ring-[#435b9f]/30 appearance-none">
                                    <option value="0">Jumlah Menampilkan Data</option>
                                    <option value="5">5 Data</option>
                                    <option value="10">10 Data</option>
                                    <option value="50">50 Data</option>
                                    <option value="100">100 Data</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#eceef0]/50">
                                    <th
                                        class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-widest text-[#444650]/70 border-b border-[#c5c6d2]/20">
                                        Scholars</th>
                                    <th
                                        class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-widest text-[#444650]/70 border-b border-[#c5c6d2]/20">
                                        NIM</th>
                                    <th
                                        class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-widest text-[#444650]/70 border-b border-[#c5c6d2]/20">
                                        Department</th>

                                    <th
                                        class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-widest text-[#444650]/70 border-b border-[#c5c6d2]/20">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-widest text-[#444650]/70 border-b border-[#c5c6d2]/20 text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#eceef0]">
                                @forelse ($mahasiswa as $data)
                                    <tr class="group hover:bg-[#f2f4f6] transition-colors"
                                        wire:key="{{ $data->id }}">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-4">
                                                <img alt="Student"
                                                    class="w-10 h-10 rounded-full object-cover shadow-sm"
                                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZE9GXw6h7XosjKtF4tz_2AbR0L05mr7uRcedKdT_gWuhkZfVXr4wATvfAI4I0AvZIdwlhJE6TxKuwF2AkzRNYTKU7jVl8B9rCusG5IQVDCEJZzcf_B45Y5t4WAjGOrjVqvljCtqpIMJ4Gx4Mchs1Lp9GLJu5s13zRMbqduYgKRFbMDVHQxVKvinD-St7RMFEwzVSwm0prdOXa9yD6xcNBFvVnXZbqqH7euddcI23A0ipaHHBqCqoe4fLAhzcfgb1htnbjhg12dS0">

                                                <div>
                                                    <p class="font-manrope font-bold text-sm text-[#00113a]">
                                                        {{ $data->nama }}
                                                    </p>
                                                    <p class="text-[11px] text-[#444650]">
                                                        {{ $data->nama }}.@univ.edu
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 text-sm font-medium text-[#5f5e5e]">
                                            {{ $data->nim }}
                                        </td>

                                        <td class="px-6 py-5 text-sm text-[#191c1e]">
                                            {{ $data->jurusan->nama_jurusan }}
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="text-xs font-semibold text-emerald-700">Active</span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    class="p-2 hover:bg-white rounded-lg text-[#00113a] transition-colors">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </button>
                                                <button
                                                    class="p-2 hover:bg-[#ffdad6] rounded-lg text-[#ba1a1a] transition-colors">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-[#444650]">
                                            Data mahasiswa belum ada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 flex items-center justify-between border-t border-[#eceef0]">

                        <div class="flex items-center justify-between mt-4 w-full">
                            <div class="text-sm text-gray-600">
                                Menampilkan {{ $mahasiswa->firstItem() }} sampai {{ $mahasiswa->lastItem() }} dari
                                {{ $mahasiswa->total() }} data
                            </div>

                            <div>
                                {{ $mahasiswa->onEachSide(1)->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </main>
    </div>
</div>
