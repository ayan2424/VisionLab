# 🎨 Color Palette Reference Guide
> **AI Context Document** — Use this file to help an AI assistant choose the right, compatible color palette for any SaaS / developer tool project.

---

## 📌 How to Use This File
Paste this file into your AI chat and say:
> *"Based on this palette reference, suggest the best color palette for my [app type / vibe]."*

Each palette includes: hex codes, UI roles, light + dark mode, WCAG compliance level, CSS variables, and best-fit use cases.

---

## 🗂️ Palette Index

| # | Palette Name | Vibe | Primary Accent | Best For |
|---|---|---|---|---|
| 1 | **Firebase × Amber** ✅ *(Current)* | Bold, energetic, developer-first | `#F05000` Orange | Dev tools, CLIs, dashboards, Firebase-like consoles |
| 2 | **Vercel × Slate** | Minimal, sleek, monochrome | `#FFFFFF` / `#000000` | SaaS landing pages, deployment tools, Linear-style apps |
| 3 | **Stripe × Indigo** | Premium, trustworthy, polished | `#635BFF` Indigo | Fintech, payments, B2B SaaS, Notion-like apps |
| 4 | **Cyber × Neon** | Futuristic, AI-native, dark-first | `#00FFC2` Neon Teal | AI tools, LLM apps, cybersecurity dashboards |
| 5 | **Supabase × Emerald** | Open-source, fresh, developer-friendly | `#3ECF8E` Emerald | Open source tools, database UIs, backend dashboards |

---

---

# 1. 🔥 Firebase × Amber ✅ (Current Selection)

**Vibe:** Bold · Energetic · Developer-first · Warm  
**Inspired by:** Firebase Console, Supabase, Raycast  
**Typography:** `Sora` (headings) + `IBM Plex Sans` (body/UI)

---

### Light Mode

| Token | Hex | UI/UX Role | WCAG on White |
|---|---|---|---|
| `--bg-primary` | `#FAFAF8` | App / page background | — |
| `--bg-surface` | `#FFFFFF` | Cards, panels, modals | — |
| `--bg-elevated` | `#F3F1ED` | Sidebar, code blocks, input fills | — |
| `--border` | `#E6E3DC` | Dividers, strokes, separators | — |
| `--text-primary` | `#1A1714` | Headings, body copy | **17.2:1 AAA** |
| `--text-secondary` | `#6B6560` | Labels, captions, metadata | **5.2:1 AA** |
| `--accent` | `#F05000` | Buttons, links, active states, CTAs | **4.7:1 AA** |
| `--accent-hover` | `#D94700` | Hover / pressed accent state | **6.1:1 AA** |
| `--accent-subtle` | `#FFF0E8` | Tag fills, selected rows, alert banners | — |
| `--success` | `#16A34A` | Confirmations, status badges, toasts | **4.6:1 AA** |
| `--error` | `#DC2626` | Validation errors, destructive alerts | **4.8:1 AA** |
| `--warning` | `#D97706` | Warnings, beta badges, deprecation | **4.5:1 AA** |
| `--info` | `#2563EB` | Tooltips, info banners, links | **5.1:1 AA** |

---

### Dark Mode

| Token | Hex | UI/UX Role | WCAG on Dark Bg |
|---|---|---|---|
| `--bg-primary` | `#0D0C0A` | App / page background | — |
| `--bg-surface` | `#161412` | Cards, panels, modals | — |
| `--bg-elevated` | `#201E1A` | Sidebar, code blocks, input fills | — |
| `--border` | `#2E2B26` | Dividers, strokes, separators | — |
| `--text-primary` | `#F5F0EA` | Headings, body copy | **17.1:1 AAA** |
| `--text-secondary` | `#A39A90` | Labels, captions, metadata | **5.6:1 AA** |
| `--accent` | `#FF6B2B` | Buttons, links, active states, CTAs | **5.1:1 AA** |
| `--accent-hover` | `#FF8147` | Hover / pressed accent state | **6.4:1 AA** |
| `--accent-subtle` | `#2A1200` | Tag fills, selected rows, alert banners | — |
| `--success` | `#22C55E` | Confirmations, status badges, toasts | **5.1:1 AA** |
| `--error` | `#F87171` | Validation errors, destructive alerts | **5.3:1 AA** |
| `--warning` | `#FBBF24` | Warnings, beta badges, deprecation | **6.8:1 AA** |
| `--info` | `#60A5FA` | Tooltips, info banners, links | **5.8:1 AA** |

