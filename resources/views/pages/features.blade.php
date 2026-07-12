@extends('layouts.landing')

@section('title', 'Features — VisionLab IDE, AI Agent & Live Collaboration')
@section('meta_description', 'Explore VisionLab features: browser-based VS Code IDE, AI teaching assistant, and smart LMS integration.')

@section('styles')
<style>
    /* Section specific styles */
    .hero {
        position: relative;
        min-height: 70vh;
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

    /* Alternating showcase feature blocks */
    .feature-block {
        display: grid;
        grid-template-columns: 1fr;
        gap: 4rem;
        align-items: center;
        margin-bottom: 8rem;
    }
    .feature-block:last-child { margin-bottom: 0; }
    @media (min-width: 1024px) {
        .feature-block { grid-template-columns: 1fr 1.1fr; }
        .feature-block.reversed { direction: rtl; }
        .feature-block.reversed .feature-content { direction: ltr; }
        .feature-block.reversed .feature-visual { direction: ltr; }
    }
    .feature-content { display: flex; flex-direction: column; gap: 1.25rem; }
    .feature-tag { font-size: 0.75rem; color: var(--cyan); text-transform: uppercase; letter-spacing: 0.18em; font-weight: 700; font-family:'JetBrains Mono',monospace; }
    .feature-title { font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 600; letter-spacing: -0.02em; color:#fff; }
    .feature-desc { font-size: 0.95rem; color: var(--text-secondary); line-height: 1.7; }

    /* Custom Mocks inside Visuals */
    .feature-visual {
        background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 1.25rem;
        padding: 1.5rem; position: relative; overflow: hidden; width: 100%; aspect-ratio: 16/10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6); display: flex; flex-direction: column;
        backdrop-filter: blur(10px);
    }
    .mock-header { display: flex; align-items: center; gap: 6px; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); margin-bottom: 1rem; }
    .mock-dot { width: 8px; height: 8px; border-radius: 50%; }
    .mock-body { flex: 1; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; overflow: hidden; }

    /* Code highlight inside Visuals */
    .code-row { display: flex; gap: 1.2rem; line-height: 1.6; }
    .code-ln { color: var(--text-muted); user-select: none; width: 2.5ch; text-align: right; }
    .code-keyword { color: #c7a6f5; }
    .code-type { color: #ffd45e; }
    .code-func { color: #6ee7e0; }
    .code-string { color: #5eeac9; }
    .code-comment { color: var(--text-muted); font-style: italic; }

    /* Comparison Table */
    .table-container { overflow-x: auto; width: 100%; border: 1px solid var(--border); border-radius: 1.25rem; background: rgba(255,255,255,0.01); margin-top: 3rem; box-shadow: 0 15px 40px rgba(0,0,0,0.4); }
    .comp-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
    .comp-table th, .comp-table td { padding: 1.25rem 1.6rem; border-bottom: 1px solid var(--border); }
    .comp-table th { font-weight: 600; color: white; background: rgba(255,255,255,0.02); }
    .comp-table td { color: var(--text-secondary); }
    .comp-table tr:hover td { color: #fff; background: rgba(255,255,255,0.005); }
    .comp-table td:first-child { font-weight: 500; color:#fff; }
    .check-icon { color: var(--emerald); font-weight: bold; }
    .cross-icon { color: #f0426d; font-weight: bold; }

    /* Integrations Grid */
    .integration-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 3rem; }
    @media (min-width: 640px) { .integration-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1024px) { .integration-grid { grid-template-columns: repeat(6, 1fr); } }
    .integration-card { background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 0.75rem; padding: 1.5rem 1rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; transition: all 0.3s var(--ease-out-expo); backdrop-filter: blur(5px); }
    .integration-card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.03); transform: translateY(-3px); }
    .integration-icon { font-size: 1.75rem; }
    .integration-name { font-size: 0.85rem; font-weight: 600; color:#fff; }
</style>
@endsection

@section('content')
<!-- Background Grid & Glows -->
<div class="pointer-events-none absolute inset-0 overflow-hidden" style="height: 100%;">
    <div class="absolute left-1/2 h-[120%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.25]" style="top: -10%; transform: translateX(-50%) perspective(900px) rotateX(60deg); transform-origin: center top;"></div>
    <div aria-hidden="true" class="absolute left-1/3 top-20 h-[500px] w-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, var(--cyan), transparent 70%);"></div>
    <div aria-hidden="true" class="absolute right-1/3 top-60 h-[500px] w-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, var(--violet), transparent 70%);"></div>
</div>

<!-- HERO -->
<section class="hero">

    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto" class="reveal">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-surface px-3.5 py-1.5">
            <span class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground">Capabilities Matrix</span>
        </div>
        <h1 class="hero-headline text-gradient-hero font-display">
            Sovereign engineering, <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">natively compiled</span> for classrooms.
        </h1>
        <p class="section-sub mt-8 mx-auto text-center">
            VisionLab consolidates browser-based interactive IDE workspaces, sandboxed container isolation, and socratic AI assistance into a centralized platform.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- FEATURE LIST -->
<section class="section">
    <!-- Block 1: Sandboxed Workspaces -->
    <div class="feature-block reveal">
        <div class="feature-content">
            <span class="feature-tag">/ 01 · Isolation Core</span>
            <h2 class="feature-title">Student-Isolated Workspaces</h2>
            <p class="feature-desc">
                Every student and group project executes inside an isolated Docker container runner, managed via real-time resource quotas. Containers drop all standard root privileges, enforce a read-only root file system, and use Nix files to ensure uniform package resolution across the cohort.
            </p>
        </div>
        <div class="feature-visual">
            <div class="mock-header">
                <span class="mock-dot" style="background:#f0426d"></span>
                <span class="mock-dot" style="background:#ffd45e"></span>
                <span class="mock-dot" style="background:#00bfa6"></span>
                <span style="color:var(--text-secondary);font-size:0.65rem;margin-left:0.5rem;font-family:'JetBrains Mono',monospace;">dev.nix · config</span>
            </div>
            <div class="mock-body">
                <div class="code-row"><span class="code-ln">1</span><span class="code-comment"># Declarative Nix configuration for classroom environment</span></div>
                <div class="code-row"><span class="code-ln">2</span><span>{ pkgs ? import <span class="code-string">&lt;nixpkgs&gt;</span> {} }:</span></div>
                <div class="code-row"><span class="code-ln">3</span><span>pkgs.mkShell {</span></div>
                <div class="code-row"><span class="code-ln">4</span><span>  buildInputs = [</span></div>
                <div class="code-row"><span class="code-ln">5</span><span>    pkgs.nodejs_20</span></div>
                <div class="code-row"><span class="code-ln">6</span><span>    pkgs.python311</span></div>
                <div class="code-row"><span class="code-ln">7</span><span>    pkgs.cargo</span></div>
                <div class="code-row"><span class="code-ln">8</span><span>  ];</span></div>
                <div class="code-row"><span class="code-ln">9</span><span>}</span></div>
            </div>
        </div>
    </div>



    <!-- Block 3: Socratic AI Assistant -->
    <div class="feature-block reveal">
        <div class="feature-content">
            <span class="feature-tag">/ 03 · AI Governance</span>
            <h2 class="feature-title">Socratic Prompt Assistance</h2>
            <p class="feature-desc">
                Provide academic assistance without enabling plagiarism. VisionLab's Socratic AI model answers students by proposing logical prompts, asking diagnostic questions, and reviewing code design, controlled via customizable token budgets set by CS faculty.
            </p>
        </div>
        <div class="feature-visual">
            <div class="mock-header">
                <span style="color:var(--violet-light);font-size:0.65rem;font-weight:700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;"><rect x="3" y="11" width="18" height="10" rx="2"></rect><circle cx="12" cy="5" r="2"></circle><path d="M12 7v4M8 16h.01M16 16h.01"></path></svg>
                    Kernel AI Copilot
                </span>
            </div>
            <div class="mock-body" style="display:flex;flex-direction:column;gap:0.75rem;">
                <div style="background:rgba(255,255,255,0.02);padding:8px 12px;border-radius:8px;">
                    <span style="color:var(--text-muted);font-size:0.6rem;display:block;">Student: Why is my binary search returning IndexOutOfBounds?</span>
                    <span style="color:#fff;font-size:0.7rem;">[Code snippet: while(low &lt;= high) { mid = (low+high)/2; ... }]</span>
                </div>
                <div style="background:rgba(155,93,229,0.06);border:1px solid rgba(155,93,229,0.15);padding:8px 12px;border-radius:8px;">
                    <span style="color:var(--violet-light);font-size:0.6rem;display:block;">Kernel: Let\'s look at your arithmetic. What happens to "low + high" when searching a very large array? Could integer overflow occur? How might you write "mid" to prevent it?</span>
                </div>
            </div>
        </div>
    </div>



    <!-- Block 5: Smart Grading -->
    <div class="feature-block reveal">
        <div class="feature-content">
            <span class="feature-tag">/ 05 · Faculty Oversight</span>
            <h2 class="feature-title">Direct Diff Reviews & Analytics</h2>
            <p class="feature-desc">
                Review and grade assignments directly from snapshot histories. Compare code differences through dynamic diff panels and track structural patterns without cloning repositories or configuring local runtimes.
            </p>
        </div>
        <div class="feature-visual">
            <div class="mock-header">
                <span style="color:var(--text-secondary);font-size:0.65rem;font-weight:700">Assignment Diff Review</span>
            </div>
            <div class="mock-body" style="font-size:0.65rem">
                <div style="background:rgba(240,66,109,0.1);border-left:2px solid var(--rose);padding:3px 6px;border-radius:3px;color:var(--rose-light);margin-bottom:2px;">- return val * 2.0;</div>
                <div style="background:rgba(0,191,166,0.1);border-left:2px solid var(--emerald);padding:3px 6px;border-radius:3px;color:var(--emerald-light)">+ return Math.pow(val, 2);</div>
                <div style="margin-top:1.5rem;color:var(--text-muted);font-family:sans-serif;">Instructor Comment: Optimal complexity achieved. (Grade: 100/100)</div>
            </div>
        </div>
    </div>

    <!-- Block 6: Hardened Infrastructure -->
    <div class="feature-block reversed reveal">
        <div class="feature-content">
            <span class="feature-tag">/ 06 · Security</span>
            <h2 class="feature-title">OWASP ASVS Level 2 Hardening</h2>
            <p class="feature-desc">
                Enforce security limits on all levels of the workspace. Every file API execution goes through strict realpath canonical checks, preventing directory traversal. Workspaces drop all Linux capabilities and restrict network routing strictly between sandbox nodes.
            </p>
        </div>
        <div class="feature-visual" style="border-color: rgba(0,191,166,0.2)">
            <div class="mock-header" style="border-bottom-color: rgba(0,191,166,0.1)">
                <span style="color:var(--emerald);font-size:0.65rem;font-weight:700" class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 inline text-emerald" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Security Telemetry Log
                </span>
            </div>
            <div class="mock-body" style="color:var(--emerald-light);font-size:0.7rem">
                <div>[INFO] Ingesting file request: /var/workspace/src/app.py</div>
                <div>[INFO] canonical realpath check: OK</div>
                <div>[INFO] Dropping Linux capabilities check: dropped ALL</div>
                <div>[INFO] Container runtime user: UID 1000 (Non-privileged)</div>
                <div style="color:white;font-weight:bold;margin-top:0.5rem">[SECURE] Audit loop completed. Request authorized.</div>
            </div>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- COMPARISON TABLE -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">The Comparison</span>
        <h2 class="section-heading font-display metallic-text">How VisionLab Compares</h2>
        <p class="section-sub" style="margin:0 auto">A holistic review of tooling features built directly into our ecosystem.</p>
    </div>

    <div class="table-container reveal">
        <table class="comp-table">
            <thead>
                <tr>
                    <th>Capabilities</th>
                    <th>VisionLab</th>
                    <th>Google Classroom</th>
                    <th>GitHub Codespaces</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>In-browser VS Code Workspace</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="check-icon">✓</span></td>
                </tr>

                <tr>
                    <td>Socratic AI Guidance</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>

                <tr>
                    <td>Direct Diff Review Grading</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
                <tr>
                    <td>OWASP ASVS Level 2 Hardening</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="cross-icon">✗</span></td>
                    <td><span class="cross-icon">✗</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<div class="section-sep"></div>

<!-- INTEGRATIONS -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">Sovereign Bridges</span>
        <h2 class="section-heading font-display metallic-text">Seamless Integrations</h2>
        <p class="section-sub" style="margin:0 auto">Connect VisionLab with your institution's core services.</p>
    </div>

    <div class="integration-grid reveal">
        <div class="integration-card">
            <span class="integration-icon" style="color:var(--purple)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 14.7255 3.09032 17.1962 4.85857 19C5.02107 19.1625 5.09703 19.3905 5.06019 19.6174C5.00843 19.9366 5.00007 20.2599 5.00007 20.5882C5.00007 21.3679 5.63214 22 6.41177 22H12Z"></path></svg>
            </span>
            <span class="integration-name">Canvas LMS</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon" style="color:var(--cyan)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path></svg>
            </span>
            <span class="integration-name">Moodle LMS</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon" style="color:var(--pink)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </span>
            <span class="integration-name">Blackboard</span>
        </div>
        <div class="integration-card">
            <span class="integration-icon" style="color:var(--emerald)">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" style="width:24px; height:24px;"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
            </span>
            <span class="integration-name">GitHub API</span>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;padding:8rem 2rem; position:relative; overflow:hidden;">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute left-1/2 h-[200%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.1]" style="top: 0; transform: translateX(-50%) perspective(900px) rotateX(60deg);"></div>
    </div>
    <div style="position:relative;z-index:10;max-width:700px;margin:0 auto" class="reveal">
        <h2 class="section-heading font-display metallic-text">Provision sandbox instances</h2>
        <p class="section-sub" style="margin:0 auto 3rem">Empower your computer science department with sandboxed workspace nodes. Verify academic integrity natively.</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Try VisionLab Features</a>
    </div>
</section>
@endsection


