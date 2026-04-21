<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Mahasiswa;
use App\Models\Jurusan;

new #[Layout('layouts.afterLogin')] class extends Component {
    public $name;
    public $now;
    public $countStudent;
    public $countJurusan;
    public $chartLabels = [];
    public $chartValues = [];

    public function mount()
    {
        $this->countStudent = Mahasiswa::count();
        $this->countJurusan = Jurusan::count();
        $this->name = auth()->user()?->name;
        $this->now = now();

        $data = Jurusan::withCount('mahasiswa')->orderBy('nama_jurusan')->get();

        $this->chartLabels = $data->pluck('nama_jurusan')->toArray();
        $this->chartValues = $data->pluck('mahasiswa_count')->toArray();
    }
};
?>

<div class="bg-[#f7f9fb] text-[#191c1e] antialiased">

    <main class="pt-20 min-h-screen">
        <div class="p-12 space-y-10">

            <!-- Welcome Section -->
            <section class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="text-5xl font-extrabold text-[#00113a] font-headline tracking-tighter leading-tight">
                        Welcome back,<br />
                        <span class="text-[#758dd5]">
                            {{ auth()->user()?->name ? auth()->user()->name : 'admin' }}.
                        </span>
                    </h2>
                </div>

                <div class="flex gap-3">
                    <div class="px-6 py-3 bg-[#f2f4f6] rounded-xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#002366]">calendar_today</span>
                        <span class="text-sm font-semibold text-[#191c1e]">
                            {{ $now->format('j F Y') }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Statistik -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    class="bg-white p-8 rounded-3xl border border-[#c5c6d2]/20 group hover:bg-[#00113a] transition-all duration-500 shadow-sm hover:shadow-xl">
                    <div class="flex justify-between items-start">
                        <div>
                            <p
                                class="text-slate-400 group-hover:text-[#dbe1ff] text-xs font-bold uppercase tracking-widest">
                                Total Students
                            </p>
                            <h3 class="text-3xl font-black mt-2 text-[#00113a] group-hover:text-white">
                                {{ $countStudent }}
                            </h3>
                        </div>
                        <div
                            class="p-3 bg-[#dbe1ff] rounded-xl text-[#00113a] group-hover:bg-white/20 group-hover:text-white">
                            <span class="material-symbols-outlined">groups</span>
                        </div>
                    </div>
                    <div
                        class="mt-6 flex items-center gap-2 text-xs font-bold text-[#00113a] group-hover:text-[#dbe1ff]">
                        <span class="material-symbols-outlined text-sm">trending_up</span>
                        <span>Mahasiswa Aktif</span>
                    </div>
                </div>

                <div
                    class="bg-white p-8 rounded-3xl border border-[#c5c6d2]/20 group hover:bg-[#00113a] transition-all duration-500 shadow-sm hover:shadow-xl">
                    <div class="flex justify-between items-start">
                        <div>
                            <p
                                class="text-slate-400 group-hover:text-[#dbe1ff] text-xs font-bold uppercase tracking-widest">
                                Jurusan
                            </p>
                            <h3 class="text-3xl font-black mt-2 text-[#00113a] group-hover:text-white">
                                {{ $countJurusan }}
                            </h3>
                        </div>
                        <div
                            class="p-3 bg-[#e5e2e1] rounded-xl text-[#5f5e5e] group-hover:bg-white/20 group-hover:text-white">
                            <span class="material-symbols-outlined">account_balance</span>
                        </div>
                    </div>
                    <div
                        class="mt-6 flex items-center gap-2 text-xs font-bold text-[#00113a] group-hover:text-[#dbe1ff]">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>All active</span>
                    </div>
                </div>
            </section>

            <!-- Chart -->
            <section class="grid grid-cols-1 gap-10 pb-20 scroll-snap-y">
                <div class="lg:col-span-1 space-y-6">
                    <h3 class="text-xl font-bold text-[#00113a] font-headline">
                        Distribusi Jurusan
                    </h3>

                    <div class="overflow-x-auto" wire:ignore>
                        <div
                            class="min-w-[1200px] h-[380px] bg-white rounded-3xl p-8 shadow-sm border border-[#c5c6d2]/20">
                            <canvas id="jurusanChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

@push('scripts')
    <script>
        let jurusanChartInstance = null;

        function renderJurusanChart() {
            const canvas = document.getElementById('jurusanChart');
            if (!canvas) return;
            if (!canvas || typeof Chart === 'undefined') return;

            const labels = @json($chartLabels);
            const values = @json($chartValues);

            if (jurusanChartInstance) {
                jurusanChartInstance.destroy();
            }

            jurusanChartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Mahasiswa',
                        data: values,
                        backgroundColor: values.map((_, i) =>
                            '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')
                        ),
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        renderJurusanChart();
        document.addEventListener('livewire:navigated', renderJurusanChart);
    </script>
@endpush