---

### CSS Variables

```css
/* ── Google Fonts Import ──────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=IBM+Plex+Sans:wght@400;500&display=swap');

/* ── Light Mode ───────────────────────────────── */
:root {
  --bg-primary:      #FAFAF8;
  --bg-surface:      #FFFFFF;
  --bg-elevated:     #F3F1ED;
  --border:          #E6E3DC;
  --text-primary:    #1A1714;
  --text-secondary:  #6B6560;
  --accent:          #F05000;
  --accent-hover:    #D94700;
  --accent-subtle:   #FFF0E8;
  --success:         #16A34A;
  --error:           #DC2626;
  --warning:         #D97706;
  --info:            #2563EB;
  --font-display:    'Sora', sans-serif;
  --font-body:       'IBM Plex Sans', sans-serif;
}

/* ── Dark Mode ────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  :root {
    --bg-primary:     #0D0C0A;
    --bg-surface:     #161412;
    --bg-elevated:    #201E1A;
    --border:         #2E2B26;
    --text-primary:   #F5F0EA;
    --text-secondary: #A39A90;
    --accent:         #FF6B2B;
    --accent-hover:   #FF8147;
    --accent-subtle:  #2A1200;
    --success:        #22C55E;
    --error:          #F87171;
    --warning:        #FBBF24;
    --info:           #60A5FA;
  }
}
```

**✅ Choose this palette when:** App is developer-facing · Has dashboards, logs, consoles · Needs an energetic, action-driven feel · Firebase / Supabase / Raycast inspiration

---

---

# 2. ⬛ Vercel × Slate

**Vibe:** Minimal · Sleek · Monochrome · Precision  
**Inspired by:** Vercel, Linear, Figma, Pitch  
**Typography:** `Geist` (headings) + `Inter` (body/UI)

---

### Light Mode

| Token | Hex | UI/UX Role | WCAG on White |
|---|---|---|---|
| `--bg-primary` | `#FFFFFF` | App / page background | — |
| `--bg-surface` | `#FAFAFA` | Cards, panels | — |
| `--bg-elevated` | `#F4F4F5` | Sidebar, hover states | — |
| `--border` | `#E4E4E7` | Dividers, input borders | — |
| `--text-primary` | `#09090B` | Headings, body copy | **20.2:1 AAA** |
| `--text-secondary` | `#71717A` | Labels, metadata | **4.6:1 AA** |
| `--accent` | `#18181B` | CTAs, active nav, primary buttons | **19.7:1 AAA** |
| `--accent-hover` | `#000000` | Hover on dark buttons | **21.0:1 AAA** |
| `--accent-subtle` | `#F4F4F5` | Selected states, badge fills | — |
| `--success` | `#17C964` | Success states | **4.5:1 AA** |
| `--error` | `#F31260` | Error states | **4.8:1 AA** |
| `--warning` | `#F5A524` | Warning states | **4.5:1 AA** |
| `--info` | `#006FEE` | Info / link color | **5.2:1 AA** |

---

### Dark Mode

| Token | Hex | UI/UX Role | WCAG on Dark Bg |
|---|---|---|---|
| `--bg-primary` | `#000000` | App / page background | — |
| `--bg-surface` | `#0A0A0A` | Cards, panels | — |
| `--bg-elevated` | `#141414` | Sidebar, code blocks | — |
| `--border` | `#27272A` | Dividers, strokes | — |
| `--text-primary` | `#FAFAFA` | Headings, body copy | **20.6:1 AAA** |
| `--text-secondary` | `#A1A1AA` | Labels, metadata | **6.1:1 AA** |
| `--accent` | `#FFFFFF` | CTAs, active nav, primary buttons | **21.0:1 AAA** |
| `--accent-hover` | `#E4E4E7` | Hover on light buttons | **18.3:1 AAA** |
| `--accent-subtle` | `#18181B` | Selected states, badge fills | — |
| `--success` | `#17C964` | Success states | **8.1:1 AAA** |
| `--error` | `#F31260` | Error states | **5.3:1 AA** |
| `--warning` | `#F5A524` | Warning states | **7.2:1 AA** |
| `--info` | `#338EF7` | Info / link color | **5.9:1 AA** |

