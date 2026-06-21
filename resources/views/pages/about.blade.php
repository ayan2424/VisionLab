@extends('layouts.landing')

@section('title', 'About VisionLab — Enterprise CS Workspace')
@section('meta_description', 'Learn about VisionLab, our mission to democratize sandboxed container environments and provide responsible AI-assisted coding to CS cohorts.')

@section('styles')
<style>
    /* Section specific styles */
    .hero {
        position: relative;
        min-height: 75vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 8rem 2rem 4rem;
        overflow: hidden;
    }

    .hero-headline {
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }
    .section-sub {
        font-size: 1rem;
        color: var(--text-secondary);
        max-width: 720px;
        line-height: 1.7;
    }
    .section-sep {
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--border), transparent);
        max-width: 1280px;
        margin: 0 auto;
    }
    .section {
        max-width: 1280px;
        margin: 0 auto;
        padding: 6rem 2rem;
        position: relative;
        z-index: 10;
    }
    .section-heading {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 600;
        line-height: 1.15;
        letter-spacing: -0.02em;
        margin-bottom: 1.25rem;
    }

    /* Grid layouts */
    .grid-4 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 640px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .grid-4 { grid-template-columns: repeat(4, 1fr); } }

    /* Value Cards */
    .value-card { padding: 2.5rem 2rem; display: flex; flex-direction: column; gap: 1.5rem; }
    .value-icon { width: 44px; height: 44px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .value-title { font-size: 1.15rem; font-weight: 600; }
    .value-desc { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; max-width: 800px; margin: 0 auto; }
    @media (min-width: 768px) { .stats-grid { grid-template-columns: repeat(4, 1fr); } }
    .stat-item { text-align: center; display: flex; flex-direction: column; gap: 0.5rem; }
    .stat-val { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700; letter-spacing: -0.02em; }
    .stat-lbl { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.15em; font-weight: 600; }

    /* Story/Timeline */
    .timeline { position: relative; max-width: 800px; margin: 4rem auto 0; padding-left: 2rem; border-left: 1px solid rgba(255,255,255,0.08); }
    .timeline-item { position: relative; padding-bottom: 3.5rem; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot { position: absolute; left: calc(-2rem - 5px); top: 8px; width: 10px; height: 10px; border-radius: 50%; background: var(--background); border: 2.5px solid var(--purple); box-shadow: 0 0 10px var(--purple); }
    .timeline-dot.cyan { border-color: var(--cyan); box-shadow: 0 0 10px var(--cyan); }
    .timeline-dot.pink { border-color: var(--rose); box-shadow: 0 0 10px var(--rose); }
    .timeline-dot.emerald { border-color: var(--emerald); box-shadow: 0 0 10px var(--emerald); }
    .timeline-date { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem; letter-spacing: 0.05em; }
    .timeline-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #fff; }
    .timeline-desc { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; }

    /* Tech Stack */
    .tech-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.2rem; max-width: 900px; margin: 3rem auto 0; }
    .tech-badge { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 1.4rem; border-radius: 9999px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); font-size: 0.9rem; font-weight: 500; font-family: 'JetBrains Mono', monospace; transition: all 0.25s; }
    .tech-badge:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.04); transform: translateY(-1px); }

    /* Team */
    .team-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    @media (min-width: 640px) { .team-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .team-grid { grid-template-columns: repeat(4, 1fr); } }
    .team-card { padding: 2.2rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; }
    .team-avatar { width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: white; border: 2px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.4); }
    .team-name { font-size: 1.1rem; font-weight: 600; color: #fff; }
    .team-role { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.12em; font-weight: 600; }
</style>
@endsection

@section('content')
<!-- Background Grid & Glows -->
<div class="pointer-events-none absolute inset-0 overflow-hidden" style="height: 100%;">
    <div class="absolute left-1/2 h-[120%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.25]" style="top: -10%; transform: translateX(-50%) perspective(900px) rotateX(60deg); transform-origin: center top;"></div>
    <div aria-hidden="true" class="absolute left-1/4 top-10 h-[500px] w-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, var(--violet), transparent 70%);"></div>
    <div aria-hidden="true" class="absolute right-1/4 top-40 h-[500px] w-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, var(--cyan), transparent 70%);"></div>
</div>

<!-- HERO -->
<section class="hero">

    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto" class="reveal">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-surface px-3.5 py-1.5">
            <span class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground">our mission</span>
        </div>
        <h1 class="hero-headline text-gradient-hero font-display">
            Built for the <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">next generation</span> of computer science.
        </h1>
        <p class="section-sub mt-8 mx-auto text-center">
            VisionLab was engineered to bridge the gap between academic theory and real-world software engineering practice. We consolidate containerized sandbox environments, socratic AI tools, and multi-cursor collaboration into a unified, secure infrastructure.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- MISSION DETAIL -->
<section class="section">
    <div style="max-width:800px;margin:0 auto;text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">System Architecture</span>
        <h2 class="section-heading font-display metallic-text">Democratizing CS Infrastructure</h2>
        <p class="section-sub" style="margin:0 auto">
            Traditionally, universities suffer from configuration drift, local setup blockages, and excessive cloud server runtime bills. VisionLab replaces this fragmented workflow. We supply student-isolated containers powered by Nix, and real-time collaboration engines managed through secure enterprise policies.
        </p>
    </div>
</section>

<!-- STATS -->
<section class="section">
    <div class="stats-grid reveal">
        <div class="stat-item">
            <span class="stat-val text-gradient-purple-pink" data-target="38">0</span>
            <span class="stat-lbl">Universities</span>
        </div>
        <div class="stat-item">
            <span class="stat-val text-gradient-cyan" data-target="15000">0</span>
            <span class="stat-lbl">Active Students</span>
        </div>
        <div class="stat-item">
            <span class="stat-val text-gradient-purple-pink" data-target="2400000">0</span>
            <span class="stat-lbl">Commits Compiled</span>
        </div>
        <div class="stat-item">
            <span class="stat-val text-gradient-cyan" data-target="99">0</span>
            <span class="stat-lbl">Uptime Target</span>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- TIMELINE / STORY -->
<section class="section">
    <div style="text-align:center;margin-bottom:4rem" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">The Chronicle</span>
        <h2 class="section-heading font-display metallic-text">How VisionLab Evolved</h2>
    </div>
    
    <div class="timeline reveal">
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-date">Q1 2024 · RESEARCH</div>
            <h3 class="timeline-title">Project Conception</h3>
            <p class="timeline-desc">VisionLab started as a computer science research project investigating sandboxed multi-tenant runtimes, aiming to prevent host system command injection and optimize container resource limits.</p>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot cyan"></div>
            <div class="timeline-date">Q3 2025 · BETA STAGE</div>
            <h3 class="timeline-title">Active Roster Deployment</h3>
            <p class="timeline-desc">Scaled the platform to 1,200 concurrent student workspaces. Deployed Reverb-based cursor broadcasting and Jitsi video moderators for real-time lecture coding.</p>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot pink"></div>
            <div class="timeline-date">Q1 2026 · AUDIT & OPTIMIZATION</div>
            <h3 class="timeline-title">Enterprise Security Hardening</h3>
            <p class="timeline-desc">Introduced the human-in-the-loop Socratic AI mutation pipeline (`ai_pending_patches`), completed OWASP ASVS Level 2 security checks, and configured Nix-based package resolution targets.</p>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot emerald"></div>
            <div class="timeline-date">FUTURE · ROADMAP</div>
            <h3 class="timeline-title">Universal Academic Standards</h3>
            <p class="timeline-desc">Focusing on deep LTI (Learning Tools Interoperability) integration with Canvas/Moodle, automated assessment pipelines, and cryptographically signed biometric forensics.</p>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- VALUES -->
<section class="section">
    <div style="text-align:center;margin-bottom:4rem" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">Sovereign Core</span>
        <h2 class="section-heading font-display metallic-text">Platform Foundations</h2>
    </div>

    <div class="grid-4 reveal">
        <div class="card value-card glass-panel">
            <div class="value-icon" style="background:rgba(168,85,247,0.1);color:var(--purple)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0110 0v4"></path></svg>
            </div>
            <h3 class="value-title text-white">Absolute Isolation</h3>
            <p class="value-desc">Strict OS-level sandboxing on student workspaces. Drop all standard Docker privileges and bind custom resource quotas per student priority.</p>
        </div>
        <div class="card value-card glass-panel">
            <div class="value-icon" style="background:rgba(0,229,255,0.1);color:var(--cyan)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path></svg>
            </div>
            <h3 class="value-title text-white">Open Nix Systems</h3>
            <p class="value-desc">No proprietary vendor locks. Package configurations are defined declaratively in Nix files, allowing identical builds across all university clusters.</p>
        </div>
        <div class="card value-card glass-panel">
            <div class="value-icon" style="background:rgba(240,66,109,0.1);color:var(--pink)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <h3 class="value-title text-white">FERPA & GDPR Compliance</h3>
            <p class="value-desc">Zero telemetry tracking leaks. Workspace files, databases, and AI audit trails are stored inside your university GCP node boundary.</p>
        </div>
        <div class="card value-card glass-panel">
            <div class="value-icon" style="background:rgba(0,191,166,0.1);color:var(--emerald)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle;"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path></svg>
            </div>
            <h3 class="value-title text-white">Socratic Guardrails</h3>
            <p class="value-desc">AI agents must ask guiding questions rather than direct copy-paste solutions, helping students develop critical logical thinking.</p>
        </div>
    </div>
</section>

<!-- TECH STACK -->
<section class="section" style="background: rgba(255,255,255,0.015); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">The Engine</span>
        <h2 class="section-heading font-display metallic-text">Production Infrastructure Stack</h2>
        <p class="section-sub" style="margin:0 auto">A highly resilient core built with proven open-source technologies.</p>
        
        <div class="tech-grid">
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><path d="M16.5 9.4 7.55 4.24a1.79 1.79 0 0 0-2.5 1.55v12.42a1.79 1.79 0 0 0 2.5 1.55l8.95-5.16a1.79 1.79 0 0 0 0-3.1Z"></path></svg>
                Laravel 11
            </div>
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line><line x1="2" y1="14" x2="22" y2="14"></line><line x1="12" y1="10" x2="12" y2="22"></line></svg>
                Docker Compose
            </div>
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                Reverb WebSockets
            </div>
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                Redis Cache
            </div>
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path></svg>
                MySQL 8.0
            </div>
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                Nix Packages
            </div>
            <div class="tech-badge">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><path d="M21.17 19.17l-15-15A2 2 0 0 0 3 5.6v12.8a2 2 0 0 0 2 2h12.8a2 2 0 0 0 1.37-.56L21.17 19.17z"></path></svg>
                WebGL / Three.js
            </div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="section">
    <div style="text-align:center;margin-bottom:4rem" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--pink);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">Core Syndicate</span>
        <h2 class="section-heading font-display metallic-text">Engineering Syndicate</h2>
    </div>

    <div class="team-grid reveal">
        <div class="card team-card glass-panel">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--purple), var(--pink))">AS</div>
            <h3 class="team-name">Ayan S.</h3>
            <span class="team-role">Principal Systems Architect</span>
        </div>
        <div class="card team-card glass-panel">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--cyan), #3b82f6)">VK</div>
            <h3 class="team-name">Dr. Vance</h3>
            <span class="team-role">Head of Infrastructure Research</span>
        </div>
        <div class="card team-card glass-panel">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--emerald), #059669)">SC</div>
            <h3 class="team-name">Sarah Chen</h3>
            <span class="team-role">Lead DevSecOps Auditor</span>
        </div>
        <div class="card team-card glass-panel">
            <div class="team-avatar" style="background:linear-gradient(135deg, var(--pink), var(--purple))">LO</div>
            <h3 class="team-name">Liam O'Connor</h3>
            <span class="team-role">Senior Full-Stack Developer</span>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;padding:8rem 2rem; position:relative; overflow:hidden;">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute left-1/2 h-[200%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.1]" style="top: 0; transform: translateX(-50%) perspective(900px) rotateX(60deg);"></div>
    </div>
    <div style="position:relative;z-index:10;max-width:700px;margin:0 auto" class="reveal">
        <h2 class="section-heading font-display metallic-text">Ready to deploy?</h2>
        <p class="section-sub" style="margin:0 auto 3rem">Deploy a production-grade, sandboxed workspace system for your department. Complete LTI integration in under a day.</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Deploy Institution</a>
    </div>
</section>
@endsection

@section('scripts')
<script type="module">
    import * as THREE from 'three';

    // ── Intersection Observer for Scroll Reveal & Stats Counting ──
    document.addEventListener('DOMContentLoaded', () => {
        const stats = document.querySelectorAll('.stat-val');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('stats-grid')) {
                        stats.forEach(animateCount);
                    }
                }
            });
        }, { threshold: 0.1 });

        const statsGrid = document.querySelector('.stats-grid');
        if (statsGrid) observer.observe(statsGrid);

        function animateCount(el) {
            if (el.dataset.counted === 'true') return;
            el.dataset.counted = 'true';
            const target = parseInt(el.dataset.target);
            const duration = 2000;
            const start = 0;
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 4); // easeOutQuart
                const val = Math.floor(start + ease * (target - start));
                
                if (target >= 1000000) {
                    el.innerText = (val / 1000000).toFixed(1) + 'M+';
                } else if (target >= 1000) {
                    el.innerText = val.toLocaleString() + '+';
                } else {
                    el.innerText = val + (target === 99 ? '.9%' : '+');
                }

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }
            requestAnimationFrame(update);
        }
    });


</script>
@endsection
