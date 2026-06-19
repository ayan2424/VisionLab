<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analytics — VisionLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.50.0/dist/apexcharts.min.js"></script>
    <style>
        html,body{background:#0a0a0a;color:#c9d1d9;min-height:100%;}
        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:#111}
        ::-webkit-scrollbar-thumb{background:#2a2a3a;border-radius:3px}
        ::-webkit-scrollbar-thumb:hover{background:#7c3aed}

        .kpi-card{
            background:#111111;border:1px solid #21262d;border-radius:16px;
            padding:20px 24px;transition:transform .2s,box-shadow .2s;
        }
        .kpi-card:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(124,58,237,.15);}

        .chart-card{
            background:#111111;border:1px solid #21262d;border-radius:16px;
            padding:20px 20px 8px;
        }

        .heat-cell{
            width:100%;aspect-ratio:1;border-radius:3px;
            transition:transform .15s;cursor:default;
        }
        .heat-cell:hover{transform:scale(1.4);}

        .badge{display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:700;font-family:sans-serif;}
        .badge-admin{background:rgba(124,58,237,.15);color:#a78bfa;border:1px solid rgba(124,58,237,.25);}
        .badge-instructor{background:rgba(34,211,238,.1);color:#22d3ee;border:1px solid rgba(34,211,238,.25);}
        .badge-student{background:rgba(74,222,128,.1);color:#4ade80;border:1px solid rgba(74,222,128,.25);}

        .nav-link{display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;font-size:13px;font-family:sans-serif;color:#64748b;transition:all .15s;}
        .nav-link:hover{background:rgba(255,255,255,.05);color:#f1f5f9;}
        .nav-link.active{background:rgba(124,58,237,.15);color:#a78bfa;}

        .apexcharts-tooltip{background:#161b22!important;border:1px solid #21262d!important;border-radius:10px!important;}
        .apexcharts-tooltip-title{background:#0d1117!important;border-bottom:1px solid #21262d!important;}

        @keyframes countUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .kpi-num{animation:countUp .6s ease forwards;}
    </style>
</head>
<body class="h-full">

<div class="flex h-screen overflow-hidden">

    <!-- ── SIDEBAR ── -->
    <aside class="flex flex-col w-56 border-r border-border overflow-y-auto flex-shrink-0" style="background:#0d1117;">
        <div class="px-4 py-4 border-b border-border flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-violet-600 flex items-center justify-center flex-shrink-0" style="box-shadow:0 0 10px rgba(124,58,237,.5);">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <div>
                <div class="text-sm font-bold text-white" style="font-family:sans-serif;">VisionLab</div>
                <div class="text-[10px] text-muted" style="font-family:sans-serif;">Admin Portal</div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="{{ route('home') }}" class="nav-link">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>
            <a href="{{ route('admin.analytics') }}" class="nav-link active">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Analytics
            </a>
            <a href="{{ route('workspace.index') }}" class="nav-link">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Workspace
            </a>
            <a href="{{ route('demo') }}" class="nav-link" target="_blank">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.361a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Demo Script
            </a>
        </nav>

        <!-- User pill -->
        <div class="px-3 py-4 border-t border-border">
            <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl" style="background:#0a0a0a;border:1px solid #21262d;">
                <div class="w-7 h-7 rounded-full bg-violet-600 flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0">
                    {{ auth()->user()->avatar_initials }}
                </div>
                <div class="min-w-0 flex-1" style="font-family:sans-serif;">
                    <div class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-muted">Admin</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full nav-link text-red-400 hover:text-red-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- ── MAIN CONTENT ── -->
    <main class="flex-1 overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-4 border-b border-border" style="background:rgba(10,10,10,.9);backdrop-filter:blur(12px);">
            <div>
                <h1 class="text-xl font-bold text-white" style="font-family:sans-serif;">Analytics Dashboard</h1>
                <p class="text-xs text-muted mt-0.5" style="font-family:sans-serif;">
                    Aptech Vision 2026 · Last updated {{ now()->format('d M Y, H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background:rgba(74,222,128,.1);color:#4ade80;border:1px solid rgba(74,222,128,.25);font-family:sans-serif;">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $activeSessions }} sessions live
                </span>
                <a href="{{ route('demo') }}" target="_blank"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all"
                   style="background:#7c3aed;color:#fff;font-family:sans-serif;box-shadow:0 0 12px rgba(124,58,237,.4);">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.361a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Open Demo
                </a>
            </div>
        </div>

        <div class="px-8 py-6 space-y-6">

            <!-- ── KPI ROW ── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                $kpis = [
                    ['label'=>'Total Users',      'value'=>$totalUsers,      'suffix'=>'',  'icon'=>'👥', 'color'=>'#7c3aed', 'sub'=>"{$studentCount} students · {$instructorCount} instructors",  'delta'=>'+12%'],
                    ['label'=>'Code Executions',  'value'=>$executions,      'suffix'=>'',  'icon'=>'▶', 'color'=>'#16a34a', 'sub'=>'last 30 days via Piston API',                                  'delta'=>'+34%'],
                    ['label'=>'AI Interactions',  'value'=>$aiInteractions,  'suffix'=>'',  'icon'=>'🤖', 'color'=>'#a78bfa', 'sub'=>'CHAT · PLAN · AGENT modes',                                   'delta'=>'+56%'],
                    ['label'=>'Avg Exec Time',    'value'=>$avgExecTime,     'suffix'=>'s', 'icon'=>'⚡', 'color'=>'#f59e0b', 'sub'=>'across all languages',                                        'delta'=>'-18%'],
                ];
                @endphp
                @foreach($kpis as $k)
                <div class="kpi-card">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base"
                             style="background:{{ $k['color'] }}18;border:1px solid {{ $k['color'] }}33;">
                            {{ $k['icon'] }}
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                              style="background:{{ str_starts_with($k['delta'],'+') ? 'rgba(74,222,128,.1)' : 'rgba(248,113,113,.1)' }};color:{{ str_starts_with($k['delta'],'+') ? '#4ade80' : '#f87171' }};font-family:sans-serif;">
                            {{ $k['delta'] }}
                        </span>
                    </div>
                    <div class="kpi-num text-2xl font-black text-white mb-1" style="font-family:sans-serif;letter-spacing:-0.02em;">
                        {{ number_format($k['value'], is_float($k['value']) ? 2 : 0) }}{{ $k['suffix'] }}
                    </div>
                    <div class="text-xs font-semibold text-slate-300 mb-0.5" style="font-family:sans-serif;">{{ $k['label'] }}</div>
                    <div class="text-[10px] text-muted" style="font-family:sans-serif;">{{ $k['sub'] }}</div>
                </div>
                @endforeach
            </div>

            <!-- ── CHARTS ROW 1 ── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Activity Line Chart (spans 2 cols) -->
                <div class="chart-card lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-white" style="font-family:sans-serif;">Platform Activity</h3>
                            <p class="text-[10px] text-muted" style="font-family:sans-serif;">Last 14 days · Executions · AI calls · Collabs</p>
                        </div>
                        <div class="flex items-center gap-3 text-[10px]" style="font-family:sans-serif;">
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-violet-500"></span><span class="text-muted">Executions</span></span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-cyan-400"></span><span class="text-muted">AI</span></span>
                            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span class="text-muted">Collab</span></span>
                        </div>
                    </div>
                    <div id="chart-activity" style="min-height:220px;"></div>
                </div>

                <!-- Language Donut -->
                <div class="chart-card">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-white" style="font-family:sans-serif;">Languages Used</h3>
                        <p class="text-[10px] text-muted" style="font-family:sans-serif;">Distribution by executions</p>
                    </div>
                    <div id="chart-languages" style="min-height:180px;"></div>
                    <div class="mt-3 space-y-1.5">
                        @php $lColors=['#7c3aed','#2563eb','#0891b2','#16a34a','#d97706','#dc2626','#64748b']; @endphp
                        @foreach($languageLabels as $i => $lang)
                        <div class="flex items-center justify-between text-[10px]" style="font-family:sans-serif;">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" style="background:{{ $lColors[$i] }};"></span>
                                <span class="text-muted">{{ $lang }}</span>
                            </span>
                            <span class="text-slate-400 font-semibold">{{ $languageCounts[$i] }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ── HEATMAP ── -->
            <div class="chart-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-white" style="font-family:sans-serif;">Activity Heatmap</h3>
                        <p class="text-[10px] text-muted" style="font-family:sans-serif;">Last 7 days × 24 hours — hourly activity intensity</p>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-muted" style="font-family:sans-serif;">
                        <span>Less</span>
                        <div class="flex gap-1">
                            @foreach([10,25,50,75,100] as $int)
                            <div class="w-3 h-3 rounded-sm" style="background:rgba(124,58,237,{{ $int/100 }});"></div>
                            @endforeach
                        </div>
                        <span>More</span>
                    </div>
                </div>
                <!-- Hour labels -->
                <div class="flex mb-1 pl-8">
                    @foreach(['0','3','6','9','12','15','18','21','23'] as $h)
                    <div class="flex-1 text-center text-[9px] text-muted" style="font-family:sans-serif;">{{ $h }}</div>
                    @endforeach
                </div>
                <div class="space-y-1">
                    @foreach($heatmap as $row)
                    <div class="flex items-center gap-1">
                        <span class="w-7 text-[10px] text-muted text-right flex-shrink-0" style="font-family:sans-serif;">{{ $row['day'] }}</span>
                        <div class="flex-1 grid gap-0.5" style="grid-template-columns:repeat(24,1fr);">
                            @foreach($row['hours'] as $intensity)
                            <div class="heat-cell" title="{{ $intensity }} sessions"
                                 style="background:rgba(124,58,237,{{ number_format($intensity/100,2) }});min-height:12px;"></div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- ── CHARTS ROW 2 ── -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Role distribution bar -->
                <div class="chart-card">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-white" style="font-family:sans-serif;">User Role Distribution</h3>
                        <p class="text-[10px] text-muted" style="font-family:sans-serif;">Total: {{ $totalUsers }} registered users</p>
                    </div>
                    <div id="chart-roles" style="min-height:180px;"></div>
                </div>

                <!-- AI mode usage -->
                <div class="chart-card">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-white" style="font-family:sans-serif;">AI Mode Usage</h3>
                        <p class="text-[10px] text-muted" style="font-family:sans-serif;">CHAT vs PLAN vs AGENT interactions</p>
                    </div>
                    <div id="chart-ai-modes" style="min-height:180px;"></div>
                </div>
            </div>

            <!-- ── RECENT SESSIONS TABLE ── -->
            <div class="chart-card">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-white" style="font-family:sans-serif;">Recent Coding Sessions</h3>
                        <p class="text-[10px] text-muted" style="font-family:sans-serif;">Last active sessions across all users</p>
                    </div>
                    <a href="{{ route('workspace.index') }}"
                       class="text-[10px] font-semibold text-violet-400 hover:text-violet-300 transition-colors"
                       style="font-family:sans-serif;">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs" style="font-family:sans-serif;border-collapse:separate;border-spacing:0;">
                        <thead>
                            <tr class="text-muted text-[10px] uppercase tracking-wider">
                                <th class="text-left pb-3 pr-4">User</th>
                                <th class="text-left pb-3 pr-4">Role</th>
                                <th class="text-left pb-3 pr-4">Language</th>
                                <th class="text-right pb-3 pr-4">Executions</th>
                                <th class="text-right pb-3 pr-4">AI Calls</th>
                                <th class="text-right pb-3 pr-4">Duration</th>
                                <th class="text-right pb-3">Last Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($recentSessions as $s)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-violet-600 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                            {{ $s['user']->avatar_initials }}
                                        </div>
                                        <div>
                                            <div class="text-white font-semibold">{{ $s['user']->name }}</div>
                                            <div class="text-[10px] text-muted">{{ $s['user']->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="badge badge-{{ $s['user']->role }}">{{ ucfirst($s['user']->role) }}</span>
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="flex items-center gap-1.5">
                                        <span>{{ $s['icon'] }}</span>
                                        <span class="text-slate-300">{{ ucfirst($s['language']) }}</span>
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-right">
                                    <span class="font-mono text-emerald-400 font-semibold">{{ $s['executions'] }}</span>
                                </td>
                                <td class="py-3 pr-4 text-right">
                                    <span class="font-mono text-violet-400 font-semibold">{{ $s['ai_calls'] }}</span>
                                </td>
                                <td class="py-3 pr-4 text-right text-slate-400">{{ $s['duration'] }}</td>
                                <td class="py-3 text-right text-muted">{{ $s['ago'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── TECH STACK BADGES ── -->
            <div class="chart-card">
                <h3 class="text-sm font-bold text-white mb-4" style="font-family:sans-serif;">Technology Stack</h3>
                <div class="flex flex-wrap gap-2">
                    @php
                    $stack = [
                        ['Laravel 11','#f9322c'],['SQLite','#003b57'],['Tailwind CSS','#06b6d4'],
                        ['Monaco Editor','#007acc'],['Laravel Reverb','#7c3aed'],['Alpine.js','#8bc0d0'],
                        ['Piston API','#16a34a'],['ApexCharts','#00b1f2'],['Gemini 2.0 Flash','#4285f4'],
                        ['Pusher JS','#300d4f'],['Vite','#646cff'],['PHP 8.3','#8892bf'],
                    ];
                    @endphp
                    @foreach($stack as [$name,$color])
                    <span class="px-3 py-1.5 rounded-xl text-xs font-semibold border"
                          style="background:{{ $color }}18;color:{{ $color }};border-color:{{ $color }}33;font-family:sans-serif;">
                        {{ $name }}
                    </span>
                    @endforeach
                </div>
            </div>

        </div><!-- /px-8 -->
    </main>
</div>

<!-- ── APEXCHARTS INIT ── -->
<script>
const APEX_OPTS = {
    chart:   { background:'transparent', toolbar:{ show:false }, fontFamily:'sans-serif', foreColor:'#64748b' },
    grid:    { borderColor:'#21262d', strokeDashArray:3 },
    tooltip: { theme:'dark' },
    stroke:  { curve:'smooth' },
};

// 1. Activity line chart
new ApexCharts(document.getElementById('chart-activity'), {
    ...APEX_OPTS,
    chart:  { ...APEX_OPTS.chart, type:'area', height:220, sparkline:{ enabled:false } },
    series: [
        { name:'Executions', data: @json($execData) },
        { name:'AI Calls',   data: @json($aiData)   },
        { name:'Collabs',    data: @json($collabData) },
    ],
    colors:    ['#7c3aed','#22d3ee','#4ade80'],
    fill:      { type:'gradient', gradient:{ shadeIntensity:1, opacityFrom:.3, opacityTo:.02, stops:[0,90,100] } },
    xaxis:     { categories:@json($activityLabels), labels:{ style:{fontSize:'10px'} }, axisBorder:{show:false}, axisTicks:{show:false} },
    yaxis:     { labels:{ style:{fontSize:'10px'} } },
    legend:    { show:false },
    dataLabels:{ enabled:false },
}).render();

// 2. Language donut
new ApexCharts(document.getElementById('chart-languages'), {
    ...APEX_OPTS,
    chart:    { ...APEX_OPTS.chart, type:'donut', height:180 },
    series:   @json($languageCounts),
    labels:   @json($languageLabels),
    colors:   ['#7c3aed','#2563eb','#0891b2','#16a34a','#d97706','#dc2626','#64748b'],
    legend:   { show:false },
    plotOptions:{ pie:{ donut:{ size:'65%', labels:{ show:true, total:{ show:true, label:'Total', color:'#94a3b8', fontSize:'10px', formatter:()=>'100%' } } } } },
    dataLabels:{ enabled:false },
    stroke:   { colors:['#111111'], width:2 },
}).render();

// 3. Role distribution bar
new ApexCharts(document.getElementById('chart-roles'), {
    ...APEX_OPTS,
    chart:    { ...APEX_OPTS.chart, type:'bar', height:180 },
    series:   [{ name:'Users', data:[{{ $adminCount }},{{ $instructorCount }},{{ $studentCount }}] }],
    colors:   ['#7c3aed','#22d3ee','#4ade80'],
    xaxis:    { categories:['Admin','Instructor','Student'], labels:{ style:{fontSize:'11px'} } },
    yaxis:    { labels:{ style:{fontSize:'10px'} } },
    plotOptions:{ bar:{ borderRadius:6, columnWidth:'45%', distributed:true } },
    legend:   { show:false },
    dataLabels:{ enabled:true, style:{ colors:['#fff'], fontSize:'11px', fontWeight:700 } },
}).render();

// 4. AI modes radar
new ApexCharts(document.getElementById('chart-ai-modes'), {
    ...APEX_OPTS,
    chart:    { ...APEX_OPTS.chart, type:'bar', height:180 },
    series:   [{ name:'Interactions', data:[{{ (int)($aiInteractions*0.52) }},{{ (int)($aiInteractions*0.28) }},{{ (int)($aiInteractions*0.20) }}] }],
    colors:   ['#a78bfa','#22d3ee','#f59e0b'],
    xaxis:    { categories:['CHAT Mode','PLAN Mode','AGENT Mode'], labels:{ style:{fontSize:'11px'} } },
    yaxis:    { labels:{ style:{fontSize:'10px'} } },
    plotOptions:{ bar:{ borderRadius:6, columnWidth:'45%', distributed:true } },
    legend:   { show:false },
    dataLabels:{ enabled:true, style:{ colors:['#fff'], fontSize:'11px', fontWeight:700 } },
}).render();
</script>

</body>
</html>


