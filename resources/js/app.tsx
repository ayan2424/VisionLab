import '../css/app.css';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { ThemeProvider } from '@/components/ThemeProvider';

const pages = import.meta.glob('./Pages/**/*.tsx');

createInertiaApp({
    title: (title) => (title ? `${title} — VisionLab` : 'VisionLab — Sovereign University Coding Labs'),
    resolve: async (name) => {
        const importer = pages[`./Pages/${name}.tsx`];
        if (!importer) {
            throw new Error(`Inertia page not found: ./Pages/${name}.tsx`);
        }
        return (await importer()) as { default: React.ComponentType<Record<string, unknown>> };
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <ThemeProvider>
                <App {...props} />
            </ThemeProvider>
        );
    },
    progress: {
        color: '#38BDF8',
        includeCSS: true,
        showSpinner: false,
    },
});
