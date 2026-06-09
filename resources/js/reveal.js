/*
 * Directive v-reveal : anime l'apparition d'un élément quand il entre dans
 * le viewport (fondu + léger glissement vers le haut). Léger, sans dépendance.
 *
 * Usage : <div v-reveal>…</div>  ou  <div v-reveal="150"> (délai en ms)
 */
export const reveal = {
    mounted(el, binding) {
        // Désactivation des animations : ?nomotion dans l'URL ou préférence système.
        const reduce =
            (typeof window !== 'undefined' &&
                new URLSearchParams(window.location.search).has('nomotion')) ||
            window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
        if (reduce) return;

        const delay = Number(binding.value) || 0;

        el.style.opacity = '0';
        el.style.transform = 'translateY(28px)';
        el.style.transition = `opacity 0.7s ease-out ${delay}ms, transform 0.7s ease-out ${delay}ms`;
        el.style.willChange = 'opacity, transform';

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                        observer.unobserve(el);
                    }
                });
            },
            { threshold: 0.12 },
        );

        observer.observe(el);
    },
};
