<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Flexible pricing plans for VisionLab. Free for individual students, institution plans for departments and universities.">
    <title>Pricing — VisionLab Plans for Universities & Teams</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,600;1,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Three.js via Import Map -->
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.164.1/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.164.1/examples/jsm/"
        }
    }
    </script>

    <style>
        :root {
            --bg: #030303;
            --surface: #0a0a0a;
            --surface-2: #111111;
            --border: rgba(255,255,255,0.06);
            --border-hover: rgba(255,255,255,0.12);
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #52525b;
            --purple: #a855f7;
            --pink: #ec4899;
            --cyan: #00e5ff;
            --cyan-glow: rgba(0,229,255,0.4);
            --emerald: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
        body {
            background: var(--bg);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        .font-serif-italic { font-family: 'Playfair Display', Georgia, serif; font-style: italic; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .text-gradient-hero {
            background: linear-gradient(135deg, var(--purple), var(--pink), var(--cyan));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .text-gradient-purple-pink {
            background: linear-gradient(to right, var(--purple), var(--pink));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .text-gradient-cyan {
            background: linear-gradient(to right, var(--cyan), #3b82f6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        /* Nav */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(3,3,3,0.8); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border); transition: all 0.3s;
        }
        .nav-inner { max-width: 1280px; margin: 0 auto; padding: 0 2rem; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .nav-brand { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white; }
        .nav-brand span { font-weight: 700; font-size: 1.1rem; letter-spacing: -0.02em; }
        .nav-brand .brand-slash { color: var(--text-muted); font-weight: 300; margin: 0 0.25rem; }
        .nav-brand .brand-edu { color: var(--text-secondary); font-weight: 400; font-size: 0.85rem; }
        .nav-links { display: none; align-items: center; gap: 2rem; }
        @media (min-width: 768px) { .nav-links { display: flex; } }
        .nav-links a { color: var(--text-secondary); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: white; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; text-decoration: none; transition: all 0.3s cubic-bezier(0.16,1,0.3,1); cursor: pointer; border: none; white-space: nowrap; width: 100%; }
        .btn-primary { background: var(--cyan); color: #000; box-shadow: 0 0 20px var(--cyan-glow), 0 0 60px rgba(0,229,255,0.15); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 30px var(--cyan-glow), 0 0 80px rgba(0,229,255,0.25); background: #33eeff; }
        .btn-secondary { background: transparent; color: white; border: 1px solid rgba(255,255,255,0.15); }
        .btn-secondary:hover { border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.03); }

        /* Sections */
        .section { max-width: 1280px; margin: 0 auto; padding: 6rem 2rem; position: relative; z-index: 10; }
        .section-heading { font-size: clamp(2.25rem, 5vw, 3.5rem); font-weight: 700; line-height: 1.1; letter-spacing: -0.03em; margin-bottom: 1.5rem; }
        .section-sub { font-size: 1rem; color: var(--text-secondary); max-width: 700px; line-height: 1.7; }
        .section-sep { width: 100%; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); max-width: 1280px; margin: 0 auto; }

        /* Hero */
        .hero { position: relative; min-height: 70vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; overflow: hidden; }
        .canvas-container { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
        .canvas-container canvas { display: block; width: 100%; height: 100%; }
        .hero-headline { font-size: clamp(3rem, 7vw, 5.25rem); font-weight: 900; line-height: 1.1; letter-spacing: -0.04em; }

        /* Pricing grid */
        .pricing-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; margin-top: 3rem; }
        @media (min-width: 1024px) { .pricing-grid { grid-template-columns: repeat(3, 1fr); align-items: stretch; } }
        
        .pricing-card {
            background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 1rem;
            padding: 3rem 2rem; display: flex; flex-direction: column; gap: 2rem; position: relative; overflow: hidden;
            transition: border-color 0.3s, background-color 0.3s; transform-style: preserve-3d;
        }
        .pricing-card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.03); }
        .pricing-card.popular {
            border: 2px solid var(--purple);
            box-shadow: 0 0 40px rgba(168,85,247,0.15);
        }
        .pricing-badge {
            position: absolute; top: 1.5rem; right: 1.5rem; background: var(--purple); color: white;
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
            padding: 0.3rem 0.75rem; border-radius: 9999px;
        }
        .plan-name { font-size: 1.25rem; font-weight: 700; }
        .plan-price { display: flex; align-items: baseline; gap: 0.25rem; }
        .price-num { font-size: 3rem; font-weight: 800; letter-spacing: -0.02em; }
        .price-period { font-size: 0.9rem; color: var(--text-muted); }
        .plan-desc { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; }
        .plan-features { display: flex; flex-direction: column; gap: 1rem; list-style: none; margin-top: auto; }
        .plan-features li { font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; color: var(--text-secondary); }
        .plan-features li::before { content: '✓'; color: var(--cyan); font-weight: bold; }
        .pricing-card.popular .plan-features li::before { color: var(--purple); }

        /* Feature Matrix */
        .table-container { overflow-x: auto; width: 100%; border: 1px solid var(--border); border-radius: 1rem; background: rgba(255,255,255,0.01); margin-top: 3rem; }
        .comp-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
        .comp-table th, .comp-table td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
        .comp-table th { font-weight: 700; color: white; background: rgba(255,255,255,0.01); }
        .comp-table td:first-child { font-weight: 600; }
        .check-icon { color: var(--cyan); font-weight: bold; }
        .cross-icon { color: var(--text-muted); }

        /* Accordion */
        .faq-accordion { max-width: 800px; margin: 3rem auto 0; display: flex; flex-direction: column; gap: 1rem; }
        .faq-item { border: 1px solid var(--border); border-radius: 0.75rem; background: rgba(255,255,255,0.01); overflow: hidden; }
        .faq-trigger { width: 100%; padding: 1.5rem; background: none; border: none; color: white; text-align: left; font-size: 1.05rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .faq-icon { font-size: 1.2rem; transition: transform 0.3s; color: var(--text-muted); }
        .faq-content { padding: 0 1.5rem; max-height: 0; overflow: hidden; transition: max-height 0.3s ease, padding 0.3s ease; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; }
        .faq-item.active .faq-trigger .faq-icon { transform: rotate(45deg); color: white; }
        .faq-item.active .faq-content { padding-bottom: 1.5rem; }

        /* Reveal animations */
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }

        /* Footer */
        .footer { border-top: 1px solid var(--border); padding: 4rem 2rem; background: var(--bg); position: relative; z-index: 10; }
        .footer-inner { max-width: 1280px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 2rem; }
        .footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .footer-links a:hover { color: white; }

        /* Cursor */
        .cursor-dot { position: fixed; width: 6px; height: 6px; background: var(--cyan); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%,-50%); box-shadow: 0 0 12px var(--cyan); display: none; }
        .cursor-ring { position: fixed; width: 36px; height: 36px; border: 1.5px solid rgba(0,229,255,0.4); border-radius: 50%; pointer-events: none; z-index: 9998; transform: translate(-50%,-50%); transition: all 0.2s cubic-bezier(0.16,1,0.3,1); display: none; }
    </style>
</head>
<body>

<!-- Cursor -->
<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- NAVIGATION -->
<nav class="nav" id="mainNav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <svg width="22" height="22" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#lg)"/><path d="M10 22V10l6 6 6-6v12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="lg" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#a855f7"/><stop offset="1" stop-color="#06b6d4"/></linearGradient></defs></svg>
            <span>VisionLab</span><span class="brand-slash">/</span><span class="brand-edu">edu</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('pricing') }}" class="active">Pricing</a>
            <a href="{{ route('docs') }}">Docs</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div style="display:flex;align-items:center;gap:1rem">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem;width:auto">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary" style="padding:0.5rem 1.5rem;font-size:0.8rem;width:auto">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem;width:auto">Deploy</a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="canvas-container" id="pricingHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Simple, <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">transparent</span> pricing.
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Free plan for individual students, self-hosted deployment modules for university departments.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- PLANS -->
<section class="section">
    <div class="pricing-grid reveal">
        <!-- Starter Plan -->
        <div class="pricing-card tilt-3d">
            <div class="plan-name">Starter</div>
            <div class="plan-price">
                <span class="price-num">$0</span>
                <span class="price-period">/ student</span>
            </div>
            <p class="plan-desc">For students looking to build code locally or join open-source research networks.</p>
            <ul class="plan-features">
                <li>1 Sandbox Workspace</li>
                <li>1GB Persistent Storage</li>
                <li>Community Socratic AI</li>
                <li>Basic Multi-cursor Sync</li>
            </ul>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="margin-top:2rem">Open Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-secondary" style="margin-top:2rem">Get Started Free</a>
            @endauth
        </div>

        <!-- Department Plan -->
        <div class="pricing-card popular tilt-3d">
            <span class="pricing-badge">Popular</span>
            <div class="plan-name" style="color:var(--purple)">Department</div>
            <div class="plan-price">
                <span class="price-num">$49</span>
                <span class="price-period">/ month</span>
            </div>
            <p class="plan-desc">For computing departments running labs, assessments, and structured grading loops.</p>
            <ul class="plan-features">
                <li>50 Sandbox Workspaces</li>
                <li>50GB Workspace Storage</li>
                <li>Full Socratic + Autonomous AI</li>
                <li>Embedded JWT Video Rooms</li>
                <li>Diff-based Review & Grading</li>
                <li>Priority 24/7 support</li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-primary" style="margin-top:2rem">Start 14-Day Trial</a>
        </div>

        <!-- Enterprise Plan -->
        <div class="pricing-card tilt-3d">
            <div class="plan-name">Enterprise</div>
            <div class="plan-price">
                <span class="price-num">Custom</span>
            </div>
            <p class="plan-desc">For large universities requiring single-sign on, custom Nix configurations, and local hosting templates.</p>
            <ul class="plan-features">
                <li>Unlimited Workspaces</li>
                <li>Custom Storage Volumes</li>
                <li>Self-Hosted AI Node Proxies</li>
                <li>SAML / OIDC Single Sign-On</li>
                <li>Nix OS Custom Blueprints</li>
                <li>Dedicated SLA & Support</li>
            </ul>
            <a href="{{ route('contact') }}" class="btn btn-secondary" style="margin-top:2rem">Contact Sales</a>
        </div>
    </div>
</section>

<div class="section-sep"></div>

<!-- MATRIX -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--purple);font-weight:700;display:block;margin-bottom:1rem">Feature Matrix</span>
        <h2 class="section-heading">Detailed Feature Comparison</h2>
    </div>

    <div class="table-container reveal">
        <table class="comp-table">
            <thead>
                <tr>
                    <th>Features</th>
                    <th>Starter</th>
                    <th>Department</th>
                    <th>Enterprise</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sandbox Storage Limits</td>
                    <td>1 GB</td>
                    <td>50 GB (pooled)</td>
                    <td>Custom allocation</td>
                </tr>
                <tr>
                    <td>AI Socratic Prompt Helpers</td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="check-icon">✓</span></td>
                    <td><span class="check-icon">✓</span></td>
                </tr>
                <tr>
                    <td>AI Autonomous Patch Generation</td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="check-icon">✓</span> (Approved by TA)</td>
                    <td><span class="check-icon">✓</span></td>
                </tr>
                <tr>
                    <td>Jitsi WebRTC Video Calls</td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="check-icon">✓</span> (Max 15 peers)</td>
                    <td><span class="check-icon">✓</span> (Unlimited)</td>
                </tr>
                <tr>
                    <td>Docker Container Management</td>
                    <td>Standard</td>
                    <td>Standard</td>
                    <td>Custom NixBlueprints</td>
                </tr>
                <tr>
                    <td>OAuth Integration (LTI)</td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="cross-icon">-</span></td>
                    <td><span class="check-icon">✓</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<div class="section-sep"></div>

