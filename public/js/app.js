const toggle = document.getElementById('menuToggle');
const navLinks = document.querySelector('.nav-links');
const form = document.getElementById('form');

toggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

console.log(navLinks)
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#contact-form');
    const btn = form?.querySelector('button');

    if (!form) return;

    form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.innerText = 'Sending...';
    });
});

// Contact = Form → Route → Controller → Validate → Mail → Feedback
// Caching = تقليل DB + سرعة