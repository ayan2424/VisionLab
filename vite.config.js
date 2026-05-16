import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        {
            // Auto-delete stale "hot" file when doing `npm run build`
            // This is the #1 cause of "CSS missing" when moving between machines
            name: 'VisionLab-cleanup',
            buildStart() {
                const hotFile = path.resolve(__dirname, 'public/hot');
                if (fs.existsSync(hotFile)) {
                    fs.unlinkSync(hotFile);
                    console.log('🧹 Removed stale public/hot file');
                }
            },
        },
    ],
});
