// My List page: after favorite toggle removes an item, animate the card out
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-favorite-toggle]');
        if (!btn) return;

        // Wait for the global handler in app.js to process the toggle
        // Then check if this is the my-list page and remove the card
        const titleId = btn.dataset.titleId;
        if (!titleId) return;

        // Listen for the axios response indirectly via a small delay
        // (the global handler fires first since it's registered first)
        setTimeout(() => {
            // If the button's SVG fill changed to 'none', item was removed
            const svg = btn.querySelector('svg');
            if (svg && svg.getAttribute('fill') === 'none') {
                const card = btn.closest('.group\\/card');
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(() => card.remove(), 300);
                }
            }
        }, 300);
    });
});
