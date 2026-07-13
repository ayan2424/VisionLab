<x-guest-layout>
    <x-slot name="title">Contact Us — VisionLab Support & Partnerships</x-slot>

    @push('styles')
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

    .hero-headline {
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }
</style>
</style>
    @endpush

<!-- HERO SECTION -->
<section class="hero">

    <div style="position:relative;z-index:10;max-width:900px;margin:0 auto">
        <h1 class="hero-headline reveal text-gradient-hero">
            Let's <span class="font-serif-italic" style="font-weight:400;text-transform:lowercase">collaborate.</span>
        </h1>
        <p class="section-sub reveal reveal-delay-1" style="margin: 2rem auto 0; text-align:center;">
            Have questions regarding server specs, LTI configuration, or cohort licensing? Speak to our engineering syndicate.
        </p>
    </div>
</section>

<div class="section-sep"></div>

<!-- CONTACT LAYOUT -->
<div class="contact-layout">
    <div class="contact-grid reveal">
        
        <!-- CONTACT FORM -->
        <div>
            <form class="contact-form" id="contactForm" action="mailto:admin@visioncode.ai" method="get" enctype="text/plain">
                <div class="form-group">
                    <label class="form-label" for="name">Your Name</label>
                    <input type="text" id="name" required class="form-control" placeholder="Your full name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">University Email</label>
                    <input type="email" id="email" required class="form-control" placeholder="your@university.edu">
                </div>
                <div class="form-group">
                    <label class="form-label" for="subject">Inquiry Sector</label>
                    <select id="subject" class="form-control" required>

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
                <button type="submit" class="btn btn-primary" style="width:100%; padding:0.8rem;">Send Inquiry</button>
            </form>
        </div>

        <!-- INFO CARDS -->
        <div class="info-column">
            <div class="info-card">
                <span class="info-lbl">Direct Contact</span>
                <a href="mailto:admin@visioncode.ai" class="info-val">admin@visioncode.ai</a>
            </div>
            
            <div class="info-card">
                <span class="info-lbl">Response Time</span>
                <span class="info-val">Within 24–48 Business Hours</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Support Hours</span>
                <span class="info-val">Mon–Fri, 9:00 AM – 6:00 PM PKT</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Based In</span>
                <span class="info-val">Karachi, Pakistan</span>
            </div>

            <div class="info-card">
                <span class="info-lbl">Source Repository</span>
                <div class="social-row">
                    <a href="https://github.com/ayan2424/VisionLab" target="_blank" class="social-link" title="GitHub Repository">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                    </a>
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
        <a href="{{ route('features') }}" class="quick-card">
            <h4 class="quick-title">Features Deck</h4>
            <p class="quick-desc">Explore containers, WebSockets sync, and Socratic AI assistants</p>
        </a>
    </div>
</div>
    </div>
</div>
</x-guest-layout>
