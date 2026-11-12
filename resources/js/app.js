// Subtle scroll reveal for elements with class "reveal" (home page sections).
// Content stays visible if JavaScript or IntersectionObserver is unavailable, and nothing
// moves for visitors who prefer reduced motion (see resources/css/app.css).
const revealables = document.querySelectorAll('.reveal');

if (revealables.length && 'IntersectionObserver' in window
    && ! window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.documentElement.classList.add('reveal-ready');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

    revealables.forEach((el) => observer.observe(el));
}
