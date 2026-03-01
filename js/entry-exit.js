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

    // Date filter: reload page with query params
    const dateFilterBtn = document.getElementById('dateFilterBtn');
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    if (dateFilterBtn && fromDateInput && toDateInput) {
        dateFilterBtn.addEventListener('click', function () {
            const from = fromDateInput.value || '';
            const to = toDateInput.value || '';
            const params = new URLSearchParams(window.location.search);
            if (from) params.set('fromDate', from);
            else params.delete('fromDate');
            if (to) params.set('toDate', to);
            else params.delete('toDate');
            window.location.search = params.toString();
        });
    }

    // Record Entry / Record Exit modals
    const recordEntryBtn = document.getElementById('recordEntryBtn');
    const recordExitBtn = document.getElementById('recordExitBtn');
    const recordEntryModal = document.getElementById('recordEntryModal');
    const recordExitModal = document.getElementById('recordExitModal');
    if (recordEntryBtn && recordEntryModal) {
        recordEntryBtn.addEventListener('click', () => { recordEntryModal.style.display = 'block'; });
    }
    if (recordExitBtn && recordExitModal) {
        recordExitBtn.addEventListener('click', () => { recordExitModal.style.display = 'block'; });
    }
    document.querySelectorAll('#recordEntryModal .close, #recordExitModal .close').forEach(el => {
        el.addEventListener('click', function () {
            const modal = this.closest('.modal');
            if (modal) modal.style.display = 'none';
        });
    });

    // Submit Record Entry validation
    const submitRecordEntry = document.getElementById('submitRecordEntry');
    if (submitRecordEntry) {
        submitRecordEntry.addEventListener('click', function () {
            const plate = document.getElementById('entryPlateNum');
            let errEl = document.getElementById('entryPlateError');
            if (!errEl) {
                errEl = document.createElement('span');
                errEl.id = 'entryPlateError';
                errEl.style.cssText = 'color:#dc2626;font-size:13px;display:block;margin-top:4px;';
                plate.parentNode.insertBefore(errEl, plate.nextSibling);
            }
            if (!plate || !plate.value.trim()) {
                errEl.textContent = 'Please enter a plate number or visitor ID.';
                errEl.style.display = 'block';
                return;
            }
            errEl.style.display = 'none';
            alert('Entry recorded for: ' + plate.value.trim());
            if (recordEntryModal) recordEntryModal.style.display = 'none';
            plate.value = '';
        });
    }

    // Submit Record Exit validation
    const submitRecordExit = document.getElementById('submitRecordExit');
    if (submitRecordExit) {
        submitRecordExit.addEventListener('click', function () {
            const plate = document.getElementById('exitPlateNum');
            let errEl = document.getElementById('exitPlateError');
            if (!errEl) {
                errEl = document.createElement('span');
                errEl.id = 'exitPlateError';
                errEl.style.cssText = 'color:#dc2626;font-size:13px;display:block;margin-top:4px;';
                plate.parentNode.insertBefore(errEl, plate.nextSibling);
            }
            if (!plate || !plate.value.trim()) {
                errEl.textContent = 'Please enter a plate number or visitor ID.';
                errEl.style.display = 'block';
                return;
            }
            errEl.style.display = 'none';
            alert('Exit recorded for: ' + plate.value.trim());
            if (recordExitModal) recordExitModal.style.display = 'none';
            plate.value = '';
        });
    }

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
            let matchesFilter = true;
            if (activeFilter === 'registered vehicle') matchesFilter = (type === 'registered');
            else if (activeFilter === 'visitor') matchesFilter = (type === 'visitor');

            row.style.display = matchesSearch && matchesFilter ? '' : 'none';
        });
    }

    // Initialize slider position
    const activeBtn = document.querySelector(".traffic-btn.active");
    if (activeBtn) {
        setTimeout(() => updateSlider(activeBtn), 50);
    }

    window.addEventListener('resize', () => {
        const activeBtn = document.querySelector(".traffic-btn.active");
        if (activeBtn) updateSlider(activeBtn);
    });
});
