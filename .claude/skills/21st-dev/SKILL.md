---
name: 21st-dev
description: Find, preview, and install production-ready React/Tailwind/shadcn UI components and design inspiration from the 21st.dev registry. Use when the user wants to add or redesign UI (dashboards, landing pages, forms, cards, tables, pricing sections, etc.) and would benefit from a real, styled component rather than one built from scratch, or when they mention 21st.dev, "21st", or "Magic MCP".
---

# 21st.dev component registry

21st.dev is a community-built catalog of 12,000+ React components, blocks, and shadcn-style themes contributed by design engineers. Components are copied into the target repo as real source (not installed as an npm dependency), so they can be freely edited and re-themed. Everything follows shadcn/ui conventions: Tailwind CSS utility classes, Radix primitives where relevant, and CSS variables for theme tokens (`--background`, `--foreground`, `--primary`, `--muted`, etc.).

## When to reach for it

- The user asks to add a new UI section (hero, pricing table, stat cards, data table, sidebar, auth form, dashboard widgets) and wants something polished rather than a bare-bones first pass.
- The user asks to "redesign" or "modernize" an existing page.
- The user explicitly mentions 21st.dev, 21st, or the Magic MCP.

Note: 21st.dev's own components target **React + Tailwind + shadcn/ui**. If the current project is not React (e.g. it's a server-rendered template language like Blade, ERB, or Twig), don't paste JSX in directly — port the *markup structure, Tailwind classes, and visual design* into the project's native templating syntax, and skip/replace any React-only behavior (hooks, `useState`, Radix component imports) with the project's existing interactivity approach (vanilla JS, Alpine, Livewire, etc.).

## Two ways to use it

### 1. Live MCP tools (preferred when the `21st` MCP server is connected)

If the `21st` MCP server is configured and authenticated (see `.mcp.json` in this repo — requires `TWENTY_FIRST_API_KEY`), these tools are available:

- `generate` — describe a UI need in natural language; returns a generated component with source code and live preview variants.
- `get_inspiration` — search existing components/blocks by keyword or category (e.g. "pricing table", "admin sidebar", "stat cards") to see multiple real-world implementations before picking a direction.
- `search_logo` — find brand/company logos as SVG components.

Workflow: call `get_inspiration` first to survey a few real implementations of the pattern being built, pick the one closest to the desired look, then adapt its structure/classes into the target file. Use `generate` when nothing close enough exists and a fresh component should be scaffolded from a description.

If a tool call fails with an auth error, the API key is missing or invalid — tell the user to generate one at 21st.dev/mcp and set it as the `TWENTY_FIRST_API_KEY` environment variable, then fall back to option 2.

### 2. Manual browse + adapt (no MCP / no API key needed)

1. Browse https://21st.dev (categories, search) to find a component or block matching the need.
2. Each component page offers either:
   - A **shadcn CLI install command**, e.g. `npx shadcn@latest add "https://21st.dev/r/<author>/<component>"` — pulls the raw source into the local `components/ui` (or equivalent) folder for a React+shadcn project.
   - A **copy-paste AI prompt** designed to be pasted directly into an AI coding assistant, which describes the component well enough to reproduce it in the current codebase's stack.
3. Adapt naming, data bindings, and framework specifics (props → Blade/template variables, `onClick` → native event/template directive, etc.) to match the surrounding codebase's conventions rather than dropping in raw React unmodified.

## Design conventions to carry over regardless of stack

- Consistent spacing scale (Tailwind's default `4px` step) and rounded corners (`rounded-lg`/`rounded-xl` on cards).
- Muted, low-saturation backgrounds with a single accent color for primary actions/highlights.
- Icon + label pairing for nav and stat items (21st.dev components commonly use `lucide-react`; this repo already uses `data-lucide` icons via Lucide's vanilla build, which is the direct equivalent).
- Cards for grouped content with subtle borders (`border`, `border-border` / a low-opacity border color) instead of heavy shadows.
- Dark-mode support via CSS variables rather than hardcoded colors, matching the project's existing `--muted-foreground`-style token usage.
