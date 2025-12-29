document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".traffic-btn");
    const slider = document.querySelector(".traffic-btn-slider");

    function updateSlider(activeBtn) {
        if (!activeBtn || !slider) return;
        slider.style.width = activeBtn.offsetWidth + "px";
        slider.style.left = activeBtn.offsetLeft + "px";
    }

    buttons.forEach((btn) => {
        btn.addEventListener("click", function () {
            buttons.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            updateSlider(btn);
            filterLogs();
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            filterLogs();
        });
    }

    function filterLogs() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;

        const searchValue = searchInput.value.toLowerCase();
        const activeBtn = document.querySelector('.traffic-btn.active');
        const activeFilter = activeBtn ? activeBtn.textContent.toLowerCase() : '';
        const rows = document.querySelectorAll('#logsTableBody tr');

        rows.forEach(row => {
            // Skip no data row
            if (row.cells.length === 1 && row.cells[0].colSpan > 1) return;

            const text = row.textContent.toLowerCase();
            const type = row.getAttribute('data-type');

            const matchesSearch = text.includes(searchValue);
            const matchesFilter = activeFilter === 'registered vehicle' ? type === 'registered' : type === 'visitor';

            row.style.display = matchesSearch && matchesFilter ? '' : 'none';
        });
    }

    // Initialize slider position
    const activeBtn = document.querySelector(".traffic-btn.active");
    if (activeBtn) {
        // Wait a slight tick for layout to settle if needed, or call immediately
        setTimeout(() => updateSlider(activeBtn), 50);
    }

    // Handle window resize to adjust slider
    window.addEventListener('resize', () => {
        const activeBtn = document.querySelector(".traffic-btn.active");
        if (activeBtn) updateSlider(activeBtn);
    });
});
