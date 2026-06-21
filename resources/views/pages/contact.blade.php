@extends('layouts.landing')

@section('title', 'Contact Us — VisionLab Support & Partnerships')
@section('meta_description', 'Contact the VisionLab engineering syndicate for deployments, SLAs, developer support, and institutional partnerships.')

@section('styles')
<style>
    /* Contact Page Layout */
    .contact-layout {
        max-width: 1280px;
        margin: 0 auto;
        padding: 4rem 2rem;
        position: relative;
        z-index: 10;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 4rem;
        margin-top: 2rem;
    }
    
    @media (min-width: 1024px) {
        .contact-grid {
            grid-template-columns: 1.2fr 1fr;
        }
    }

    /* Forms */
    .contact-form {
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        box-shadow: rgba(0, 0, 0, 0.5) 0px 30px 90px -30px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .form-label {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted-foreground);
    }
    
    .form-control {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        color: #fff;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.3s var(--ease-out-expo);
        font-family: inherit;
    }
    
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.2);
    }
    
    .form-control:focus {
        border-color: rgba(23, 195, 214, 0.4);
        box-shadow: 0 0 0 4px rgba(23, 195, 214, 0.08);
        background: rgba(255, 255, 255, 0.03);
    }
    
    select.form-control option {
        background: #050507;
        color: #fff;
    }

    /* Info Column */
    .info-column {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .info-card {
        background: rgba(255, 255, 255, 0.015);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: all 0.3s var(--ease-out-expo);
    }
    
    .info-card:hover {
        border-color: var(--border-hover);
        background: rgba(255, 255, 255, 0.025);
        transform: translateY(-2px);
    }
    
    .info-lbl {
        font-family: 'JetBrains Mono', monospace;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted-foreground);
        font-weight: 600;
    }
    
    .info-val {
        font-size: 1.25rem;
        font-weight: 600;
        text-decoration: none;
        color: #fff;
        transition: color 0.2s;
    }
    
    a.info-val:hover {
        color: var(--cyan);
    }

    /* Social channels */
    .social-row {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }
    
    .social-link {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted-foreground);
        text-decoration: none;
        transition: all 0.3s var(--ease-out-expo);
        font-size: 1.2rem;
    }
    
    .social-link:hover {
        border-color: #fff;
        color: #fff;
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-2px);
    }

    /* FAQ Quick links */
    .quick-links {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 4rem;
    }
    
    @media (min-width: 640px) {
        .quick-links {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    .quick-card {
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.75rem 1.25rem;
        text-align: center;
        text-decoration: none;
        color: #fff;
        transition: all 0.3s var(--ease-out-expo);
    }
    
    .quick-card:hover {
        border-color: var(--border-hover);
        background: rgba(255, 255, 255, 0.03);
        transform: translateY(-2px);
    }
    
    .quick-title {
        font-size: 1.05rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    
    .quick-desc {
        font-size: 0.8rem;
        color: var(--muted-foreground);
        line-height: 1.4;
    }

    /* Hero */
    .hero {
        position: relative;
        min-height: 50vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 8rem 2rem 4rem;
        overflow: hidden;
    }
    .canvas-container {
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
    }
    .canvas-container canvas {
        display: block;
        width: 100%;
        height: 100%;
    }
    .hero-headline {
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }
</style>
@endsection

@section('content')
<!-- HERO SECTION -->
<section class="hero">
    <div class="canvas-container" id="contactHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Let's <span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">collaborate.</span>
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Have questions regarding deployment SLAs, server specs, LTI configuration, or cohort licensing? Speak to our engineering syndicate.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- CONTACT LAYOUT -->
<div class="contact-layout">
    <div class="contact-grid reveal">
        
        <!-- CONTACT FORM -->
        <div>
            <form class="contact-form" id="contactForm" onsubmit="event.preventDefault(); window.vcToast ? window.vcToast('Inquiry dispatch successful. Our team will contact you within 12-24 hours.', 'success') : alert('Message sent successfully!'); this.reset();">
                <div class="form-group">
                    <label class="form-label" for="name">Your Name</label>
                    <input type="text" id="name" required class="form-control" placeholder="Dr. Sarah Vance">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">University Email</label>
                    <input type="email" id="email" required class="form-control" placeholder="svance@mit.edu">
                </div>
                <div class="form-group">
                    <label class="form-label" for="subject">Inquiry Sector</label>
                    <select id="subject" class="form-control" required>
                        <option value="Enterprise Deployment">Enterprise SLA & Deployments</option>
                        <option value="Academic Licensing">Academic Cohort Licensing</option>
                        <option value="LMS LTI Setup">LMS LTI 1.3 Integrations</option>
                        <option value="Security Audit">Security & Vulnerability Audits</option>
                        <option value="Developer Relations">General Support & Contribution</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="message">Detailed Inquiry</label>
                    <textarea id="message" rows="5" required class="form-control" placeholder="Describe your department size, estimated student rosters, cloud vs. on-prem host preferences, and target integration systems..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; padding:0.8rem;">Dispatch Inquiry</button>
            </form>
        </div>

        <!-- INFO CARDS -->
        <div class="info-column">
            <div class="info-card">
                <span class="info-lbl">Direct Helpline</span>
                <a href="mailto:support@visionlab.edu" class="info-val">support@visionlab.edu</a>
            </div>
            
            <div class="info-card">
                <span class="info-lbl">SLA Response Guarantee</span>
                <span class="info-val">Under 12 Hours (Tier-1 Enterprise)</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Helpline Hours</span>
                <span class="info-val">Mon-Fri, 9:00 AM - 6:00 PM PKT</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Administrative Headquarters</span>
                <span class="info-val">Karachi, Pakistan</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Syndicate channels</span>
                <div class="social-row">
                    <a href="https://github.com" target="_blank" class="social-link" title="GitHub">🐙</a>
                    <a href="https://x.com" target="_blank" class="social-link" title="Twitter / X">𝕏</a>
                    <a href="https://discord.com" target="_blank" class="social-link" title="Discord">💬</a>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK LINKS -->
    <div class="quick-links reveal">
        <a href="{{ route('docs') }}" class="quick-card">
            <h4 class="quick-title">Developer Docs</h4>
            <p class="quick-desc">Read sandbox installation guides and configuration Blueprints</p>
        </a>
        <a href="{{ route('pricing') }}" class="quick-card">
            <h4 class="quick-title">Pricing Cards</h4>
            <p class="quick-desc">Inspect university, department, and custom enterprise licensing tiers</p>
        </a>
        <a href="{{ route('features') }}" class="quick-card">
            <h4 class="quick-title">Features Deck</h4>
            <p class="quick-desc">Explore containers, WebSockets sync, and Socratic AI assistants</p>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script type="module">
    import * as THREE from 'three';

    // ── Three.js Hero Scene (Envelopes / Floating wireframe boxes) ──
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
@endsection
