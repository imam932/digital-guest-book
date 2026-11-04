document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.admin-sidebar .nav-link');

    navLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            navLinks.forEach((item) => item.classList.remove('active'));
            event.currentTarget.classList.add('active');
        });
    });
});
