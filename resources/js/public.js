document.addEventListener('DOMContentLoaded', () => {
    const yearNode = document.querySelector('[data-current-year]');

    if (yearNode) {
        yearNode.textContent = new Date().getFullYear();
    }
});
