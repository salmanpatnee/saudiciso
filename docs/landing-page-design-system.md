# Landing Page Design System

Source of truth: `public/css/landing.css` (loaded by `resources/views/welcome.blade.php`).

This file documents the design tokens and component patterns used on the public landing page so they can be reused consistently across the rest of the app. The stylesheet was built in two layers: a dark "base" theme defined in `:root`, then a **light executive theme** override block near the bottom of the file that repaints most surfaces. The tokens below reflect what actually renders today — the light theme for content, with the navbar and footer intentionally kept dark.

## 1. Color

### Brand palette

| Token | Value | Usage |
|---|---|---|
| `--gold` | `#caa41b` | Deep gold — accent bar gradients, badge borders |
| `--gold-bright` | `#f0cf3a` | Primary gold — buttons, eyebrows, icon accents, focus rings |
| Gold text gradient | `linear-gradient(135deg, var(--gold), #8a6d10)` | `.text-accent` — gradient-clipped gold text for emphasis words |
| `--bg` | `#00053c` | Deep navy — nav bar, footer, dark section backgrounds |
| `--bg-soft` | `#080b4a` | Secondary navy — `.section--deep` background |
| `--card` / `--card-strong` | `#0b0f4f` / `#111665` | Dark glass-card backgrounds (CTA card, modal) |
| Heading navy | `#050739` | All headings on light surfaces |
| Body copy | `#4e5870` | Paragraph text on light surfaces |
| Secondary body | `#283252` | Proof/meta text on light surfaces |
| `--white` | `#ffffff` | Text on dark surfaces (nav, footer, dark cards) |
| `--muted` | `#aab4c5` | Muted text on dark surfaces |
| `--muted-strong` | `#d7deea` | Slightly brighter muted text on dark surfaces |
| `--border` | `rgba(255,255,255,.08)` | Hairline borders on dark surfaces |
| `--border-strong` | `rgba(255,255,255,.14)` | Emphasized borders on dark surfaces |
| Light border | `rgba(5,7,57,.09–.13)` | Hairline borders on light surfaces (navy at low opacity) |

**Page background** (`.landing-page`): layered radial gradients over a base color —
```css
radial-gradient(900px 520px at 82% -10%, rgba(240,207,58,.18), transparent 58%),
radial-gradient(760px 420px at 4% 6%, rgba(5,7,57,.08), transparent 56%),
linear-gradient(180deg, #f8f9ff 0%, #eef1fb 48%, #f8f9ff 100%);
```
This "gold glow, top-right / navy glow, bottom-left, on an off-white base" formula repeats (with different anchor points) on hero, cards, and CTA sections — treat it as the signature background texture.

**Surface rule of thumb:**
- **Light surfaces** (default page background, service cards, membership panel, modal): white/off-white background, navy headings, `#4e5870` body text, gold accents.
- **Dark surfaces** (nav, footer, `.section--deep`, CTA card, dark glass cards): navy background, white headings, `--muted` body text, gold accents.
- Gold is the *only* accent color used across both surfaces — never introduce a second accent hue.

## 2. Typography

- **Font family:** `"Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif` — loaded via Google Fonts (`400;500;600;700;800;900`).
- **Base body:** `18px` / `line-height: 1.65` (`16px` under 680px).
- **Headings** (`h1`, `h2`, `h3`): `letter-spacing: 0`, `line-height: 1.04`, weight `800`–`900`.

| Element | Size | Weight |
|---|---|---|
| Hero `h1` | `clamp(3.05rem, 6vw, 4rem)` | 900 |
| Hero tagline (`.hero__tagline`) | `clamp(1.3rem, 2.6vw, 1.6rem)` | 800 |
| Hero lead paragraph | `clamp(1.05rem, 2vw, 1.18rem)`, line-height 1.8 | 400 |
| Section heading (`.section-head h2`, etc.) | `clamp(2.2rem, 4vw, 2.55rem)` | 800 |
| Card heading (`.service-card h3`) | `1.22rem` | 850 |
| Eyebrow label | `.82rem`, uppercase, `letter-spacing: .08em` | 800 |
| Body copy | `1rem`, line-height 1.75 | 400 |
| Small/meta text | `.82rem`–`.9rem` | 700–800 |

**Eyebrow pattern** — a small uppercase label used above every section/card heading:
```html
<p class="eyebrow"><span class="eyebrow__dot"></span> Label text</p>
```
Gold-bright color, bold, with a glowing dot (`box-shadow: 0 0 0 7px rgba(240,207,58,.14)`).

## 3. Spacing & Layout

| Token | Value |
|---|---|
| `--maxw` | `1200px` (container max width) |
| `.container` width | `min(100% - 40px, var(--maxw))` (28px gutter under 680px) |
| `.section` padding | `112px 0` (`84px` ≤900px, `unset` for compact variants) |
| `.section--compact` padding | `54px 0` (`42px` ≤900px) |
| `--nav-height` | `82px` |

Grid gaps typically range **18px** (dense card grids) → **32–84px** (two-column hero/feature layouts).

## 4. Radius Scale

| Token | Value | Used for |
|---|---|---|
| `--radius-sm` | `10px` | small chips |
| `--radius` | `18px` | floating cards |
| `--radius-lg` | `28px` | modal, membership card, CTA card, why-panel |
| `--radius-xl` | `36px` | hero image card, Saudization banner (large hero-level surfaces) |
| Pill | `999px` | all buttons, badges, nav links, form inputs (newsletter) |

## 5. Shadows & Elevation

