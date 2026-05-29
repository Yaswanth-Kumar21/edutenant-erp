<script>
/* ── Sidebar Toggle ─────────────────────────────────────────────────── */
(function () {
    const sidebar  = document.getElementById('sidebar');
    const main     = document.getElementById('main-content');
    const toggle   = document.getElementById('sidebar-toggle');
    const topToggle= document.getElementById('topnav-toggle');
    const overlay  = document.getElementById('sidebar-overlay');

    function isMobile() { return window.innerWidth < 768; }

    function doToggle() {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('expanded');
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
        }
    }

    if (toggle)    toggle.addEventListener('click', doToggle);
    if (topToggle) topToggle.addEventListener('click', doToggle);

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    // Restore collapsed state on desktop
    if (!isMobile() && localStorage.getItem('sidebar_collapsed') === 'true') {
        sidebar?.classList.add('collapsed');
        main?.classList.add('expanded');
    }

    // Dark mode toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon   = document.getElementById('theme-icon');
    const html        = document.documentElement;

    if (localStorage.getItem('theme') === 'dark') {
        html.setAttribute('data-theme', 'dark');
        themeIcon?.classList.replace('fa-moon', 'fa-sun');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const isDark = html.getAttribute('data-theme') === 'dark';
            html.setAttribute('data-theme', isDark ? 'light' : 'dark');
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
            themeIcon?.classList.replace(isDark ? 'fa-sun' : 'fa-moon', isDark ? 'fa-moon' : 'fa-sun');
        });
    }
})();

/* ── Toast Notifications ────────────────────────────────────────────── */
(function () {
    @if(session('success'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
    });
    @endif

    @if(session('error'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: @json(session('error')),
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
    });
    @endif

    @if(session('warning'))
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title: @json(session('warning')),
        showConfirmButton: false,
        timer: 4000,
    });
    @endif
})();

/* ── Confirm Delete ─────────────────────────────────────────────────── */
document.querySelectorAll('[data-confirm-delete]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const formId = this.dataset.confirmDelete;
        const name   = this.dataset.name || 'this record';
        Swal.fire({
            title: 'Delete ' + name + '?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    });
});

/* ── Counter Animation ──────────────────────────────────────────────── */
document.querySelectorAll('[data-counter]').forEach(function (el) {
    const target   = parseInt(el.dataset.target || el.textContent, 10);
    const duration = 800;
    const step     = Math.ceil(target / (duration / 16));
    let current    = 0;
    const timer    = setInterval(function () {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        el.textContent = current.toLocaleString('en-IN');
    }, 16);
});
</script>