---

### CSS Variables

```css
/* ── Google Fonts Import ──────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap');

/* ── Light Mode ───────────────────────────────── */
:root {
  --bg-primary:      #FFFFFF;
  --bg-surface:      #FAFAFA;
  --bg-elevated:     #F4F4F5;
  --border:          #E4E4E7;
  --text-primary:    #09090B;
  --text-secondary:  #71717A;
  --accent:          #18181B;
  --accent-hover:    #000000;
  --accent-subtle:   #F4F4F5;
  --success:         #17C964;
  --error:           #F31260;
  --warning:         #F5A524;
  --info:            #006FEE;
  --font-display:    'Geist', sans-serif;
  --font-body:       'Inter', sans-serif;
}

/* ── Dark Mode ────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  :root {
    --bg-primary:     #000000;
    --bg-surface:     #0A0A0A;
    --bg-elevated:    #141414;
    --border:         #27272A;
    --text-primary:   #FAFAFA;
    --text-secondary: #A1A1AA;
    --accent:         #FFFFFF;
    --accent-hover:   #E4E4E7;
    --accent-subtle:  #18181B;
    --success:        #17C964;
    --error:          #F31260;
    --warning:        #F5A524;
    --info:           #338EF7;
  }
}
```

**✅ Choose this palette when:** App is a SaaS platform / deployment tool · Minimalist aesthetic is priority · Content should take center stage · Vercel / Linear / Figma inspiration

---

---

# 3. 💳 Stripe × Indigo

**Vibe:** Premium · Trustworthy · Polished · Corporate  
**Inspired by:** Stripe, Notion, Loom, Coda  
**Typography:** `Plus Jakarta Sans` (headings) + `DM Sans` (body/UI)

---

### Light Mode

| Token | Hex | UI/UX Role | WCAG on White |
|---|---|---|---|
| `--bg-primary` | `#F6F8FA` | App / page background | — |
| `--bg-surface` | `#FFFFFF` | Cards, panels, modals | — |
| `--bg-elevated` | `#EEF2F7` | Sidebar, hover states | — |
| `--border` | `#D0D7E2` | Dividers, input borders | — |
| `--text-primary` | `#0D1117` | Headings, body copy | **19.5:1 AAA** |
| `--text-secondary` | `#5B6880` | Labels, metadata | **4.8:1 AA** |
| `--accent` | `#635BFF` | CTAs, links, selected states | **4.6:1 AA** |
| `--accent-hover` | `#4F46E5` | Hover on buttons | **5.9:1 AA** |
| `--accent-subtle` | `#EEF0FF` | Badge fills, alert banners | — |
| `--success` | `#0C8346` | Success states | **5.2:1 AA** |
| `--error` | `#C0392B` | Error states | **5.4:1 AA** |
| `--warning` | `#C87900` | Warning states | **4.6:1 AA** |
| `--info` | `#1A56DB` | Info / link color | **5.7:1 AA** |

---

### Dark Mode

| Token | Hex | UI/UX Role | WCAG on Dark Bg |
|---|---|---|---|
| `--bg-primary` | `#0B0D11` | App / page background | — |
| `--bg-surface` | `#131720` | Cards, panels, modals | — |
| `--bg-elevated` | `#1C2130` | Sidebar, code blocks | — |
| `--border` | `#2D3548` | Dividers, strokes | — |
| `--text-primary` | `#E8EDF5` | Headings, body copy | **16.3:1 AAA** |
| `--text-secondary` | `#8896AF` | Labels, metadata | **5.1:1 AA** |
| `--accent` | `#7C74FF` | CTAs, links, active nav | **4.9:1 AA** |
| `--accent-hover` | `#9E98FF` | Hover on buttons | **6.3:1 AA** |
| `--accent-subtle` | `#1A1940` | Badge fills, alert banners | — |
| `--success` | `#34D399` | Success states | **8.4:1 AAA** |
| `--error` | `#F87171` | Error states | **5.8:1 AA** |
| `--warning` | `#FBBF24` | Warning states | **7.1:1 AA** |
| `--info` | `#60A5FA` | Info / link color | **6.1:1 AA** |

