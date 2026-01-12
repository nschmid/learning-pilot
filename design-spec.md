# LearningPilot Design Specification

## Brand Colors

### Primary: Teal
The primary brand color used for interactive elements, links, and emphasis.

| Token | Hex | Usage |
|-------|-----|-------|
| `teal-50` | `#f0fdfa` | Backgrounds, hover states |
| `teal-100` | `#ccfbf1` | Light backgrounds |
| `teal-200` | `#99f6e4` | Borders, dividers |
| `teal-400` | `#2dd4bf` | Icons (on dark backgrounds) |
| `teal-500` | `#14b8a6` | Focus rings |
| `teal-600` | `#0d9488` | Primary buttons, links, logo |
| `teal-700` | `#0f766e` | Hover states on primary |
| `teal-800` | `#115e59` | Active states |
| `teal-900` | `#134e4a` | Dark text |

### Accent: Amber
Secondary accent color for highlights and warnings.

| Token | Hex | Usage |
|-------|-----|-------|
| `amber-50` | `#fffbeb` | Warning backgrounds |
| `amber-500` | `#f59e0b` | Warning icons |
| `amber-600` | `#d97706` | Warning text |

### Semantic Colors

| Purpose | Background | Text | Ring |
|---------|------------|------|------|
| Success | `green-50` | `green-600` | `green-600/10` |
| Warning | `amber-50` | `amber-600` | `amber-600/10` |
| Danger | `rose-50` | `rose-600` | `rose-600/10` |
| Info | `sky-50` | `sky-600` | `sky-600/10` |
| Accent | `purple-50` | `purple-600` | `purple-600/10` |

---

## Typography

### Font Stack
```css
font-family: Inter, system-ui, sans-serif;
```

### Scale
| Element | Class | Size |
|---------|-------|------|
| Page Title | `text-2xl font-bold text-gray-900` | 24px |
| Section Title | `text-lg font-semibold text-gray-900` | 18px |
| Card Title | `font-semibold text-gray-900` | 16px |
| Body | `text-sm text-gray-600` | 14px |
| Caption | `text-xs text-gray-500` | 12px |
| Label | `text-sm font-medium text-gray-700` | 14px |

---

## Cards

### Base Card
```html
<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
  <!-- content -->
</div>
```

### Interactive Card (with hover)
```html
<a href="..." class="group rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 hover:shadow-md hover:ring-gray-900/10 transition-all duration-200">
  <!-- content -->
</a>
```

### Card with Header
```html
<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
  <div class="border-b border-gray-100 px-6 py-4 bg-gray-50 rounded-t-xl">
    <!-- header -->
  </div>
  <div class="p-6">
    <!-- content -->
  </div>
</div>
```

### Stat Card
```html
<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
  <p class="text-sm font-medium text-gray-500">Label</p>
  <p class="mt-2 text-3xl font-bold text-gray-900">Value</p>
</div>
```

---

## Icons & Icon Containers

### Icon Container (colored background)
```html
<div class="flex size-12 items-center justify-center rounded-xl bg-teal-50 ring-1 ring-teal-600/10">
  <svg class="size-6 text-teal-600">...</svg>
</div>
```

### Icon Container Variants
| Variant | Background | Ring | Icon Color |
|---------|------------|------|------------|
| Primary | `bg-teal-50` | `ring-teal-600/10` | `text-teal-600` |
| Success | `bg-green-50` | `ring-green-600/10` | `text-green-600` |
| Warning | `bg-amber-50` | `ring-amber-600/10` | `text-amber-600` |
| Danger | `bg-rose-50` | `ring-rose-600/10` | `text-rose-600` |
| Info | `bg-sky-50` | `ring-sky-600/10` | `text-sky-600` |
| Accent | `bg-purple-50` | `ring-purple-600/10` | `text-purple-600` |

### Interactive Icon Container (hover effect)
```html
<div class="flex size-12 items-center justify-center rounded-xl bg-gray-50 ring-1 ring-gray-900/5 group-hover:bg-teal-50 group-hover:ring-teal-600/10 transition">
  <svg class="size-6 text-gray-600 group-hover:text-teal-600 transition">...</svg>
</div>
```

---

## Buttons

### Primary Button
```html
<button class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition">
  Button Text
</button>
```

### Secondary Button
```html
<button class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition">
  Button Text
</button>
```

### Ghost Button
```html
<button class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition">
  Button Text
</button>
```

### Danger Button
```html
<button class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition">
  Delete
</button>
```

---

## Form Elements

### Input Field
```html
<input type="text" class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-500 sm:text-sm">
```

### Select
```html
<select class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500 sm:text-sm">
  <option>Option</option>
</select>
```

### Label
```html
<label class="block text-sm font-medium text-gray-700 mb-1.5">
  Field Label
</label>
```

