document.addEventListener('DOMContentLoaded', function () {
    // Client-side search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    }

    // Filter by action
    const filterAction = document.getElementById('filterAction');
    if (filterAction) {
        filterAction.addEventListener('change', function () {
            const filterValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            if (filterValue === '') {
                rows.forEach(row => row.style.display = '');
                return;
            }

            rows.forEach(row => {
                const actionCell = row.querySelector('.action-badge');
                if (actionCell) {
                    const action = actionCell.textContent.toLowerCase().trim();
                    row.style.display = action === filterValue ? '' : 'none';
                }
            });
        });
    }
});

// Toggle logs visibility
function toggleLogs() {
    const hiddenLogs = document.querySelectorAll('.hidden-log');
    const btnText = document.getElementById('btnText');
    const btn = document.getElementById('viewAllBtn');

    // Using check for display style or class presence could be tricky if filtering is also active
    // But generally, hidden-log class is applied initially.
    // The simplest check is based on the button text

    if (btnText.textContent === 'View All') {
        hiddenLogs.forEach(row => row.style.display = 'table-row');
        btnText.textContent = 'Show Less';
        btn.querySelector('svg path').setAttribute('d', 'M18 15l-6-6-6 6');
    } else {
        hiddenLogs.forEach(row => row.style.display = 'none');
        btnText.textContent = 'View All';
        btn.querySelector('svg path').setAttribute('d', 'M6 9l6 6 6-6');
    }
}
