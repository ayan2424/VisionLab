import React, { createContext, useCallback, useContext, useEffect, useState } from 'react';

export type ThemeMode = 'dark' | 'light';

interface ThemeContextValue {
    theme: ThemeMode;
    isDark: boolean;
    toggleTheme: () => void;
    setTheme: (next: ThemeMode) => void;
}

const ThemeContext = createContext<ThemeContextValue>({
    theme: 'dark',
    isDark: true,
    toggleTheme: () => {},
    setTheme: () => {},
});

function readInitialTheme(): ThemeMode {
    if (typeof window === 'undefined') return 'dark';
    const stored = window.localStorage.getItem('vl-theme');
    if (stored === 'light' || stored === 'dark') return stored;
    return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
}

export function ThemeProvider({ children }: { children: React.ReactNode }) {
    const [theme, setThemeState] = useState<ThemeMode>(readInitialTheme);

    const applyTheme = useCallback((next: ThemeMode) => {
        setThemeState(next);
        if (typeof window === 'undefined') return;

        const root = document.documentElement;
        root.classList.toggle('dark', next === 'dark');
        root.style.colorScheme = next;
        window.localStorage.setItem('vl-theme', next);

        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', next === 'dark' ? '#05080F' : '#F4F7FB');
        }

        window.dispatchEvent(new CustomEvent('vl:theme-changed', { detail: { theme: next } }));
    }, []);

    useEffect(() => {
        applyTheme(theme);
    }, []);

    const toggleTheme = useCallback(() => {
        applyTheme(theme === 'dark' ? 'light' : 'dark');
    }, [theme, applyTheme]);

    return (
        <ThemeContext.Provider
            value={{
                theme,
                isDark: theme === 'dark',
                toggleTheme,
                setTheme: applyTheme,
            }}
        >
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme(): ThemeContextValue {
    return useContext(ThemeContext);
}