### Help Text
```html
<p class="mt-1.5 text-sm text-gray-500">
  Help text goes here.
</p>
```

---

## Navigation

### Header (Public/Authenticated)
```html
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      <!-- Logo, Nav, Actions -->
    </div>
  </div>
</header>
```

### Navigation Link
```html
<a href="..." class="text-sm font-medium text-gray-700 hover:text-teal-600 transition">
  Link Text
</a>
```

### Active Navigation Link
```html
<a href="..." class="text-sm font-medium text-teal-600">
  Active Link
</a>
```

### Breadcrumbs
```html
<nav class="flex items-center gap-2 text-sm">
  <a href="..." class="text-gray-500 hover:text-teal-600 transition">Parent</a>
  <svg class="size-4 text-gray-400"><!-- chevron --></svg>
  <span class="font-medium text-gray-900">Current</span>
</nav>
```

---

## Sidebar (Admin)

### Dark Sidebar
```html
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900">
  <!-- content -->
</aside>
```

### Sidebar Link (inactive)
```html
<a href="..." class="text-gray-300 hover:bg-gray-800 hover:text-white group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition">
  <svg class="mr-3 size-5 text-gray-400 group-hover:text-teal-400">...</svg>
  Link Text
</a>
```

### Sidebar Link (active)
```html
<a href="..." class="bg-gray-800 text-white group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium">
  <svg class="mr-3 size-5 text-teal-400">...</svg>
  Active Link
</a>
```

---

## Badges & Status

### Badge
```html
<span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700 ring-1 ring-inset ring-teal-600/20">
  Badge
</span>
```

### Badge Variants
| Variant | Classes |
|---------|---------|
| Primary | `bg-teal-50 text-teal-700 ring-teal-600/20` |
| Success | `bg-green-50 text-green-700 ring-green-600/20` |
| Warning | `bg-amber-50 text-amber-700 ring-amber-600/20` |
| Danger | `bg-rose-50 text-rose-700 ring-rose-600/20` |
| Gray | `bg-gray-50 text-gray-700 ring-gray-600/20` |

### Status Dot
```html
<span class="flex size-2 rounded-full bg-green-500"></span>
```

---

## Tables

### Table Container
```html
<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
  <table class="min-w-full divide-y divide-gray-200">
    <!-- content -->
  </table>
</div>
```

### Table Header
```html
<thead class="bg-gray-50">
  <tr>
    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
      Header
    </th>
  </tr>
</thead>
```

### Table Row
```html
<tr class="hover:bg-gray-50 transition">
  <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
    Cell content
  </td>
</tr>
```

---

## Spacing

### Standard Spacing Scale
| Token | Value | Usage |
|-------|-------|-------|
| `gap-2` | 8px | Tight spacing (icons, badges) |
| `gap-4` | 16px | Default spacing (card content) |
| `gap-6` | 24px | Section spacing |
| `gap-8` | 32px | Page section spacing |

### Page Layout
```html
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
  <!-- Page content -->
</div>
```

### Card Grid
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Cards -->
</div>
```

---

## Transitions

### Standard Transition
```css
transition /* duration-150 ease-out */
```

### All Properties Transition
```css
transition-all duration-200
```

### Color Transition Only
```css
transition-colors duration-150
```

---

## Shadows

| Class | Usage |
|-------|-------|
| `shadow-sm` | Cards, dropdowns |
| `shadow-md` | Hover states, modals |
| `shadow-lg` | Popovers, elevated elements |

---

## Focus States

### Focus Ring (inputs)
```css
focus:ring-2 focus:ring-inset focus:ring-teal-500
```

### Focus Ring (buttons)
```css
focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2
```

---

## Logo

### Primary Logo Color
- Hex: `#0D9488` (teal-600)
- Used in: Circle background, path elements, "Pilot" text

### Logo Sizes
| Context | Size |
|---------|------|
| Header | `h-8` (32px) |
| Footer | `h-10` (40px) |
| Favicon | 48x48px |

---

## Responsive Breakpoints

| Breakpoint | Min Width | Usage |
|------------|-----------|-------|
| `sm` | 640px | Mobile landscape |
| `md` | 768px | Tablets |
| `lg` | 1024px | Desktop |
| `xl` | 1280px | Large desktop |
| `2xl` | 1536px | Extra large |

---

## Z-Index Scale

| Layer | Z-Index | Usage |
|-------|---------|-------|
| Base | `z-0` | Default content |
| Dropdown | `z-10` | Dropdowns, tooltips |
| Sticky | `z-20` | Sticky elements |
| Fixed | `z-30` | Fixed elements |
| Modal Backdrop | `z-40` | Modal backgrounds |
| Modal | `z-50` | Modals, sidebars |
| Toast | `z-[60]` | Notifications |