| Token | Value | Usage |
|---|---|---|
| `--shadow` | `0 24px 70px rgba(0,0,0,.34)` | dark glass cards |
| `--shadow-soft` | `0 18px 45px rgba(4,10,20,.24)` | floating cards, dark service cards |
| Light card shadow | `0 18px 48px rgba(5,7,57,.08)` | service/stat/testimonial cards on light bg |
| Prominent banner shadow | `0 30px 80px rgba(5,7,57,.16)` | Saudization card, hero image |
| Gold edge ring | `0 0 0 1px rgba(240,207,58,.18–.3)` combined with a large soft shadow | "frame" effect on the hero image (see §6) |

## 6. Core Components

### Buttons (`.btn`)
- Base: pill radius, `min-height: 46px` (`56px` for `.btn--large`), bold, `transform: translateY(-2px)` on hover.
- **Primary** (`.btn--primary`): gold gradient (`linear-gradient(135deg, var(--gold-bright), #fff1a1)`), dark text (`#04130e`), gold glow shadow. Use for the main conversion action ("Become a Member").
- **Secondary/Ghost** (`.btn--secondary`, `.btn--ghost`): near-transparent navy fill on light surfaces / white fill on dark surfaces (nav), bordered. Use for secondary navigation actions ("Access Platform").

### Cards
- **Feature/service card**: white gradient bg, hairline border, `22px` radius, lifts on hover (`translateY(-7px)`) with a gold border glow.
- **Icon box** (`.icon-box`): `52×52px`, `16px` radius, gold-tinted background + gold-bright icon stroke. Scale up (e.g. `76×76px`, `22px` radius) for hero-level banners via a modifier class combined with `.icon-box` (see `.icon-box.saudization-card__icon`) rather than overriding the base.
- **Prominent banner card** (e.g. Saudization "Good News" card): large padding (~`50px 54px`), `--radius-xl`, thick gold border (`rgba(202,164,27,.4)`), dual radial-gradient background, a `6px` gold gradient top accent bar (`::before`), and an optional pill badge (`.saudization-card__badge`) justified to the end of the grid.
- **Framed image card** (hero image): white background, `--radius-xl`, combined border + inset gold ring shadow for a "picture frame" look, plus a dark-to-transparent gradient overlay (`::after`) for text legibility if content is ever placed on top, and a subtle inner highlight (`::before`, `inset 0 0 0 1px rgba(255,255,255,.5)`). Lifts on hover.

### Badges / Pills
Small uppercase label in a pill: `rgba(240,207,58,.16)` background, `rgba(202,164,27,.4)` border, gold-brown text (`#7a5f10`), `.8rem` bold, `.04em` letter-spacing.

### Text accent (`.text-accent`)
Reusable gradient-clipped gold text for emphasizing a single word inside a heading:
```css
.text-accent {
  background: linear-gradient(135deg, var(--gold), #8a6d10);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
```
Usage: `<h1>What is Your Biggest <span class="text-accent">Problem</span> Today?</h1>`

### Navigation
Fixed, dark navy (`#00053c`) regardless of section theme, blurred/shadowed once scrolled (`.is-scrolled`). Links are pill-shaped on hover. Collapses to an off-canvas panel under 1100px.

### Footer
Dark navy, matches nav. Two variants:
- Full (`.footer-grid`): brand + 3 link columns + newsletter form.
- **Minimal** (`.site-footer--minimal` + `.footer-grid--minimal`): brand block + tagline only, centered, no link columns/newsletter/social — used when a simpler footer is desired. Copyright bar (`.footer-bottom`) is centered.

### Modal
Centered dialog, blurred backdrop (`rgba(2,8,18,.72)`, `blur(12px)`), dark card background with a gold radial highlight, `28px` radius.

### Reveal-on-scroll
Any element with class `.reveal` starts hidden/offset (`opacity:0; translateY(24px)`) and animates in via an `IntersectionObserver` toggling `.is-visible` (see inline script in `welcome.blade.php`). Respects `prefers-reduced-motion`.

## 7. Motion

- Standard transition: `transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease, color .2s ease` (buttons/links).
- Card hover lift: `transform .22s–.35s ease` + shadow transition, `translateY(-4px to -7px)`.
- Reveal-in: `opacity .65s ease, transform .65s ease`.
- Floating card idle animation: `floatCard 6s ease-in-out infinite` (±12px vertical drift), staggered with `animation-delay`.
- All animation is disabled under `prefers-reduced-motion: reduce`.

## 8. Responsive Breakpoints

| Breakpoint | Purpose |
|---|---|
| `1100px` | Nav collapses to hamburger/off-canvas; two-column grids (hero, why, membership) stack to one column |
| `900px` | Section padding tightens; 3-col card grids drop to 2 columns |
| `680px` | Mobile: base font drops to 16px; hero image is **hidden** (`.hero__image-card { display: none; }`); card grids go to 1 column; forms stack |
| `430px` | Final small-phone tweaks (floating cards become static, security grid tightens) |

## 9. Applying This Elsewhere in the App

- Reuse the CSS custom properties (`--gold`, `--gold-bright`, `--radius-xl`, `--shadow`, etc.) rather than hardcoding hex values in new views.
- Keep the **light-surface / dark-surface** split: don't mix navy-on-navy or introduce a second accent color.
- For any new "call to attention" banner or card, follow the Saudization card pattern: thick gold border + top accent bar + large radius + optional badge, rather than inventing a new visual language.
- For emphasis words inside headings, use `.text-accent` instead of a new gold text style.
- Buttons should always be one of `.btn--primary` (gold, one per view max) or `.btn--secondary`/`.btn--ghost` (everything else).
