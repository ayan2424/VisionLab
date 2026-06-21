import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Force dark mode
document.documentElement.classList.add('dark');

Alpine.start();
