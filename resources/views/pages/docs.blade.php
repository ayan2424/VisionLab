@extends('layouts.landing')

@section('title', 'Documentation — VisionLab Developer Syndicate')
@section('meta_description', 'Developer manuals, architecture specifications, sandboxing guidelines, and LTI parameters for VisionLab.')

@section('styles')
<style>
    /* Docs Page Layout */
    .docs-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 3rem;
        max-width: 1280px;
        margin: 0 auto;
        padding: 4rem 2rem;
        position: relative;
        z-index: 10;
    }
    
    @media (min-width: 1024px) {
        .docs-layout {
            grid-template-columns: 280px 1fr;
        }
    }

    /* Sticky Sidebar */
    .docs-sidebar {
        position: sticky;
        top: 100px;
        height: calc(100vh - 140px);
        overflow-y: auto;
        padding-right: 1.5rem;
    }
    
    .docs-sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .docs-sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 2px;
    }

    .docs-nav-group {
        margin-bottom: 2rem;
    }
    
    .docs-nav-title {
        font-family: 'JetBrains Mono', monospace;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: var(--muted-foreground);
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .docs-nav-link {
        display: block;
        color: var(--muted-foreground);
        text-decoration: none;
        font-size: 13px;
        padding: 0.4rem 0;
        transition: all 0.2s var(--ease-out-expo);
        border-left: 2px solid transparent;
        padding-left: 0.75rem;
    }
    
    .docs-nav-link:hover {
        color: #fff;
        padding-left: 1rem;
    }
    
    .docs-nav-link.active {
        color: var(--cyan);
        border-left-color: var(--cyan);
        font-weight: 600;
        padding-left: 1rem;
    }

    /* Docs Content */
    .docs-content {
        max-width: 820px;
    }

    .docs-section {
        margin-bottom: 5rem;
        scroll-margin-top: 100px;
    }

    .docs-section-title {
        font-family: "Clash Display", sans-serif;
        font-size: clamp(1.75rem, 3vw, 2.5rem);
        font-weight: 600;
        letter-spacing: -0.02em;
        margin-bottom: 1.5rem;
        color: #fff;
    }

    .docs-para {
        font-size: 0.95rem;
        color: var(--muted-foreground);
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    /* Bullet List styling */
    .docs-list {
        margin-left: 1.5rem;
        margin-bottom: 1.5rem;
        color: var(--muted-foreground);
        font-size: 0.95rem;
    }
    .docs-list li {
        margin-bottom: 0.5rem;
    }
    .docs-list li strong {
        color: #fff;
    }

    /* Terminals & Code Blocks */
    .code-widget {
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin: 1.5rem 0;
        font-family: 'JetBrains Mono', monospace;
        position: relative;
        overflow: hidden;
    }
    
    .code-widget-header {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: var(--muted-foreground);
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .code-widget-body {
        font-size: 12.5px;
        color: #e4e4e7;
        line-height: 1.6;
        overflow-x: auto;
        white-space: pre;
    }
    
    .highlight-keyword { color: var(--rose-light); }
    .highlight-string { color: var(--emerald-light); }
    .highlight-comment { color: #71717a; }

    /* Alert callout */
    .callout-box {
        background: rgba(23, 195, 214, 0.02);
        border: 1px solid rgba(23, 195, 214, 0.15);
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        margin: 2rem 0;
        display: flex;
        gap: 1rem;
    }
    
    .callout-icon {
        color: var(--cyan);
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .callout-body {
        font-size: 0.9rem;
        color: var(--muted-foreground);
        line-height: 1.6;
    }
    
    .callout-title {
        font-weight: 600;
        color: #fff;
        margin-bottom: 0.25rem;
    }

    /* Hero header */
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
    <div class="canvas-container" id="docsHeroCanvas"></div>
    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Architectural <br><span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">developer guide.</span>
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Detailed specs on Nix sandboxing, Socratic AI prompts, Reverb WebSockets, LTI 1.3 integration parameters, and direct REST APIs.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- DOUBLE COLUMN DOCS LAYOUT -->
<div class="docs-layout">
    
    <!-- LEFT SIDEBAR -->
    <aside class="docs-sidebar">
        <div class="docs-nav-group">
            <div class="docs-nav-title">Onboarding</div>
            <a href="#quickstart" class="docs-nav-link active" onclick="updateActiveLink(this)">Quick Start</a>
            <a href="#lti-integration" class="docs-nav-link" onclick="updateActiveLink(this)">LTI 1.3 Integration</a>
        </div>
        
        <div class="docs-nav-group">
            <div class="docs-nav-title">Architecture</div>
            <a href="#sandboxed-workspaces" class="docs-nav-link" onclick="updateActiveLink(this)">Nix Sandboxing</a>
            <a href="#ai-assistant" class="docs-nav-link" onclick="updateActiveLink(this)">AI Agent Modes</a>
            <a href="#presence-sync" class="docs-nav-link" onclick="updateActiveLink(this)">Presence & Reverb</a>
        </div>
        
        <div class="docs-nav-group">
            <div class="docs-nav-title">API Primitives</div>
            <a href="#api-reference" class="docs-nav-link" onclick="updateActiveLink(this)">REST API Docs</a>
        </div>
    </aside>

    <!-- RIGHT CONTENT PANELS -->
    <main class="docs-content">
        
        <!-- QUICKSTART -->
        <section id="quickstart" class="docs-section reveal">
            <h2 class="docs-section-title">Quick Start</h2>
            <p class="docs-para">
                VisionLab is designed to boot isolated development workspaces for computer science cohorts within seconds. Universities can integrate the platform directly with their existing Learning Management Systems (Canvas, Moodle, Blackboard) or run it as a standalone container platform.
            </p>
            <p class="docs-para">
                To run a local development instance of the VisionLab kernel and proxy services on your machine, clone the repository and execute the environment bootstrapper.
            </p>
            
            <div class="code-widget">
                <div class="code-widget-header">
                    <span>Terminal</span>
                    <span>bash</span>
                </div>
                <div class="code-widget-body"><span class="highlight-comment"># Clone the repository and navigate to root</span>
git clone https://github.com/ayan2424/VisionLab.git
cd VisionLab

<span class="highlight-comment"># Run the environment setup script</span>
./setup.sh --dev-mode

<span class="highlight-comment"># Build assets and start Laravel Sail / Docker containers</span>
sail up -d
sail artisan migrate --seed</div>
            </div>

            <p class="docs-para">
                Once initialized, the local portal is accessible at <code class="font-mono text-cyan">http://localhost:8000</code>. You can sign in using pre-configured seed credentials for instructors, students, or system administrators.
            </p>
        </section>

        <!-- LTI 1.3 -->
        <section id="lti-integration" class="docs-section reveal">
            <h2 class="docs-section-title">LTI 1.3 LMS Integration</h2>
            <p class="docs-para">
                VisionLab complies with the IMS Global Learning Tools Interoperability (LTI) 1.3 standards. This allows automated single sign-on (SSO), dynamic course syncing, and programmatic grade passback from VisionLab workspaces directly into Canvas or Moodle.
            </p>
            <p class="docs-para">
                To bind a course cohort from your LMS, navigate to your administrator console in Canvas, add a developer key, and supply the following connection parameters:
            </p>

            <div class="code-widget">
                <div class="code-widget-header">
                    <span>Configuration Parameters</span>
                    <span>JSON</span>
                </div>
                <div class="code-widget-body">{
  <span class="highlight-keyword">"target_link_uri"</span>: <span class="highlight-string">"https://visionlab.edu/lti/launch"</span>,
  <span class="highlight-keyword">"oidc_initiation_url"</span>: <span class="highlight-string">"https://visionlab.edu/lti/login"</span>,
  <span class="highlight-keyword">"public_jwk_url"</span>: <span class="highlight-string">"https://visionlab.edu/lti/jwks"</span>,
  <span class="highlight-keyword">"scopes"</span>: [
    <span class="highlight-string">"https://purl.imsglobal.org/spec/lti-ags/scope/score"</span>,
    <span class="highlight-string">"https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly"</span>
  ]
}</div>
            </div>

            <p class="docs-para">
                Grade points scored during coding assignments or compiler evaluations are automatically forwarded back using LTI Outcomes services.
            </p>
        </section>

        <!-- NIX SANDBOXING -->
        <section id="sandboxed-workspaces" class="docs-section reveal">
            <h2 class="docs-section-title">Nix Sandboxing</h2>
            <p class="docs-para">
                CS Workspaces are managed dynamically by the <code class="font-mono text-cyan">CodeServerManager</code> service. Each student receives an isolated workspace derived from declarative Nix environments (<code class="font-mono text-cyan">dev.nix</code>). This guarantees exact compiler environments across the entire student roster.
            </p>
            
            <div class="callout-box">
                <div class="callout-icon">🛡️</div>
                <div class="callout-body">
                    <div class="callout-title">Container Hardening Matrix</div>
                    Every container runs with dropped privileges: <code class="font-mono">--security-opt no-new-privileges:true</code>, dropped capabilities (<code class="font-mono">--cap-drop ALL</code>), a read-only root filesystem, and strict memory limits. Any file API requests undergo <code class="font-mono">realpath()</code> validation.
                </div>
            </div>

            <p class="docs-para">
                A typical workspace container definition uses the following system blueprint:
            </p>

            <div class="code-widget">
                <div class="code-widget-header">
                    <span>Nix Configuration</span>
                    <span>dev.nix</span>
                </div>
                <div class="code-widget-body">{ pkgs ? import &lt;nixpkgs&gt; {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.php83
    pkgs.nodejs_20
    pkgs.python311
    pkgs.gcc
    pkgs.git
  ];

  shellHook = <span class="highlight-string">''
    echo "VisionLab Sandbox Environment Initialized."
  ''</span>;
}</div>
            </div>
        </section>

        <!-- AI AGENT MODES -->
        <section id="ai-assistant" class="docs-section reveal">
            <h2 class="docs-section-title">Governed AI Agent Modes</h2>
            <p class="docs-para">
                The integrated AI assistant is managed by the server to balance pedagogy with coding assistance. Instructors can configure one of three specific operational modes for each programming assignment:
            </p>
            
            <ul class="docs-list">
                <li><strong>Socratic Mode (Default):</strong> The assistant is forbidden from generating directly copy-pasteable blocks of code. Instead, it parses student errors and suggests architectural concepts, guiding the student to solve the problem themselves.</li>
                <li><strong>Plan Mode:</strong> The assistant is permitted to produce structured pseudocode and implementation blueprints, detailing step-by-step algorithms without supplying functional code files.</li>
                <li><strong>Agent Mutation Mode:</strong> Full sandbox permissions are enabled. The assistant proposed code mutations as diffs. Files are never mutated directly; all changes must pass through the `ai_pending_patches` queue for manual human inspection and approval via the diff viewer.</li>
            </ul>

            <div class="callout-box">
                <div class="callout-icon">🤖</div>
                <div class="callout-body">
                    <div class="callout-title">Patch Queue Safeguards</div>
                    AI mutations are bound by a 20-patch lifecycle safety limit. Any code changes require user approval. The only directory exempted is the workspace-scoped metadata file <code class="font-mono">.visionlab_memory.md</code>.
                </div>
            </div>
        </section>

        <!-- PRESENCE & REVERB -->
        <section id="presence-sync" class="docs-section reveal">
            <h2 class="docs-section-title">Presence & WebSockets</h2>
            <p class="docs-para">
                Real-time collaboration (multi-user cursor tracking, document sync, and workspace chat) is powered by **Laravel Reverb**, a high-performance native WebSocket server. Reverb handles connection multiplexing using Redis, enabling seamless classroom scaling.
            </p>
            <p class="docs-para">
                Client-side state synchronization broadcasts updates such as cursor movements every 80ms over client whispers. Document changes are sent as delta diffs to conserve packet overhead.
            </p>

            <div class="code-widget">
                <div class="code-widget-header">
                    <span>WebSocket Events</span>
                    <span>JavaScript / Echo</span>
                </div>
                <div class="code-widget-body">Echo.join(<span class="highlight-string">`workspace.${roomId}`</span>)
  .here((users) =&gt; {
    updateCollaboratorList(users);
  })
  .joining((user) =&gt; {
    triggerPresenceToast(<span class="highlight-string">`Joined: ${user.name}`</span>);
  })
  .listenForWhisper(<span class="highlight-string">'CursorMoved'</span>, (e) =&gt; {
    updateRemoteCursor(e.userId, e.x, e.y);
  });</div>
            </div>
        </section>

        <!-- REST API -->
        <section id="api-reference" class="docs-section reveal">
            <h2 class="docs-section-title">REST API Reference</h2>
            <p class="docs-para">
                VisionLab exposes secure endpoints protected by Sanctum API tokens. External administrative scripts or CI pipelines can trigger evaluations or monitor node telemetry using standard endpoints.
            </p>

            <div class="code-widget">
                <div class="code-widget-header">
                    <span>GET /api/v1/workspaces</span>
                    <span>cURL</span>
                </div>
                <div class="code-widget-body">curl -X GET https://visionlab.edu/api/v1/workspaces \
  -H <span class="highlight-string">'Authorization: Bearer vl_sec_7a2b9...'</span> \
  -H <span class="highlight-string">'Accept: application/json'</span></div>
            </div>

            <div class="code-widget">
                <div class="code-widget-header">
                    <span>Response JSON</span>
                    <span>application/json</span>
                </div>
                <div class="code-widget-body">{
  <span class="highlight-keyword">"status"</span>: <span class="highlight-string">"success"</span>,
  <span class="highlight-keyword">"data"</span>: [
    {
      <span class="highlight-keyword">"workspace_id"</span>: <span class="highlight-string">"ws_99818"</span>,
      <span class="highlight-keyword">"user_id"</span>: 142,
      <span class="highlight-keyword">"status"</span>: <span class="highlight-string">"active"</span>,
      <span class="highlight-keyword">"cpu_usage_pct"</span>: 4.8,
      <span class="highlight-keyword">"memory_mb"</span>: 512,
      <span class="highlight-keyword">"uptime_seconds"</span>: 3600
    }
  ]
}</div>
            </div>
        </section>
    </main>
</div>
@endsection

@section('scripts')
<script type="module">
    import * as THREE from 'three';

    // ── Update sidebar active state on scroll ──
    const sections = document.querySelectorAll('.docs-section');
    const navLinks = document.querySelectorAll('.docs-nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= sectionTop - 120) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });

    window.updateActiveLink = function(el) {
        navLinks.forEach(l => l.classList.remove('active'));
        el.classList.add('active');
    };

    // ── Three.js Hero Scene (Matrix-like falling particles) ──
    (function initHeroScene() {
        const container = document.getElementById('docsHeroCanvas');
        if (!container) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 10);

        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        container.appendChild(renderer.domElement);

        scene.add(new THREE.AmbientLight(0x404060, 0.5));
        const pl = new THREE.PointLight(0x00e5ff, 2, 25); pl.position.set(4, 2, 5); scene.add(pl);
        const pl2 = new THREE.PointLight(0xa855f7, 2, 25); pl2.position.set(-4, -2, 5); scene.add(pl2);

        // Falling code particles
        const pCount = 200;
        const pGeo = new THREE.BufferGeometry();
        const pPos = new Float32Array(pCount * 3);
        const speeds = [];
        for (let i = 0; i < pCount; i++) {
            pPos[i*3] = (Math.random() - 0.5) * 20;
            pPos[i*3+1] = (Math.random() - 0.5) * 12;
            pPos[i*3+2] = (Math.random() - 0.5) * 8 - 2;
            speeds.push(Math.random() * 0.02 + 0.005);
        }
        pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
        const pMat = new THREE.PointsMaterial({
            color: 0xa855f7,
            size: 0.06,
            transparent: true,
            opacity: 0.6,
            blending: THREE.AdditiveBlending
        });
        const particles = new THREE.Points(pGeo, pMat);
        scene.add(particles);

        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', e => {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        const clock = new THREE.Clock();
        function animate() {
            requestAnimationFrame(animate);

            // Falling animation
            const pos = particles.geometry.attributes.position.array;
            for (let i = 0; i < pCount; i++) {
                pos[i*3+1] -= speeds[i];
                if (pos[i*3+1] < -6) {
                    pos[i*3+1] = 6;
                }
            }
            particles.geometry.attributes.position.needsUpdate = true;

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