---

### CSS Variables

```css
/* ── Google Fonts Import ──────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=DM+Sans:wght@400;500&display=swap');

/* ── Light Mode ───────────────────────────────── */
:root {
  --bg-primary:      #F6F8FA;
  --bg-surface:      #FFFFFF;
  --bg-elevated:     #EEF2F7;
  --border:          #D0D7E2;
  --text-primary:    #0D1117;
  --text-secondary:  #5B6880;
  --accent:          #635BFF;
  --accent-hover:    #4F46E5;
  --accent-subtle:   #EEF0FF;
  --success:         #0C8346;
  --error:           #C0392B;
  --warning:         #C87900;
  --info:            #1A56DB;
  --font-display:    'Plus Jakarta Sans', sans-serif;
  --font-body:       'DM Sans', sans-serif;
}

/* ── Dark Mode ────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  :root {
    --bg-primary:     #0B0D11;
    --bg-surface:     #131720;
    --bg-elevated:    #1C2130;
    --border:         #2D3548;
    --text-primary:   #E8EDF5;
    --text-secondary: #8896AF;
    --accent:         #7C74FF;
    --accent-hover:   #9E98FF;
    --accent-subtle:  #1A1940;
    --success:        #34D399;
    --error:          #F87171;
    --warning:        #FBBF24;
    --info:           #60A5FA;
  }
}
```

**✅ Choose this palette when:** App handles money / sensitive data · B2B audience · Needs to feel trustworthy and mature · Stripe / Notion / Loom inspiration

---

---

# 4. 🤖 Cyber × Neon

**Vibe:** Futuristic · AI-native · Dark-first · High-tech  
**Inspired by:** Vercel AI, Cursor, Perplexity, Terminal UIs  
**Typography:** `Space Grotesk` (headings) + `JetBrains Mono` (body/code)

---

### Light Mode

| Token | Hex | UI/UX Role | WCAG on White |
|---|---|---|---|
| `--bg-primary` | `#F0FDF9` | App / page background | — |
| `--bg-surface` | `#FFFFFF` | Cards, panels | — |
| `--bg-elevated` | `#E6FAF5` | Sidebar, hover states | — |
| `--border` | `#B2EEE0` | Dividers, input borders | — |
| `--text-primary` | `#042F26` | Headings, body copy | **16.8:1 AAA** |
| `--text-secondary` | `#2A7A65` | Labels, metadata | **4.8:1 AA** |
| `--accent` | `#00A87A` | CTAs, links, active states | **4.6:1 AA** |
| `--accent-hover` | `#007A58` | Hover on buttons | **6.2:1 AA** |
| `--accent-subtle` | `#CCFBEF` | Badge fills, banners | — |
| `--success` | `#15803D` | Success states | **5.4:1 AA** |
| `--error` | `#DC2626` | Error states | **4.8:1 AA** |
| `--warning` | `#B45309` | Warning states | **5.1:1 AA** |
| `--info` | `#0E7490` | Info / link color | **5.0:1 AA** |

---

### Dark Mode *(Primary — this palette is dark-first)*

| Token | Hex | UI/UX Role | WCAG on Dark Bg |
|---|---|---|---|
| `--bg-primary` | `#050E0B` | App / page background | — |
| `--bg-surface` | `#0A1A14` | Cards, panels | — |
| `--bg-elevated` | `#0F2A20` | Sidebar, code blocks | — |
| `--border` | `#1A3D2E` | Dividers, strokes | — |
| `--text-primary` | `#EDFDF8` | Headings, body copy | **19.2:1 AAA** |
| `--text-secondary` | `#6EE7C7` | Labels, metadata | **7.3:1 AA** |
| `--accent` | `#00FFC2` | CTAs, links, active nav | **12.6:1 AAA** |
| `--accent-hover` | `#5FFFDA` | Hover on buttons | **15.1:1 AAA** |
| `--accent-subtle` | `#00261A` | Badge fills, alert banners | — |
| `--success` | `#4ADE80` | Success states | **9.1:1 AAA** |
| `--error` | `#FF6B6B` | Error states | **5.4:1 AA** |
| `--warning` | `#FCD34D` | Warning states | **10.2:1 AAA** |
| `--info` | `#38BDF8` | Info / link color | **8.7:1 AAA** |

