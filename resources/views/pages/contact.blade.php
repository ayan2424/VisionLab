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
                    <a href="https://github.com" target="_blank" class="social-link" title="GitHub">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                    </a>
                    <a href="https://x.com" target="_blank" class="social-link" title="Twitter / X">𝕏</a>
                    <a href="https://discord.com" target="_blank" class="social-link" title="Discord">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.894.077.077 0 0 1-.008-.128c.126-.093.252-.19.372-.287a.075.075 0 0 1 .077-.011c3.92 1.793 8.18 1.793 12.061 0a.073.073 0 0 1 .078.009c.12.099.246.195.373.289a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.894.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.156-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.156 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.156-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.156 2.418z"/></svg>
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


