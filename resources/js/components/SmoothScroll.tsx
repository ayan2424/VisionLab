import React, { useEffect } from 'react';
import Lenis from 'lenis';
import { gsap, ScrollTrigger } from '@/lib/gsap';

interface SmoothScrollProps {
    children: React.ReactNode;
    enabled?: boolean;
}

export function SmoothScroll({ children, enabled = true }: SmoothScrollProps) {
    useEffect(() => {
        if (!enabled || typeof window === 'undefined') return;

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reducedMotion) return;

        const lenis = new Lenis({
            duration: 1.15,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            touchMultiplier: 1.5,
        });

        lenis.on('scroll', ScrollTrigger.update);

        const tick = (time: number) => {
            lenis.raf(time * 1000);
        };

        gsap.ticker.add(tick);
        gsap.ticker.lagSmoothing(0);

        return () => {
            gsap.ticker.remove(tick);
            lenis.destroy();
        };
    }, [enabled]);

    return <>{children}</>;
}
