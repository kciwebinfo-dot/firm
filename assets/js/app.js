document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');
    document.getElementById('mobileMenu')?.addEventListener('click', () => {
        sidebar?.classList.add('open');
        overlay?.classList.add('show');
    });
    overlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        overlay.classList.remove('show');
    });
    document.getElementById('sidebarCollapse')?.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-mini');
    });

    if (window.jQuery && $('.data-table').length) {
        $('.data-table').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['excel', 'pdf', 'print'],
            pageLength: 5
        });
    }

    const chartCanvas = document.getElementById('workChart');
    if (chartCanvas) {
        new Chart(chartCanvas, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                datasets: [{
                    label: 'Completed Work',
                    data: [8, 12, 10, 16, 19, 14],
                    borderColor: getComputedStyle(document.body).getPropertyValue('--theme'),
                    backgroundColor: 'rgba(67, 97, 238, .12)',
                    tension: .4,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    document.getElementById('sweetDemo')?.addEventListener('click', () => {
        Swal.fire('Success', 'Dashboard widgets are ready.', 'success');
    });

    document.querySelectorAll('[data-preview]').forEach(input => {
        input.addEventListener('change', () => {
            const target = document.querySelector(input.dataset.preview);
            const file = input.files[0];
            if (target && file) target.src = URL.createObjectURL(file);
        });
    });
});