<!-- FAQ -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.2em;color:var(--cyan);font-weight:700;display:block;margin-bottom:1rem">Frequently Asked</span>
        <h2 class="section-heading">Pricing FAQs</h2>
    </div>

    <div class="faq-accordion reveal">
        <div class="faq-item">
            <button class="faq-trigger">
                <span>Is VisionLab free for students?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>Yes. Students can create individual accounts for free. Free accounts include access to a sandboxed workspace node, core socratic AI chat helper elements, and standard collaborative presence frameworks.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>What happens after the trial?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>When your Department plan trial concludes, your account degrades to the Starter tier. No data is instantly erased, though workspace allocations are restricted to 1 until the subscription is renewed.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>Can we self-host VisionLab?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>Yes. Enterprise instances support self-hosting setups. We export docker-compose topologies compiled via nix profiles to deploy on local university hardware clusters.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>Do you offer education discounts?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>Yes. We offer up to 50% discount matrices for accredited universities and research groups. Contact our academic syndicates for details.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-trigger">
                <span>What payment methods do you accept?</span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-content">
                <p>We accept major credit cards (Visa, MasterCard, Amex) for monthly plans, and invoicing (wire transfer, bank drafts) for institutional accounts.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;padding:8rem 2rem">
    <div style="position:relative;z-index:10;max-width:700px;margin:0 auto" class="reveal">
        <h2 class="section-heading">Have More Questions?</h2>
        <p class="section-sub" style="margin:0 auto 3rem">Talk to our syndicate engineering department about deployment topologies.</p>
        <a href="{{ route('contact') }}" class="btn btn-primary" style="width:auto">Get in Touch</a>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('features') }}">Features</a>
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('docs') }}">Docs</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <p style="font-size:0.75rem;color:var(--text-muted)">&copy; 2026 VisionLab. Aptech Vision 2026 Competition Entry. All rights reserved.</p>
    </div>
