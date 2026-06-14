# VisionLab Coding Standards & UI/UX Directives

These strictures govern all code contributed to VisionLab. Deviations will fail the CI/CD pipeline.

## 1. User Interface (UI) & User Experience (UX)

### Frontend Theming & Color Psychology
The existing boring/basic aesthetics are strictly banned. The UI must represent ultimate professionalism, creativity, and a state-of-the-art premium experience.

- **Workspace / IDE Engine**: 
  - **Primary Accent**: **Orange**. The coding workspace container environment must strictly utilize Orange accents to differentiate it as the deep-focus "Lab" zone.
  
- **Frontend / Landing / Dashboards (Non-Workspace Pages)**:
  - **Theme Profile**: Avoid plain solid colors. The theme must feel highly creative and professional.
  - **Gradients**: Heavily utilize modern, premium gradients (similar to Google's latest gradient aesthetics—e.g., fluid meshes, smooth multi-stop blends like Deep Purple to Vibrant Pink/Blue, or sleek Silver/Graphite to Cyber Cyan).
  - **Dark Mode**: Strict Dark Mode (`#0a0a0a` or similar ultra-deep tones). Do not use `#000000` pure black.
  - **Material Elements**: Employ "Glassmorphism" (translucent backgrounds with blur filters) for floating elements, navbars, and modals. Everything must feel like it is floating off the canvas.
  - **Shapes & Geometry**: STRICTly use highly rounded, "pilled" shapes for buttons, badges, and cards. Avoid sharp corners anywhere in the frontend.
  - **Hero Section & 3D Elements**: The hero section must be an ultra-premium centerpiece featuring superb interactivity and 3D-type design elements that respond to mouse movement or scroll.
  - **Animations**: Use Intersection Observers to trigger subtle micro-animations and entrance fades. The app must feel alive, extremely interactive, and premium.

### Styling Technology
- Use **Tailwind CSS**. Avoid writing arbitrary custom CSS unless absolutely necessary for complex animations.
- Use **Vanilla JavaScript** (or Alpine.js if integrated) for DOM manipulation outside of complex Vue/React widget mounting points.

## 2. Backend (PHP/Laravel)

### Strict Typing
- **PHP 8.3 Features**: Explicitly declare return types, argument types, and utilize readonly classes/properties where applicable.
- `declare(strict_types=1);` is highly recommended at the top of domain-critical files.

### Laravel Ecosystem
- **Zero-Trust**: Do not write queries directly in controllers without passing through Laravel Policies via `$this->authorize()`.
- **Vendor Trash**: Absolutely **NO modifications** inside the `vendor/` directory. Use Service Providers, Overrides, or custom packages.
- **No Stubs**: Never use `// TODO` or `// Add logic here` in committed code. Write complete, production-ready blocks.

## 3. Security
- **Path Traversal Protection**: Any file I/O must validate paths using `realpath()` and assert they sit securely within the designated workspace volume.
- **XSS Protection**: Blade templating `{{ }}` escaping is mandatory. Avoid `{!! !!}` unless sanitizing output through HTMLPurifier.
- **SQL Injection**: Rely entirely on Eloquent ORM. Raw queries must use secure bindings (`DB::select('... ?', [$var])`).
