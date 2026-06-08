<?php

use Livewire\Component;
use App\Models\Mahasiswa;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function exportCSV()
    {
        $fileName = 'data-mahasiswa-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        $callBack = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xef) . chr(0xbb) . chr(0xbf));
            fputcsv($file, ['NIM', 'Nama', 'Jurusan'], ';');

            Mahasiswa::with('jurusan:id,nama_jurusan')
                ->orderBy('nama')
                ->chunk(100, function ($mahasiswa) use ($file) {
                    foreach ($mahasiswa as $data) {
                        fputcsv($file, [
                            $data->nim,
                            $data->nama,
                            $data->jurusan?->nama_jurusan ?? '-',
                        ], ';');
                    }
                });

            fclose($file);
        };

        return response()->streamDownload($callBack, $fileName, $headers);
    }

    public function with(): array
    {
        $mahasiswa = Mahasiswa::with('jurusan:id,nama_jurusan')
            ->orderBy('nama')
            ->get();

        return [
            'mahasiswa' => $mahasiswa,
            'tanggalCetak' => now()->format('d/m/Y H:i'),
            'totalMahasiswa' => $mahasiswa->count(),
            'totalJurusan' => $mahasiswa->pluck('id_jurusan')->filter()->unique()->count(),
        ];
    }
};
?>

<div class="min-h-screen bg-[#f7f9fb] py-10 text-[#191c1e] print:bg-white print:py-0">
    <style>
        @page {
            size: A4;
            margin: 16mm;
        }

        @media print {
            body {
                background: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .print-sheet {
                width: 100% !important;
                max-width: none !important;
                border: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>

    <div class="no-print mx-auto mb-6 flex max-w-5xl flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('data-mahasiswa') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#c5c6d2] bg-white px-5 py-3 text-sm font-bold text-[#00113a] transition-all hover:shadow-md">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Data Mahasiswa
        </a>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button type="button" wire:click="exportCSV"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#c5c6d2] bg-white px-5 py-3 text-sm font-bold text-[#00113a] transition-all hover:shadow-md">
                <span class="material-symbols-outlined text-lg">download</span>
                Export CSV
            </button>
            <button type="button" onclick="window.print()"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-[#00113a] to-[#002366] px-6 py-3 text-sm font-bold text-white transition-all hover:shadow-lg active:scale-95">
                <span class="material-symbols-outlined text-lg">print</span>
                Print
            </button>
        </div>
    </div>

    <section class="print-sheet mx-auto max-w-5xl rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <header class="border-b-2 border-[#00113a] pb-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#758dd5]">MyCampus</p>
                    <h1 class="mt-2 font-headline text-3xl font-extrabold text-[#00113a]">Laporan Data Mahasiswa</h1>
                    <p class="mt-2 text-sm text-[#444650]">Dicetak pada {{ $tanggalCetak }} WIB</p>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-[#00113a] text-white">
                    <span class="material-symbols-outlined text-3xl">account_balance</span>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 gap-4 py-6 sm:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-[#f7f9fb] p-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#444650]">Total Mahasiswa</p>
                <p class="mt-1 font-headline text-3xl font-extrabold text-[#00113a]">{{ $totalMahasiswa }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-[#f7f9fb] p-4">
                <p class="text-xs font-bold uppercase tracking-widest text-[#444650]">Jurusan Terisi</p>
                <p class="mt-1 font-headline text-3xl font-extrabold text-[#00113a]">{{ $totalJurusan }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200">
            <table class="w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-[#00113a] text-white">
                        <th class="w-16 px-4 py-3 text-xs font-bold uppercase tracking-widest">No</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-widest">NIM</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-widest">Nama Mahasiswa</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-widest">Jurusan</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($mahasiswa as $data)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-[#00113a]">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-[#191c1e]">{{ $data->nim }}</td>
                            <td class="px-4 py-3 font-medium text-[#191c1e]">{{ $data->nama }}</td>
                            <td class="px-4 py-3 text-[#444650]">{{ $data->jurusan?->nama_jurusan ?? '-' }}</td>
                            <td class="px-4 py-3 text-[#444650]">Aktif</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#444650]">
                                Data mahasiswa belum ada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer class="mt-10 grid grid-cols-1 gap-8 text-sm text-[#444650] sm:grid-cols-2">
            <div>
                <p class="font-bold text-[#00113a]">Catatan</p>
                <p class="mt-2">Laporan ini dibuat otomatis dari data mahasiswa yang tersimpan di sistem MyCampus.</p>
            </div>
            <div class="text-left sm:text-right">
                <p>Admin MyCampus</p>
                <div class="ml-auto mt-16 w-48 border-t border-[#00113a] pt-2 sm:ml-auto">
                    <p class="font-bold text-[#00113a]">Petugas Akademik</p>
                </div>
            </div>
        </footer>
    </section>

    <script>
        window.addEventListener('load', function() {
            if (window.location.hash === '#print') {
                window.print();
            }
        });
    </script>
</div>
