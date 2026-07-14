@extends('layouts.app')

@section('content')
<!-- Dashboard double-pane container using Alpine.js for sidebars and tabs -->
<div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- 1. Left Sidebar Navigation -->
    @include('layouts.dashboard-sidebar')

    <!-- Overlay back shadow on mobile when sidebar is open -->
    <div x-show="mobileSidebar" 
         @click="mobileSidebar = false" 
         class="fixed inset-0 bg-slate-900/40 z-20 md:hidden"
         x-cloak></div>

    <!-- 2. Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        


        <!-- Page Main Wrapper -->
        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto font-sans">
            
            <!-- Breadcrumbs / Overview Top Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Overview</span>
                </div>
                <div class="hidden sm:flex items-center space-x-2.5">
                    <span class="text-[9px] font-black tracking-widest bg-blue-100 text-[#1e40af] px-3 py-1 rounded-full uppercase">
                        🟢 {{ Auth::user()->role }} Panel Active
                    </span>
                </div>
            </div>

            <!-- Premium Welcome Panel with Live Clock & Greeting -->
            <div class="bg-gradient-to-r from-blue-800 via-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-blue-700/20">
                <!-- Decorative background shapes -->
                <div class="absolute -right-16 -top-16 w-44 h-44 rounded-full bg-blue-600/10 blur-xl"></div>
                <div class="absolute -left-16 -bottom-16 w-44 h-44 rounded-full bg-indigo-600/10 blur-xl"></div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-black tracking-widest text-blue-200 uppercase block font-display">Control Center</span>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight font-display" x-data="{ 
                            getGreeting() {
                                const hrs = new Date().getHours();
                                if (hrs < 12) return 'Good Morning';
                                if (hrs < 18) return 'Good Afternoon';
                                return 'Good Evening';
                            }
                        }">
                            <span x-text="getGreeting()"></span>, {{ Auth::user()->name }}!
                        </h1>
                        <p class="text-xs text-blue-100/80 max-w-md leading-relaxed font-medium">Welcome back to the SK Portal. Manage citizen submissions, schedule events, and monitor community requests.</p>
                    </div>

                    <!-- Live Date, Time & Location Panel -->
                    <div x-data="{ 
                        time: '', 
                        date: '',
                        updateClock() {
                            const now = new Date();
                            // Format Time: hh:mm:ss AM/PM
                            let hours = now.getHours();
                            const minutes = String(now.getMinutes()).padStart(2, '0');
                            const seconds = String(now.getSeconds()).padStart(2, '0');
                            const ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12;
                            hours = hours ? hours : 12; // the hour '0' should be '12'
                            this.time = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;

                            // Format Date: Month DD, YYYY
                            const options = { month: 'long', day: 'numeric', year: 'numeric' };
                            this.date = now.toLocaleDateString('en-US', options);
                        }
                    }" x-init="updateClock(); setInterval(() => updateClock(), 1000)" class="flex flex-col items-start md:items-end bg-white/5 border border-white/10 rounded-2xl p-4 shrink-0 backdrop-blur-sm min-w-[240px]">
                        <div class="flex items-center space-x-2 text-xs font-bold text-blue-200 uppercase tracking-wider">
                            <span>Mandaluyong City, Metro Manila</span>
                        </div>
                        <div class="text-lg font-mono font-bold tracking-tight text-white mt-1.5" x-text="time"></div>
                        <div class="text-[10px] font-bold text-blue-250 uppercase tracking-widest mt-0.5" x-text="date"></div>
                    </div>
                </div>
            </div>

            <!-- Stats Metric Cards (Clicking redirects to profiling registry page with corresponding filter) -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Registered Youth -->
                <a href="{{ route('dashboard.profiling.index') }}"
                   class="card text-left p-5 flex flex-col justify-between transition-all duration-200 cursor-pointer border border-slate-100 hover:border-blue-200 hover:shadow-md active:scale-98 bg-white rounded-3xl border-l-4 border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="users" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Total Youth</span>
                    </div>
                    <div class="mt-4">
                        <span class="block text-2xl font-black font-display text-slate-800">{{ $totalYouth }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">KK Registry</span>
                    </div>
                </a>

                <!-- In-School Youth -->
                <a href="{{ route('dashboard.profiling.index', ['classification' => 'ISY']) }}"
                   class="card text-left p-5 flex flex-col justify-between transition-all duration-200 cursor-pointer border border-slate-100 hover:border-blue-200 hover:shadow-md active:scale-98 bg-white rounded-3xl border-l-4 border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="education" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">In-School</span>
                    </div>
                    <div class="mt-4">
                        <span class="block text-2xl font-black font-display text-slate-800">{{ $totalIsy }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ISY Members</span>
                    </div>
                </a>

                <!-- Out-of-School Youth -->
                <a href="{{ route('dashboard.profiling.index', ['classification' => 'OSY']) }}"
                   class="card text-left p-5 flex flex-col justify-between transition-all duration-200 cursor-pointer border border-slate-100 hover:border-blue-200 hover:shadow-md active:scale-98 bg-white rounded-3xl border-l-4 border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="active-citizenship" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Out-Of-School</span>
                    </div>
                    <div class="mt-4">
                        <span class="block text-2xl font-black font-display text-slate-800">{{ $totalOsy }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">OSY Members</span>
                    </div>
                </a>

                <!-- Working Youth -->
                <a href="{{ route('dashboard.profiling.index', ['classification' => 'WY']) }}"
                   class="card text-left p-5 flex flex-col justify-between transition-all duration-200 cursor-pointer border border-slate-100 hover:border-blue-200 hover:shadow-md active:scale-98 bg-white rounded-3xl border-l-4 border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="youth-employment" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Working</span>
                    </div>
                    <div class="mt-4">
                        <span class="block text-2xl font-black font-display text-slate-800">{{ $totalWy }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">WY Members</span>
                    </div>
                </a>

                <!-- SK Voters -->
                <a href="{{ route('dashboard.profiling.index', ['sk_voter' => 1]) }}"
                   class="card text-left p-5 flex flex-col justify-between transition-all duration-200 cursor-pointer border border-slate-100 hover:border-blue-200 hover:shadow-md active:scale-98 bg-white rounded-3xl border-l-4 border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="governance" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">SK Voters</span>
                    </div>
                    <div class="mt-4">
                        <span class="block text-2xl font-black font-display text-slate-800">{{ $totalSkVoters }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Registered Voters</span>
                    </div>
                </a>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Stacked Bar Chart Component (Chart.js) -->
                <div class="card space-y-4 bg-white border border-slate-100 p-6 rounded-3xl shadow-sm lg:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Youth Population by Purok</h3>
                    <div class="relative h-[250px] sm:h-[300px]">
                        <canvas id="submissionsChart"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart: Youth Classification Distribution -->
                <div class="card space-y-4 bg-white border border-slate-100 p-6 rounded-3xl shadow-sm lg:col-span-1">
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Youth Classification Distribution</h3>
                    <div class="relative h-[200px] sm:h-[240px] md:h-[250px] flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Service Requestor Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Doughnut Chart: Accomplished Services by Program -->
                <div class="card space-y-4 bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Accomplished Services by Program</h3>
                    <div class="relative h-[200px] sm:h-[240px] flex items-center justify-center">
                        <canvas id="serviceRequestsChart"></canvas>
                    </div>
                </div>

                <!-- Line Chart: Accomplishment Trends over Time -->
                <div class="card space-y-4 bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Accomplishment Trends (Last 6 Months)</h3>
                    <div class="relative h-[200px] sm:h-[240px]">
                        <canvas id="accomplishmentTrendsChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Include Chart.js CDN for interactive visual trend reports -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Youth Population by Purok Bar Chart
        const ctx = document.getElementById('submissionsChart').getContext('2d');
        const data = @json($chartData);
        
        const puroks = data.map(d => d.purok);
        const counts = data.map(d => d.count);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: puroks,
                datasets: [
                    {
                        label: 'Total Youth Population',
                        data: counts,
                        backgroundColor: '#1e40af', // Primary Blue
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                }
            }
        });

        // Youth Classification Distribution Doughnut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const classificationData = @json($classificationDistribution);

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['In-School Youth (ISY)', 'Out-of-School Youth (OSY)', 'Working Youth (WY)'],
                datasets: [{
                    data: [classificationData.isy, classificationData.osy, classificationData.wy],
                    backgroundColor: [
                        '#1e40af', // Blue for ISY
                        '#f59e0b', // Amber for OSY
                        '#10b981'  // Emerald for WY
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });

        // Accomplished Services by Program Doughnut Chart
        const serviceCtx = document.getElementById('serviceRequestsChart').getContext('2d');
        const serviceData = @json($accomplishedByProgram);

        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Health Consults', 'Pabili Medicine', 'Library Study Slots', 'Sports Tournament'],
                datasets: [{
                    data: [serviceData.health, serviceData.medicine, serviceData.silid, serviceData.sports],
                    backgroundColor: [
                        '#1e40af', // Primary Blue
                        '#f59e0b', // Amber Accent
                        '#10b981', // Emerald
                        '#6366f1'  // Indigo
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });

        // Accomplishment Trends over Time Line Chart
        const trendsCtx = document.getElementById('accomplishmentTrendsChart').getContext('2d');
        const trendsData = @json($accomplishmentTrends);

        const trendLabels = trendsData.map(d => d.label);
        const trendCounts = trendsData.map(d => d.count);

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Accomplished Requests',
                    data: trendCounts,
                    borderColor: '#10b981', // Premium emerald green
                    backgroundColor: 'rgba(16, 185, 129, 0.1)', // Light emerald gradient area fill
                    fill: true,
                    tension: 0.4, // Smooth curve
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hidden for single series
                    }
                }
            }
        });
    });
</script>
@endsection
