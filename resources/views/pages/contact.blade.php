<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Contact VisionLab for demos, partnerships, support, or questions. We respond within 24 hours.">
    <title>Contact Us — Get in Touch with VisionLab</title>
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
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; text-decoration: none; transition: all 0.3s cubic-bezier(0.16,1,0.3,1); cursor: pointer; border: none; white-space: nowrap; }
        .btn-primary { background: var(--cyan); color: #000; box-shadow: 0 0 20px var(--cyan-glow), 0 0 60px rgba(0,229,255,0.15); width: 100%; }
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

        /* Contact Layout */
        .contact-grid { display: grid; grid-template-columns: 1fr; gap: 4rem; margin-top: 3rem; }
        @media (min-width: 1024px) { .contact-grid { grid-template-columns: 1.2fr 1fr; } }

        /* Forms */
        .contact-form {
            background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 1rem;
            padding: 2.5rem; display: flex; flex-direction: column; gap: 1.5rem;
        }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-label { font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); }
        .form-control {
            background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 0.5rem;
            padding: 0.75rem 1rem; color: white; font-size: 0.9rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-control:focus {
            border-color: rgba(0,229,255,0.3);
            box-shadow: 0 0 0 3px rgba(0,229,255,0.1);
        }
        select.form-control option { background: var(--surface); color: white; }

        /* Cards */
        .contact-info { display: flex; flex-direction: column; gap: 1.5rem; }
        .info-card {
            background: rgba(255,255,255,0.015); border: 1px solid var(--border); border-radius: 1rem;
            padding: 2rem; display: flex; flex-direction: column; gap: 0.5rem;
        }
        .info-lbl { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700; }
        .info-val { font-size: 1.15rem; font-weight: 700; text-decoration: none; color: white; }
        .info-card a.info-val:hover { color: var(--cyan); }

        /* Social Icons */
        .social-row { display: flex; gap: 1rem; margin-top: 1rem; }
        .social-link { width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); text-decoration: none; transition: all 0.3s; font-size: 1.1rem; }
        .social-link:hover { border-color: white; color: white; background: rgba(255,255,255,0.05); }

        /* Office Hours */
        .hours-card { border: 1px solid var(--border); border-radius: 1rem; padding: 2rem; background: rgba(255,255,255,0.005); text-align: center; }
        .hours-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; }
        .hours-desc { font-size: 0.85rem; color: var(--text-secondary); }

        /* FAQ Quick links */
        .quick-links { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 3rem; }
        .quick-card { background: rgba(255,255,255,0.01); border: 1px solid var(--border); border-radius: 0.75rem; padding: 1.5rem 1rem; text-align: center; text-decoration: none; color: white; transition: all 0.3s; }
        .quick-card:hover { border-color: var(--border-hover); background: rgba(255,255,255,0.03); transform: translateY(-2px); }
        .quick-title { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.25rem; }
        .quick-desc { font-size: 0.75rem; color: var(--text-muted); }

        /* Reveal animations */
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }

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
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('docs') }}">Docs</a>
            <a href="{{ route('contact') }}" class="active">Contact</a>
        </div>
        <div style="display:flex;align-items:center;gap:1rem">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.8rem">Deploy</a>
            @endauth
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="canvas-container" id="contactHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Let's <span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">build together.</span>
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Have a question about deploying sandboxed workspace containers at your university? Speak to our engineering syndicate.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- CONTACT LAYOUT -->
<section class="section">
    <div class="contact-grid reveal">
        <!-- Contact Form -->
        <div>
            <form class="contact-form" id="contactForm" onsubmit="event.preventDefault(); window.vcToast('Message sent! Our engineering team will contact you within 24 hours.', 'success'); this.reset();">
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" required class="form-control" placeholder="Ayan Khan">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">University Email</label>
                    <input type="email" id="email" required class="form-control" placeholder="ayan@university.edu">
                </div>
                <div class="form-group">
                    <label class="form-label" for="subject">Inquiry Type</label>
                    <select id="subject" class="form-control" required>
                        <option value="Demo Request">Demo Request</option>
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Partnership">Partnership</option>
                        <option value="Technical Support">Technical Support</option>
                        <option value="Bug Report">Bug Report</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="message">Message</label>
                    <textarea id="message" rows="5" required class="form-control" placeholder="Describe your department's size, requirements, and deployment targets..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Inquiry</button>
            </form>
        </div>

        <!-- Contact info cards -->
        <div class="contact-info">
            <div class="info-card">
                <span class="info-lbl">Direct Email</span>
                <a href="mailto:hello@visionlab.edu" class="info-val">hello@visionlab.edu</a>
            </div>
            
            <div class="info-card">
                <span class="info-lbl">SLA Response window</span>
                <span class="info-val">Within 24 Hours</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">HQ Location</span>
                <span class="info-val">Karachi, Pakistan</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Syndicate channels</span>
                <div class="social-row">
                    <a href="https://github.com" target="_blank" class="social-link">🐙</a>
                    <a href="https://x.com" target="_blank" class="social-link">𝕏</a>
                    <a href="https://discord.com" target="_blank" class="social-link">💬</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- OFFICE HOURS -->
