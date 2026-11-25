import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
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
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'react-vendor': ['react', 'react-dom', '@inertiajs/react'],
                    'motion-vendor': ['motion', 'gsap', '@gsap/react', 'lenis'],
                    'three-vendor': ['three', '@react-three/fiber', '@react-three/drei'],
                    'charts-vendor': ['recharts'],
                },
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.tsx',
            ],
            refresh: true,
        }),
        react(),
        {
            name: 'visioncode-cleanup',
            buildStart() {
                const hotFile = path.resolve(__dirname, 'public/hot');
                if (fs.existsSync(hotFile)) {
                    fs.unlinkSync(hotFile);
                    console.log('Removed stale public/hot file');
                }
            },
        },
    ],
});
