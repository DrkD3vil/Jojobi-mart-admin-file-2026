<style>
    /* ============================================
       SHADCN UI THEME VARIABLES — Dockline palette
       Kept in sync with resources/views/components/style.blade.php
       so the login/2FA flow matches the rest of the admin.
    ============================================ */

    :root {
        /* Base radius for components */
        --radius: 0.375rem;

        /* Light mode colors */
        --background: #F4F0E6;
        --foreground: #12181C;
        --card: #FFFFFF;
        --card-foreground: #12181C;
        --popover: #FFFFFF;
        --popover-foreground: #12181C;
        --primary: #B96E10;
        --primary-foreground: #12181C;
        --secondary: #EAE3D2;
        --secondary-foreground: #12181C;
        --muted: #EAE3D2;
        --muted-foreground: #5A6570;
        --accent: #EAE3D2;
        --accent-foreground: #12181C;
        --destructive: #C22E2E;
        --border: rgba(18, 24, 28, .14);
        --input: rgba(18, 24, 28, .14);
        --ring: #B96E10;

        /* Chart colors */
        --chart-1: #B96E10;
        --chart-2: #177264;
        --chart-3: #3B6FC4;
        --chart-4: #C22E2E;
        --chart-5: #8452A6;

        /* Sidebar colors */
        --sidebar: #FFFFFF;
        --sidebar-foreground: #12181C;
        --sidebar-primary: #B96E10;
        --sidebar-primary-foreground: #12181C;
        --sidebar-accent: #EAE3D2;
        --sidebar-accent-foreground: #12181C;
        --sidebar-border: rgba(18, 24, 28, .14);
        --sidebar-ring: #B96E10;

        /* Extended semantic colors */
        --success: #177264;
        --success-foreground: #FFFFFF;
        --warning: #B96E10;
        --warning-foreground: #12181C;
        --info: #3B6FC4;
        --info-foreground: #FFFFFF;

        /* Card shadows */
        --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
        --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);
    }

    /* Dark mode colors */
    .dark {
        --background: #0A1420;
        --foreground: #ECE6D8;
        --card: #101B27;
        --card-foreground: #ECE6D8;
        --popover: #101B27;
        --popover-foreground: #ECE6D8;
        --primary: #FFB020;
        --primary-foreground: #0A1420;
        --secondary: #0E1926;
        --secondary-foreground: #ECE6D8;
        --muted: #0E1926;
        --muted-foreground: #93A4B0;
        --accent: #0E1926;
        --accent-foreground: #ECE6D8;
        --destructive: #FF5D5D;
        --border: rgba(236, 230, 216, .14);
        --input: rgba(236, 230, 216, .14);
        --ring: #FFB020;

        /* Chart colors (dark mode) */
        --chart-1: #FFB020;
        --chart-2: #2FD9C0;
        --chart-3: #6EA8FE;
        --chart-4: #FF5D5D;
        --chart-5: #C792EA;

        /* Sidebar colors (dark mode) */
        --sidebar: #101B27;
        --sidebar-foreground: #ECE6D8;
        --sidebar-primary: #FFB020;
        --sidebar-primary-foreground: #0A1420;
        --sidebar-accent: #0E1926;
        --sidebar-accent-foreground: #ECE6D8;
        --sidebar-border: rgba(236, 230, 216, .14);
        --sidebar-ring: #FFB020;

        /* Extended semantic colors (dark mode) */
        --success: #2FD9C0;
        --success-foreground: #06251F;
        --warning: #FFB020;
        --warning-foreground: #0A1420;
        --info: #6EA8FE;
        --info-foreground: #0A1420;

        /* Card shadows (dark mode) */
        --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.35);
        --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.45), 0 3px 6px -2px rgb(0 0 0 / 0.3);
    }
</style>