<section class="section">
    <div class="hours-card reveal">
        <h3 class="hours-title">Academic Support Hours</h3>
        <p class="hours-desc">Our engineering team is active Mon-Fri, 9:00 AM - 6:00 PM PKT. Support queues are monitored 24/7 for Enterprise SLA tiers.</p>
    </div>
</section>

<div class="section-sep"></div>

<!-- FAQ QUICK LINKS -->
<section class="section">
    <div style="text-align:center" class="reveal">
        <h2 class="section-heading">Looking for Something Else?</h2>
        <p class="section-sub" style="margin:0 auto">Hop directly to other vital sectors of our documentation.</p>
    </div>

    <div class="quick-links reveal">
        <a href="{{ route('docs') }}" class="quick-card">
            <h4 class="quick-title">Documentation</h4>
            <p class="quick-desc">Read sandbox installation guides</p>
        </a>
        <a href="{{ route('pricing') }}" class="quick-card">
            <h4 class="quick-title">Pricing Cards</h4>
            <p class="quick-desc">Inspect department plans</p>
        </a>
        <a href="{{ route('features') }}" class="quick-card">
            <h4 class="quick-title">Features Showcase</h4>
            <p class="quick-desc">Explore collaborative features</p>
        </a>
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
        document.querySelectorAll('a, button, .card, .info-card, input, textarea, select').forEach(el => {
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

    // ── Three.js Hero Scene (Envelopes / Abstract floaters) ──
    (function initHeroScene() {
        const container = document.getElementById('contactHeroCanvas');
        if (!container) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 10);

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        scene.add(new THREE.AmbientLight(0x404060, 0.5));
        const pl = new THREE.PointLight(0xa855f7, 2, 25); pl.position.set(4, 2, 5); scene.add(pl);
        const pl2 = new THREE.PointLight(0x00e5ff, 2, 25); pl2.position.set(-4, -2, 5); scene.add(pl2);

        // Abstract Envelopes (Boxes deformed)
        const count = 6;
        const floaters = [];
        for (let i = 0; i < count; i++) {
            const geo = new THREE.BoxGeometry(1.6, 1.0, 0.1);
            const color = Math.random() > 0.5 ? 0xa855f7 : 0xec4899;
            const mat = new THREE.MeshStandardMaterial({
                color: color,
                emissive: color,
                emissiveIntensity: 0.15,
                metalness: 0.7,
                roughness: 0.3,
                wireframe: Math.random() > 0.6
            });
            const m = new THREE.Mesh(geo, mat);
            m.position.set(
                (Math.random() - 0.5) * 14,
                (Math.random() - 0.5) * 8,
                (Math.random() - 0.5) * 6 - 2
            );
            m.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, 0);
            m.userData = {
                rx: Math.random() * 0.008 + 0.002,
                ry: Math.random() * 0.008 + 0.002,
                speedY: Math.random() * 0.002 + 0.001,
                offset: Math.random() * Math.PI
            };
            scene.add(m);
            floaters.push(m);
        }

        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', e => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        const clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);
            const t = clock.getElapsedTime();

            floaters.forEach(f => {
                f.rotation.x += f.userData.rx;
                f.rotation.y += f.userData.ry;
                f.position.y += Math.sin(t * 1.2 + f.userData.offset) * 0.002;
            });

            camera.position.x += (mouseX * 1.5 - camera.position.x) * 0.02;
            camera.position.y += (-mouseY * 1.0 - camera.position.y) * 0.02;
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