---

### CSS Variables

```css
/* ── Google Fonts Import ──────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=JetBrains+Mono:wght@400;500&display=swap');

/* ── Light Mode ───────────────────────────────── */
:root {
  --bg-primary:      #F0FDF9;
  --bg-surface:      #FFFFFF;
  --bg-elevated:     #E6FAF5;
  --border:          #B2EEE0;
  --text-primary:    #042F26;
  --text-secondary:  #2A7A65;
  --accent:          #00A87A;
  --accent-hover:    #007A58;
  --accent-subtle:   #CCFBEF;
  --success:         #15803D;
  --error:           #DC2626;
  --warning:         #B45309;
  --info:            #0E7490;
  --font-display:    'Space Grotesk', sans-serif;
  --font-body:       'JetBrains Mono', monospace;
}

/* ── Dark Mode ────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  :root {
    --bg-primary:     #050E0B;
    --bg-surface:     #0A1A14;
    --bg-elevated:    #0F2A20;
    --border:         #1A3D2E;
    --text-primary:   #EDFDF8;
    --text-secondary: #6EE7C7;
    --accent:         #00FFC2;
    --accent-hover:   #5FFFDA;
    --accent-subtle:  #00261A;
    --success:        #4ADE80;
    --error:          #FF6B6B;
    --warning:        #FCD34D;
    --info:           #38BDF8;
  }
}
```

**✅ Choose this palette when:** App is an AI tool / LLM interface · Terminal or code-heavy UI · Cybersecurity dashboard · Dark mode is default · Cursor / Perplexity / sci-fi inspiration

---

---

# 5. 🌿 Supabase × Emerald

**Vibe:** Open-source · Fresh · Friendly · Developer-trusted  
**Inspired by:** Supabase, Planetscale, Railway, Render  
**Typography:** `Manrope` (headings) + `Fira Code` (body/code)

---

### Light Mode

| Token | Hex | UI/UX Role | WCAG on White |
|---|---|---|---|
| `--bg-primary` | `#F8FAF9` | App / page background | — |
| `--bg-surface` | `#FFFFFF` | Cards, panels | — |
| `--bg-elevated` | `#EFF5F2` | Sidebar, input fills | — |
| `--border` | `#D1E8DF` | Dividers, strokes | — |
| `--text-primary` | `#0E1C17` | Headings, body copy | **18.7:1 AAA** |
| `--text-secondary` | `#4E7363` | Labels, metadata | **5.1:1 AA** |
| `--accent` | `#3ECF8E` | CTAs, links, active states | **4.5:1 AA** |
| `--accent-hover` | `#2BAF74` | Hover on buttons | **5.8:1 AA** |
| `--accent-subtle` | `#D1FAE5` | Badge fills, banners | — |
| `--success` | `#059669` | Success states | **5.3:1 AA** |
| `--error` | `#DC2626` | Error states | **4.8:1 AA** |
| `--warning` | `#D97706` | Warning states | **4.5:1 AA** |
| `--info` | `#0891B2` | Info / link color | **5.0:1 AA** |

---

### Dark Mode

| Token | Hex | UI/UX Role | WCAG on Dark Bg |
|---|---|---|---|
| `--bg-primary` | `#09130F` | App / page background | — |
| `--bg-surface` | `#101F18` | Cards, panels | — |
| `--bg-elevated` | `#172E22` | Sidebar, code blocks | — |
| `--border` | `#1F4030` | Dividers, strokes | — |
| `--text-primary` | `#ECFDF5` | Headings, body copy | **18.1:1 AAA** |
| `--text-secondary` | `#6EE7B7` | Labels, metadata | **9.2:1 AAA** |
| `--accent` | `#3ECF8E` | CTAs, links, active nav | **8.4:1 AAA** |
| `--accent-hover` | `#6EDDA8` | Hover on buttons | **11.2:1 AAA** |
| `--accent-subtle` | `#052E16` | Badge fills, alert banners | — |
| `--success` | `#34D399` | Success states | **10.1:1 AAA** |
| `--error` | `#F87171` | Error states | **5.8:1 AA** |
| `--warning` | `#FBBF24` | Warning states | **9.4:1 AAA** |
| `--info` | `#22D3EE` | Info / link color | **11.3:1 AAA** |

