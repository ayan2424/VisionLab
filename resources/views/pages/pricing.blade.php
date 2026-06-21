@extends('layouts.landing')

@section('title', 'Pricing — VisionLab Plans for Universities & CS Departments')
@section('meta_description', 'Flexible institutional pricing plans for VisionLab. Free tier for individual students, dedicated resources for CS departments and universities.')

@section('styles')
<style>
    /* Section specific styles */
    .hero {
        position: relative;
        min-height: 65vh;
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

    /* Pricing grid */
    .pricing-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; margin-top: 3rem; }
    @media (min-width: 1024px) { .pricing-grid { grid-template-columns: repeat(3, 1fr); align-items: stretch; } }
    
    .pricing-card {
        background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 1.25rem;
        padding: 3rem 2rem; display: flex; flex-direction: column; gap: 2rem; position: relative; overflow: hidden;
        transition: border-color 0.3s, background-color 0.3s, transform 0.1s; transform-style: preserve-3d;
        backdrop-filter: blur(12px);
    }
    .pricing-card:hover { border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.025); }
    .pricing-card.popular {
        border: 1px solid var(--purple);
        box-shadow: 0 0 35px rgba(155,93,229,0.12);
        background: linear-gradient(rgba(255, 255, 255, 0.02) 0%, rgba(155, 93, 229, 0.005) 100%);
    }
    .pricing-badge {
        position: absolute; top: 1.5rem; right: 1.5rem; background: var(--purple); color: white;
        font-size: 0.65rem; font-family: 'JetBrains Mono', monospace; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;
        padding: 0.3rem 0.8rem; border-radius: 9999px; box-shadow: 0 4px 12px rgba(155,93,229,0.3);
    }
    .plan-name { font-size: 1.3rem; font-weight: 600; color:#fff; }
    .plan-price { display: flex; align-items: baseline; gap: 0.25rem; }
    .price-num { font-size: 3rem; font-weight: 700; letter-spacing: -0.02em; color:#fff; }
    .price-period { font-size: 0.9rem; color: var(--text-muted); }
    .plan-desc { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; }
    .plan-features { display: flex; flex-direction: column; gap: 1rem; list-style: none; margin-top: auto; }
    .plan-features li { font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; color: var(--text-secondary); }
    .plan-features li::before { content: '✓'; color: var(--cyan); font-weight: bold; }
    .pricing-card.popular .plan-features li::before { color: var(--purple); }

    /* Feature Matrix */
    .table-container { overflow-x: auto; width: 100%; border: 1px solid var(--border); border-radius: 1.25rem; background: rgba(255,255,255,0.01); margin-top: 3rem; box-shadow: 0 15px 40px rgba(0,0,0,0.4); }
    .comp-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
    .comp-table th, .comp-table td { padding: 1.25rem 1.6rem; border-bottom: 1px solid var(--border); }
    .comp-table th { font-weight: 600; color: white; background: rgba(255,255,255,0.02); }
    .comp-table td { color: var(--text-secondary); }
    .comp-table td:first-child { font-weight: 500; color:#fff; }
    .check-icon { color: var(--cyan); font-weight: bold; }
    .cross-icon { color: var(--text-muted); }

    /* Accordion FAQ */
    .faq-accordion { max-width: 800px; margin: 3rem auto 0; display: flex; flex-direction: column; gap: 1rem; }
    .faq-item { border: 1px solid var(--border); border-radius: 0.75rem; background: rgba(255,255,255,0.01); overflow: hidden; transition: border-color 0.3s; }
    .faq-item:hover { border-color: rgba(255,255,255,0.12); }
    .faq-trigger { width: 100%; padding: 1.5rem; background: none; border: none; color: white; text-align: left; font-size: 1.05rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
    .faq-icon { font-size: 1.2rem; transition: transform 0.3s var(--ease-out-expo); color: var(--text-muted); }
    .faq-content { padding: 0 1.5rem; max-height: 0; overflow: hidden; transition: max-height 0.4s var(--ease-out-expo), padding 0.4s var(--ease-out-expo); color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; }
    .faq-item.active { border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.015); }
    .faq-item.active .faq-trigger .faq-icon { transform: rotate(45deg); color: white; }
    .faq-item.active .faq-content { padding-bottom: 1.5rem; }
</style>
@endsection

@section('content')
<!-- Background Grid & Glows -->
<div class="pointer-events-none absolute inset-0 overflow-hidden" style="height: 100%;">
    <div class="absolute left-1/2 h-[120%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.25]" style="top: -10%; transform: translateX(-50%) perspective(900px) rotateX(60deg); transform-origin: center top;"></div>
    <div aria-hidden="true" class="absolute left-1/4 top-10 h-[500px] w-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, var(--purple), transparent 70%);"></div>
    <div aria-hidden="true" class="absolute right-1/4 top-30 h-[500px] w-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, var(--cyan), transparent 70%);"></div>
</div>

<!-- HERO -->
<section class="hero">

    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto" class="reveal">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-surface px-3.5 py-1.5">
            <span class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground">transparent scaling</span>
        </div>
        <h1 class="hero-headline text-gradient-hero font-display">
            Predictable models, <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">aligned with academic</span> quotas.
        </h1>
        <p class="section-sub mt-8 mx-auto text-center">
            Deploy VisionLab with complete resource control. Free individual workspace nodes for students, with departmental plans tailored to your curriculum demands.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- PLANS -->
<section class="section">
    <div class="pricing-grid reveal">
        <!-- Starter Plan -->
        <div class="pricing-card tilt-card">
            <div>
                <h3 class="plan-name">Individual Starter</h3>
                <div class="plan-price mt-4">
                    <span class="price-num">$0</span>
                    <span class="price-period">/ student forever</span>
                </div>
            </div>
            <p class="plan-desc">For individual students and TAs running basic lab assignments. Includes standard workspace configurations.</p>
            <ul class="plan-features">
                <li>1 Core Sandboxed Workspace</li>
                <li>Nix Package Resolvers</li>
                <li>Standard Socratic AI Tutoring</li>
                <li>Shared Sandbox Queues</li>
                <li>1 GB Persistent Storage Volume</li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-secondary mt-8">Register Account</a>
        </div>

        <!-- Department Plan -->
        <div class="pricing-card tilt-card popular">
            <span class="pricing-badge">recommended</span>
            <div>
                <h3 class="plan-name">CS Department</h3>
                <div class="plan-price mt-4">
                    <span class="price-num">$8</span>
                    <span class="price-period">/ student monthly</span>
                </div>
            </div>
            <p class="plan-desc">Designed for course coordinators, TAs, and cohorts requiring dedicated server quotas and classroom rosters.</p>
            <ul class="plan-features">
                <li>Dedicated CPU/Memory Workspaces</li>
                <li>Socratic AI Faculty Budgets</li>
                <li>Reverb Multi-Cursor Presence</li>
                <li>15-Peer Jitsi Video Classrooms</li>
                <li>50 GB Persistent Storage Pools</li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-primary mt-8">Start 14-Day Trial</a>
        </div>

        <!-- Enterprise Plan -->
        <div class="pricing-card tilt-card">
            <div>
                <h3 class="plan-name">Institutional</h3>
                <div class="plan-price mt-4">
                    <span class="price-num">Custom</span>
                    <span class="price-period">/ yearly contract</span>
                </div>
            </div>
            <p class="plan-desc">For full university departments requiring dedicated GCP nodes, SSO integrations, and complete regulatory compliance audits.</p>
            <ul class="plan-features">
                <li>Dedicated Sandbox GCP Node Pools</li>
                <li>Canvas, Moodle & LTI Integration</li>
                <li>Custom VS Code Extension Registries</li>
                <li>Unlimited Jitsi JWT Peer Video</li>
                <li>Biometric Keystroke Dynamics logs</li>
            </ul>
            <a href="{{ route('contact') }}" class="btn btn-secondary mt-8">Contact Partnerships</a>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- DETAIL COMPARISON -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">The Comparison</span>
        <h2 class="section-heading font-display metallic-text">Detailed Plan Capabilities</h2>
    </div>

    <div class="table-container reveal">
        <table class="comp-table">
            <thead>
                <tr>
                    <th>Capabilities</th>
                    <th>Starter</th>
                    <th>Department</th>
                    <th>Institutional</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sandbox Volume Allocation</td>
                    <td>1 GB</td>
                    <td>50 GB (Pooled)</td>
                    <td>Custom Target limits</td>
                </tr>
                <tr>
                    <td>AI Socratic Prompt Help</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="check-icon">✓</span></td>
                </tr>
                <tr>
                    <td>Approved AI Patch Generation</td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="check-icon">✓</span> (TA review diffs)</td>
                    <td><span class="check-icon">✓</span> (Custom safety filters)</td>
                </tr>
                <tr>
                    <td>Embedded JWT Video Rooms</td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="check-icon">✓</span> (Max 15 peers)</td>
                    <td><span class="check-icon">✓</span> (Unlimited)</td>
                </tr>
                <tr>
                    <td>SSO & LTI Integration</td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="check-icon">✓</span></td>
                </tr>
                <tr>
                    <td>Audit Trail Analytics</td>
                    <td>Basic</td>
                    <td>Comprehensive</td>
                    <td>Full Forensics telemetry</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<div class="section-sep"></div>

<!-- FAQs -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:600;display:block;margin-bottom:1rem;font-family:'JetBrains Mono',monospace;">Platform FAQs</span>
        <h2 class="section-heading font-display metallic-text">Academic Pricing FAQs</h2>
    </div>

    <div class="faq-accordion reveal">
        <div class="faq-item">
            <button class="faq-trigger">
                <span>Is VisionLab free for standard student courses?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>Yes. Students can create individual accounts and provision standard sandboxed workspaces for free. Free accounts support core Nix package builds, standard Socratic AI support, and Reverb presence loops.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>What happens when a trial subscription finishes?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>When your Department plan trial concludes, your account degrades to the free Starter tier. Student data is preserved, though active workspace counts are capped at 1 until the curriculum subscription is renewed.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>Can we self-host container runners locally?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>Yes. Institutional plans support local self-hosting. We provide pre-compiled Nix-based profiles and Docker Compose topologies, allowing you to deploy secure workspace runners on your own server nodes inside the university network.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>Does the platform support FERPA and GDPR parameters?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>Absolutely. Academic privacy is central to VisionLab. All workspace files, databases, code logs, and AI history payloads are stored within your university's self-contained GCP cloud zone, preventing any external data harvesting.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;padding:8rem 2rem; position:relative; overflow:hidden;">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute left-1/2 h-[200%] w-[200%] -translate-x-1/2 grid-floor opacity-[0.1]" style="top: 0; transform: translateX(-50%) perspective(900px) rotateX(60deg);"></div>
    </div>
    <div style="position:relative;z-index:10;max-width:700px;margin:0 auto" class="reveal">
        <h2 class="section-heading font-display metallic-text">Need dedicated node plans?</h2>
        <p class="section-sub" style="margin:0 auto 3rem">Contact our engineering department to configure dedicated VM pools, custom memory parameters, and LTI integrations.</p>
        <a href="{{ route('contact') }}" class="btn btn-primary">Talk to Engineers</a>
    </div>
</section>
@endsection

@section('scripts')
<script type="module">
    import * as THREE from 'three';

    // ── FAQ Accordion Toggle ──
    document.addEventListener('DOMContentLoaded', () => {
        const triggers = document.querySelectorAll('.faq-trigger');
        triggers.forEach(tr => {
            tr.addEventListener('click', () => {
                const item = tr.parentElement;
                const content = item.querySelector('.faq-content');
                const isActive = item.classList.contains('active');

                // Close all other FAQ items
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-content').style.maxHeight = '0px';
                });

                if (!isActive) {
                    item.classList.add('active');
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            });
        });

        // ── 3D Card Tilt Inertia ──
        const cards = document.querySelectorAll('.tilt-card');
        cards.forEach(card => {
            card.addEventListener('mousemove', e => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left - (rect.width/2);
                const y = e.clientY - rect.top - (rect.height/2);
                const tiltX = -(y / (rect.height/2)) * 8;
                const tiltY = (x / (rect.width/2)) * 8;
                card.style.transform = `rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateY(-5px)`;
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = `rotateX(0deg) rotateY(0deg) translateY(0)`;
            });
        });
    });


</script>
@endsection