</footer>

<!-- SCRIPTS -->
<script type="module">
    import * as THREE from 'three';

    // ── Mouse Cursor ──
    const dot = document.getElementById('cursorDot');
    const ring = document.getElementById('cursorRing');
    if (window.matchMedia('(pointer: fine)').matches) {
        dot.style.display = 'block';
        ring.style.display = 'block';
        document.addEventListener('mousemove', e => {
            dot.style.left = e.clientX + 'px';
            dot.style.top = e.clientY + 'px';
            setTimeout(() => {
                ring.style.left = e.clientX + 'px';
                ring.style.top = e.clientY + 'px';
            }, 50);
        });
        document.querySelectorAll('a, button, .card, .pricing-card').forEach(el => {
            el.addEventListener('mouseenter', () => ring.style.transform = 'translate(-50%,-50%) scale(1.5)');
            el.addEventListener('mouseleave', () => ring.style.transform = 'translate(-50%,-50%) scale(1)');
        });
    }

    // ── Reveal on Scroll ──
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(r => observer.observe(r));

    // ── 3D Card Tilt Effect ──
    const cards = document.querySelectorAll('.tilt-3d');
    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left - (rect.width / 2);
            const y = e.clientY - rect.top - (rect.height / 2);
            
            // Normalize inputs
            const rx = (y / (rect.height / 2)) * -10; // max 10 degrees
            const ry = (x / (rect.width / 2)) * 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-5px)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0)';
        });
    });

    // ── Accordion FAQ Toggles ──
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const trigger = item.querySelector('.faq-trigger');
        const content = item.querySelector('.faq-content');
        trigger.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close other items
            faqItems.forEach(other => {
                other.classList.remove('active');
                other.querySelector('.faq-content').style.maxHeight = '0px';
            });

            if (!isActive) {
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });

    // ── Three.js Hero Scene (Floating Torus & Octahedrons) ──
    (function initHeroScene() {
        const container = document.getElementById('pricingHeroCanvas');
        if (!container) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 10);

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        scene.add(new THREE.AmbientLight(0x404060, 0.4));
        const pl = new THREE.PointLight(0xa855f7, 2, 30); pl.position.set(3, 3, 5); scene.add(pl);
        const pl2 = new THREE.PointLight(0x00e5ff, 2, 30); pl2.position.set(-3, -3, 5); scene.add(pl2);

        // Geometries
        const geom1 = new THREE.TorusGeometry(1.5, 0.4, 8, 32);
        const geom2 = new THREE.OctahedronGeometry(1.2);

        const mat1 = new THREE.MeshStandardMaterial({ color: 0xa855f7, emissive: 0xa855f7, emissiveIntensity: 0.1, metalness: 0.8, roughness: 0.2 });
        const mat2 = new THREE.MeshStandardMaterial({ color: 0x00e5ff, emissive: 0x00e5ff, emissiveIntensity: 0.1, wireframe: true });

        const mesh1 = new THREE.Mesh(geom1, mat1);
        const mesh2 = new THREE.Mesh(geom2, mat2);

        mesh1.position.set(-3.5, 0.5, 0);
        mesh2.position.set(3.5, -0.5, 0);

        scene.add(mesh1);
        scene.add(mesh2);

        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', e => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        const clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);
            const t = clock.getElapsedTime();

            mesh1.rotation.y = t * 0.4;
            mesh1.rotation.x = t * 0.2;
            mesh1.position.y = 0.5 + Math.sin(t * 1.2) * 0.25;

            mesh2.rotation.x = -t * 0.3;
            mesh2.rotation.z = t * 0.2;
            mesh2.position.y = -0.5 + Math.sin(t * 1.5) * 0.2;

            camera.position.x += (mouseX * 1.2 - camera.position.x) * 0.02;
            camera.position.y += (-mouseY * 0.8 - camera.position.y) * 0.02;
            camera.lookAt(0, 0, 0);

            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
    })();
</script>

</body>
</html>
