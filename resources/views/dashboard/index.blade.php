@extends('layouts.app')

@section('title', 'Dashboard - AD Rey de Reyes')

@push('styles')
<style>
/* ===== DASHBOARD PREMIUM ===== */
.kpi-card {
    background: linear-gradient(145deg, var(--bg-card), rgba(var(--bg-card-rgb), 0.8));
    border: 1px solid var(--border-color);
    border-radius: 24px;
    padding: 1.85rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-sm);
    backdrop-filter: blur(10px);
}
.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: rgba(var(--bs-primary-rgb), 0.3);
}
.kpi-card::after {
    content: '';
    position: absolute;
    top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, transparent 70%);
    pointer-events: none;
}
.kpi-card.kpi-primary { border-left: 4px solid #3b82f6; }
.kpi-card.kpi-success { border-left: 4px solid #10b981; }
.kpi-card.kpi-warning { border-left: 4px solid #f59e0b; }
.kpi-card.kpi-purple  { border-left: 4px solid #8b5cf6; }

.kpi-icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    box-shadow: 0 8px 16px -4px rgba(0,0,0,0.1);
}
.kpi-icon.icon-primary { background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.05)); color: #60a5fa; }
.kpi-icon.icon-success { background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.05)); color: #34d399; }
.kpi-icon.icon-warning { background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(245,158,11,0.05)); color: #fbbf24; }
.kpi-icon.icon-purple  { background: linear-gradient(135deg, rgba(139,92,246,0.2), rgba(139,92,246,0.05)); color: #a78bfa; }

.kpi-number {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.04em;
    margin: 10px 0;
}
.kpi-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
}
.kpi-trend {
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0.9;
}



.avatar-ring {
    width: 42px; height: 42px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.9rem;
    flex-shrink: 0;
}

/* Chart cards */
.chart-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: var(--shadow-sm);
}
.chart-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 2px;
}

