// public/js/main.js - Interactive helpers & Accessible Modals

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Navigation Toggle
    const navToggle = document.querySelector('.mobile-nav-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (navToggle && navLinks) {
        navToggle.addEventListener('click', () => {
            const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', !isExpanded);
            navLinks.classList.toggle('show');
        });
    }

    // 2. Accessible Confirmation Dialogs
    // Any form or button with data-confirm="Message"
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', (e) => {
            const message = element.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // 3. Auto-calculate minimum datetime-local for listings
    const availableUntilInput = document.querySelector('input[type="datetime-local"][name="available_until"]');
    if (availableUntilInput && !availableUntilInput.value) {
        const now = new Date();
        now.setHours(now.getHours() + 4); // default 4 hours from now
        // Format to YYYY-MM-DDTHH:MM
        const pad = (n) => String(n).padStart(2, '0');
        const formatted = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        availableUntilInput.min = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    }
});