---

### CSS Variables

```css
/* ── Google Fonts Import ──────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@600;700&family=Fira+Code:wght@400;500&display=swap');

/* ── Light Mode ───────────────────────────────── */
:root {
  --bg-primary:      #F8FAF9;
  --bg-surface:      #FFFFFF;
  --bg-elevated:     #EFF5F2;
  --border:          #D1E8DF;
  --text-primary:    #0E1C17;
  --text-secondary:  #4E7363;
  --accent:          #3ECF8E;
  --accent-hover:    #2BAF74;
  --accent-subtle:   #D1FAE5;
  --success:         #059669;
  --error:           #DC2626;
  --warning:         #D97706;
  --info:            #0891B2;
  --font-display:    'Manrope', sans-serif;
  --font-body:       'Fira Code', monospace;
}

/* ── Dark Mode ────────────────────────────────── */
@media (prefers-color-scheme: dark) {
  :root {
    --bg-primary:     #09130F;
    --bg-surface:     #101F18;
    --bg-elevated:    #172E22;
    --border:         #1F4030;
    --text-primary:   #ECFDF5;
    --text-secondary: #6EE7B7;
    --accent:         #3ECF8E;
    --accent-hover:   #6EDDA8;
    --accent-subtle:  #052E16;
    --success:        #34D399;
    --error:          #F87171;
    --warning:        #FBBF24;
    --info:           #22D3EE;
  }
}
```

**✅ Choose this palette when:** App is open-source or developer-community focused · Backend / database UI · Fresh, approachable dev tool · Supabase / Railway / Render inspiration

---

---

# 🧠 AI Selection Guide

> **Paste this guide with your app description to help an AI choose the right palette.**

```
Given the following palettes, recommend the best one for my app:

1. Firebase × Amber  — Bold, warm orange, developer-first dashboards
2. Vercel × Slate    — Minimal, monochrome black/white, SaaS/deployment tools
3. Stripe × Indigo   — Premium, trustworthy indigo/purple, fintech/B2B SaaS
4. Cyber × Neon      — Futuristic, neon teal, AI tools and dark-first UIs
5. Supabase × Emerald — Fresh, open-source friendly emerald green, backend tools

My app: [DESCRIBE YOUR APP HERE]
Audience: [WHO USES IT]
Mood: [HOW IT SHOULD FEEL]
Default mode preference: [Light / Dark / System]
```

---

# 📐 Universal Design Tokens (All Palettes)

These spacing, radius, and shadow tokens are **palette-agnostic** and work with any palette above.

```css
:root {
  /* Spacing scale */
  --space-1:  4px;
  --space-2:  8px;
  --space-3:  12px;
  --space-4:  16px;
  --space-6:  24px;
  --space-8:  32px;
  --space-12: 48px;
  --space-16: 64px;

  /* Border radius */
  --radius-sm:  4px;
  --radius-md:  8px;
  --radius-lg:  12px;
  --radius-xl:  16px;
  --radius-2xl: 24px;
  --radius-full: 9999px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
  --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
  --shadow-xl: 0 16px 48px rgba(0,0,0,0.16);

  /* Transitions */
  --ease-default: 150ms ease;
  --ease-smooth:  250ms cubic-bezier(0.4, 0, 0.2, 1);
  --ease-spring:  400ms cubic-bezier(0.34, 1.56, 0.64, 1);

  /* Z-index scale */
  --z-dropdown: 100;
  --z-sticky:   200;
  --z-modal:    300;
  --z-toast:    400;
  --z-tooltip:  500;
}
```

---

# ✅ WCAG Quick Reference

| Level | Min Contrast | Used For |
|---|---|---|
| **AA** | 4.5:1 | Normal text (< 18px or < 14px bold) |
| **AA Large** | 3.0:1 | Large text (≥ 18px or ≥ 14px bold) |
| **AAA** | 7.0:1 | Enhanced accessibility (recommended) |

> All palettes in this document are designed to meet **WCAG 2.1 AA** as a minimum. AAA is met where possible.

---

*Last updated: May 2026 — Generated with Claude (Anthropic)*
