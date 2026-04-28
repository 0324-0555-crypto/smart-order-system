// Smart Order Management System - main.js

document.addEventListener('DOMContentLoaded', function () {

    // Sidebar toggle
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('sidebar');
    const main      = document.querySelector('.main-content');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
                if (main) {
                    main.style.marginLeft = sidebar.classList.contains('collapsed') ? '70px' : '260px';
                }
            }
        });
    }

    // Auto-dismiss alerts after 4 seconds
    const alerts = document.querySelectorAll('.alert-custom');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Tooltip init (Bootstrap 5)
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.map(function (el) { return new bootstrap.Tooltip(el); });
});