/* Bulletproof gradient for action button */
.btn-bulletproof-blue {
    background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
    color: white !important;
    box-shadow: 0 4px 14px rgba(37,99,235,0.25) !important;
    border: none !important;
    transition: all 0.3s ease !important;
}
.btn-bulletproof-blue:hover {
    background: linear-gradient(135deg, #1d4ed8, #4338ca) !important;
    box-shadow: 0 6px 20px rgba(37,99,235,0.35) !important;
    transform: translateY(-2px);
}
</style>
@endpush

@section('header_title', 'Dashboard General')
@section('header_subtitle', 'Resumen estratégico de la congregación · ' . now()->translatedFormat('l, d F'))
@section('header_icon')
<i class="fas fa-chart-pie fs-5"></i>
@endsection

@section('content')

{{-- ===== KPIs BENTO GRID (AL PRINCIPIO) ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card kpi-primary">
        <div class="flex justify-between items-start">
            <div class="kpi-icon icon-primary"><i class="fas fa-users"></i></div>
            <div class="kpi-trend text-emerald-500"><i class="fas fa-arrow-up"></i> +2</div>
        </div>
        <div class="mt-4">
            <div class="kpi-label">Total Miembros</div>
            <div class="kpi-number text-blue-400">{{ $totalMiembros }}</div>
            <div class="text-xs font-semibold text-emerald-500">Congregación activa</div>
        </div>
    </div>
    <div class="kpi-card kpi-success">
        <div class="flex justify-between items-start">
            <div class="kpi-icon icon-success"><i class="fas fa-user-check"></i></div>
        </div>
        <div class="mt-4">
            <div class="kpi-label">Activos</div>
            <div class="kpi-number text-emerald-400">{{ $miembrosActivos }}</div>
            <div class="text-xs font-semibold text-gray-500">de {{ $totalMiembros }} total</div>
        </div>
    </div>
    <div class="kpi-card kpi-warning">
        <div class="flex justify-between items-start">
            <div class="kpi-icon icon-warning"><i class="fas fa-home"></i></div>
        </div>
        <div class="mt-4">
            <div class="kpi-label">Familias</div>
            <div class="kpi-number text-amber-400">{{ $totalFamilias }}</div>
            <div class="text-xs font-semibold text-gray-500">núcleos registrados</div>
        </div>
    </div>
    <div class="kpi-card kpi-purple">
        <div class="flex justify-between items-start">
            <div class="kpi-icon icon-purple"><i class="fas fa-network-wired"></i></div>
        </div>
        <div class="mt-4">
            <div class="kpi-label">Células</div>
            <div class="kpi-number text-purple-400">{{ \App\Models\Celula::count() }}</div>
            <div class="text-xs font-semibold text-gray-500">grupos activos</div>
        </div>
    </div>
</div>

{{-- ===== BARRA DE ACCIONES RÁPIDAS (BENTO BAR PREMIUM) ===== --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50 dark:bg-slate-800/30 p-5 rounded-3xl border border-slate-200 dark:border-slate-800/80 mb-6 shadow-sm gap-4">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/10 text-blue-500 font-bold shadow-sm flex-shrink-0">
            <i class="fas fa-bolt text-xl"></i>
        </div>
        <div>
            <h6 class="font-bold text-slate-800 dark:text-white mb-0.5 text-base tracking-tight">Acciones Rápidas</h6>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 font-normal">Gestión directa y accesos ministeriales estratégicos</p>
        </div>
    </div>
    <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">
        <a href="{{ route('miembros.index') }}" class="flex-1 sm:flex-none btn-bulletproof-blue px-6 py-3 rounded-2xl font-bold text-xs flex items-center justify-center gap-2.5 no-underline cursor-pointer">
            <i class="fas fa-user-plus text-sm"></i> <span>Nuevo Miembro</span>
        </a>
        <a href="{{ route('reportes.index') }}" class="flex-1 sm:flex-none border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 px-6 py-3 rounded-2xl font-bold text-xs shadow-sm flex items-center justify-center gap-2.5 transition-all hover:-translate-y-0.5 no-underline cursor-pointer">
            <i class="fas fa-chart-line text-sm"></i> <span>Centro de Reportes</span>
        </a>
    </div>
</div>

{{-- ===== CHARTS ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
    <div class="lg:col-span-5">
        <div class="chart-card h-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="chart-label">Distribución</div>
                    <h6 class="font-bold mb-0 text-slate-800 dark:text-white">Géneros en la Congregación</h6>
                </div>
                <div class="kpi-icon icon-primary" style="width:36px;height:36px;font-size:1rem;border-radius:10px;">
                    <i class="fas fa-venus-mars"></i>
                </div>
            </div>
            <div style="height: 220px;">
                <canvas id="chartSexo"></canvas>
            </div>
        </div>
    </div>
    <div class="lg:col-span-7">
        <div class="chart-card h-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="chart-label">Estadística</div>
                    <h6 class="font-bold mb-0 text-slate-800 dark:text-white">Estado Civil de Miembros</h6>
                </div>
                <div class="kpi-icon icon-purple" style="width:36px;height:36px;font-size:1rem;border-radius:10px;">
                    <i class="fas fa-heart"></i>
                </div>
            </div>
            <div style="height: 220px;">
                <canvas id="chartCivil"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ===== BOTTOM WIDGETS ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Cumpleaños --}}
    <div>
        <div class="chart-card h-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="chart-label">Este Mes</div>
                    <h6 class="font-bold mb-0 flex items-center gap-2 text-slate-800 dark:text-white">
                        <i class="fas fa-birthday-cake text-amber-500"></i> Cumpleaños
                    </h6>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(245,158,11,.15);color:#f59e0b;">
                    {{ count($cumpleañeros) }} este mes
                </span>
            </div>
            @forelse($cumpleañeros as $miembro)
            <div class="premium-list-item flex items-center justify-between p-3 rounded-2xl mb-2 bg-slate-50 dark:bg-slate-800/30 border border-transparent hover:bg-slate-100 dark:hover:bg-slate-800 transition-all hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="avatar-ring shadow-sm" style="background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(245,158,11,0.05)); color:#fbbf24;">
                        <i class="fas fa-birthday-cake" style="font-size:0.8rem;"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white" style="font-size:.95rem; letter-spacing:-0.02em;">{{ $miembro->nombres }}</div>
                        <div class="text-gray-500 font-medium" style="font-size:.78rem;">{{ $miembro->dia_cumple }} de {{ now()->translatedFormat('F') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="rounded-full font-bold" style="background:rgba(96,165,250,0.1); color:#60a5fa; font-size:.7rem; padding:.5rem .9rem; border: 1px solid rgba(96,165,250,0.15);">
                        {{ $miembro->edad_a_cumplir }} años
                    </span>
                    <a href="https://wa.me/{{ $miembro->telefono }}" target="_blank"
                       class="rounded-full shadow-sm hover:opacity-80 transition-opacity" title="Felicitar"
                       style="width:36px;height:36px;background:linear-gradient(135deg, rgba(37,211,102,0.2), rgba(37,211,102,0.1)); color:#22c55e; border: 1px solid rgba(37,211,102,0.2); display:flex; align-items:center; justify-content:center;">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div class="avatar-ring mx-auto mb-3" style="background:rgba(245,158,11,.1);color:#f59e0b;width:56px;height:56px;font-size:1.5rem;">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <p class="text-gray-500 text-sm mb-0">No hay cumpleaños este mes.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Seguimiento Nuevos --}}
    <div>
        <div class="chart-card h-full">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="chart-label">Seguimiento</div>
                    <h6 class="font-bold mb-0 flex items-center gap-2 text-slate-800 dark:text-white">
                        <i class="fas fa-seedling text-emerald-500"></i> Nuevos Convertidos
                    </h6>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-bold" style="background:rgba(16,185,129,.15);color:#10b981;">
                    Recientes
                </span>
            </div>
            @forelse($nuevosConvertidos as $m)
            <div class="premium-list-item flex items-center justify-between p-3 rounded-2xl mb-2 bg-slate-50 dark:bg-slate-800/30 border border-transparent hover:bg-slate-100 dark:hover:bg-slate-800 transition-all hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="avatar-ring font-bold shadow-sm border border-blue-200 dark:border-blue-500/20" style="background: linear-gradient(135deg, rgba(96,165,250,0.2), rgba(96,165,250,0.05)); color:#60a5fa; font-size:.85rem;">
                        {{ strtoupper(substr($m->nombres, 0, 1)) }}{{ strtoupper(substr($m->apellidos, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white" style="font-size:.95rem; letter-spacing:-0.02em;">{{ $m->nombres }} {{ $m->apellidos }}</div>
                        <div class="text-gray-500 font-medium" style="font-size:.78rem;">Integrado hace {{ $m->dias_desde_integracion }} días</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="rounded-full font-bold" style="background:rgba(139,92,246,0.1); color:#a78bfa; font-size:.7rem; padding:.5rem .9rem; border: 1px solid rgba(139,92,246,0.15);">
                        {{ $m->etapa_consolidacion }}
                    </span>
                    <a href="{{ route('miembros.show', $m->id) }}"
                       class="rounded-full shadow-sm hover:opacity-80 transition-opacity" title="Ver Perfil"
                       style="width:36px;height:36px;background:linear-gradient(135deg, rgba(96,165,250,0.2), rgba(96,165,250,0.1)); color:#60a5fa; border: 1px solid rgba(96,165,250,0.2); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-chevron-right" style="font-size:0.8rem;"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <div class="avatar-ring mx-auto mb-3" style="background:rgba(16,185,129,.1);color:#10b981;width:56px;height:56px;font-size:1.5rem;">
                    <i class="fas fa-seedling"></i>
                </div>
                <p class="text-gray-500 text-sm mb-0">No hay nuevos registros recientes.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let chartSexo, chartCivil;

    function getThemeColors() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            text: isDark ? '#94a3b8' : '#64748b',
            grid: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
            border: isDark ? '#1e293b' : '#ffffff'
        };
    }

    function initCharts() {
        const c = getThemeColors();
        const palette = ['#3b82f6', '#e11d48', '#10b981', '#f59e0b', '#8b5cf6'];

        if (chartSexo) chartSexo.destroy();
        if (chartCivil) chartCivil.destroy();

        chartSexo = new Chart(document.getElementById('chartSexo'), {
            type: 'doughnut',
            data: {
                labels: @json($distribucionSexo->pluck('sexo')),
                datasets: [{ 
                    data: @json($distribucionSexo->pluck('total')),
                    backgroundColor: [palette[0], palette[1]],
                    borderColor: c.border,
                    borderWidth: 4,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: c.text, padding: 20, font: { size: 12, weight: '600' } } }
                }
            }
        });

        chartCivil = new Chart(document.getElementById('chartCivil'), {
            type: 'bar',
            data: {
                labels: @json($distribucionCivil->pluck('estado_civil')),
                datasets: [{
                    data: @json($distribucionCivil->pluck('total')),
                    backgroundColor: palette.map(p => p + '99'),
                    borderColor: palette,
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: c.grid }, ticks: { color: c.text, font: { size: 11 } }, border: { display: false } },
                    x: { grid: { display: false }, ticks: { color: c.text, font: { size: 11 } }, border: { display: false } }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initCharts);

    const observer = new MutationObserver(() => initCharts());
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
</script>
@endpush
