// Staggered Entrance Animations using Intersection Observer
document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                // Add the animation class
                entry.target.classList.add('animate-fade-in-up');
                
                // If it's a grid container, stagger the children
                if (entry.target.hasAttribute('data-stagger-children')) {
                    const children = Array.from(entry.target.children);
                    children.forEach((child, index) => {
                        child.style.opacity = '0';
                        setTimeout(() => {
                            child.classList.add('animate-fade-in-up');
                            child.style.opacity = '1';
                        }, index * 100); // 100ms delay between each child
                    });
                }
                
                // Stop observing once animated
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with data-animate attribute
    document.querySelectorAll('[data-animate]').forEach((el) => {
        // Pre-hide elements before animation starts
        if (!el.hasAttribute('data-stagger-children')) {
            el.style.opacity = '0';
        }
        observer.observe(el);
    });
});
