document.addEventListener('DOMContentLoaded', function () {
    const generateBtn = document.getElementById("generateBtn");
    const periodModal = document.getElementById("periodModal");
    const reportPopup = document.getElementById("reportPopup");
    const overlay = document.getElementById("overlay");
    const closePopup = document.getElementById("closePopup");
    const closePeriodModal = document.getElementById("closePeriodModal");
    const generateReportBtn = document.getElementById("generateReportBtn");
    const downloadBtn = document.getElementById("downloadBtn");
    const previewBtn = document.getElementById("previewBtn");
    const backToSelectionBtn = document.getElementById("backToSelectionBtn");

    // Show period selection modal
    if (generateBtn) {
        generateBtn.addEventListener("click", () => {
            periodModal.style.display = "block";
            overlay.style.display = "block";
        });
    }

    // Generate report based on selected period (with validation)
    if (generateReportBtn) {
        generateReportBtn.addEventListener("click", () => {
            const period = document.getElementById("reportPeriod").value;
            const customDate = document.getElementById("customDate").value;
            if (!period) {
                alert("Please select a report period (e.g. Day, Week, Month, or Custom Date).");
                return;
            }
            if (period === "custom" && !customDate) {
                alert("Please select a date for Custom Date range.");
                return;
            }
            generateReport(period, customDate);
        });
    }

    // Show/hide custom date input
    const reportPeriodSelect = document.getElementById("reportPeriod");
    if (reportPeriodSelect) {
        reportPeriodSelect.addEventListener("change", function () {
            const customContainer = document.getElementById("customDateContainer");
            if (this.value === "custom") {
                customContainer.style.display = "block";
            } else {
                customContainer.style.display = "none";
            }
        });
    }

    // Close modals
    if (closePopup) {
        closePopup.addEventListener("click", closeModals);
    }

    if (closePeriodModal) {
        closePeriodModal.addEventListener("click", closeModals);
    }

    if (overlay) {
        overlay.addEventListener("click", closeModals);
    }

    // Download report
    if (downloadBtn) {
        downloadBtn.addEventListener("click", downloadReport);
    }


    // Back to selection
    if (backToSelectionBtn) {
        backToSelectionBtn.addEventListener("click", () => {
            reportPopup.style.display = "none";
            periodModal.style.display = "block";
        });
    }

    function closeModals() {
        if (periodModal) periodModal.style.display = "none";
        if (reportPopup) reportPopup.style.display = "none";
        if (overlay) overlay.style.display = "none";
    }

    function generateReport(period, customDate = '') {
        fetch('generate_report.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ period: period, customDate: customDate })
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('reportDate').textContent = new Date().toLocaleString();
                document.getElementById('reportContent').innerHTML = data.content;
                periodModal.style.display = "none";
                reportPopup.style.display = "block";
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating report');
            });
    }

    function downloadReport() {
        const period = document.getElementById('reportPeriod').value || 'day';
        const customDate = document.getElementById('customDate').value || '';
        window.location.href = `export_report.php?type=report&period=${period}&customDate=${customDate}`;
    }


    let trafficChart;

    const trafficBtns = document.querySelectorAll(".traffic-btn");
    trafficBtns.forEach((btn, idx, btns) => {
        btn.addEventListener("click", function () {
            btns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            const slider = btn.parentElement.querySelector(".traffic-btn-slider");
            if (slider) {
                slider.style.width = btn.offsetWidth + "px";
                slider.style.left = btn.offsetLeft + "px";
            }

            // Update chart based on selected period
            updateTrafficChart(btn.textContent.toLowerCase());
        });
    });

    // On page load, set slider to active button and init chart
    const activeBtn = document.querySelector(".traffic-btn.active");
    if (activeBtn) {
        const slider = activeBtn.parentElement.querySelector(".traffic-btn-slider");
        if (slider) {
            slider.style.width = activeBtn.offsetWidth + "px";
            slider.style.left = activeBtn.offsetLeft + "px";
        }
    }

    // Initialize chart
    if (window.reportsData) {
        initChart();
    }

    function initChart() {
        const data = window.reportsData;
        const trafficCtx = document.getElementById('trafficChart');

        if (trafficCtx && data.dayTraffic) {
            trafficChart = new Chart(trafficCtx, {
                type: 'line',
                data: {
                    labels: data.dayTraffic.map(d => d.label),
                    datasets: [{
                        label: 'Entries',
                        data: data.dayTraffic.map(d => d.entries),
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }

    function updateTrafficChart(period) {
        const data = window.reportsData;
        let trafficData;

        switch (period) {
            case 'day':
                trafficData = data.dayTraffic;
                break;
            case 'week':
                trafficData = data.weekTraffic;
                break;
            case 'month':
                trafficData = data.monthTraffic;
                break;
            default:
                trafficData = data.dayTraffic;
        }

        if (trafficChart && trafficData) {
            trafficChart.data.labels = trafficData.map(d => d.label);
            trafficChart.data.datasets[0].data = trafficData.map(d => d.entries);
            trafficChart.update();
        }
    }

    // Violation Search Logic
    const searchViolationBtn = document.getElementById('searchViolationBtn');
    const searchInput = document.getElementById('violationSearchInput');
    const resultsTable = document.getElementById('violationResults');
    const resultsBody = document.getElementById('violationResultsBody');
    const noResultsMsg = document.getElementById('noResultsMsg');
    if (searchViolationBtn && searchInput) {
        searchViolationBtn.addEventListener('click', function () {
            performSearch();
        });

        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    function performSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            alert("Please enter a search term");
            return;
        }

        // Show loading state if desired (optional)

        fetch('ajax/search_violations.php?query=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data.data);
                } else {
                    console.error("Search failed:", data.message);
                    alert("Search failed: " + data.message);
                }
            })
            .catch(err => {
                console.error("Error:", err);
                alert("An error occurred during search.");
            });
    }

    function displayResults(data) {
        resultsBody.innerHTML = '';

        if (data.length === 0) {
            resultsTable.classList.add('hidden');
            noResultsMsg.classList.remove('hidden');
            return;
        }

        noResultsMsg.classList.add('hidden');
        resultsTable.classList.remove('hidden');

        data.forEach(item => {
            const row = document.createElement('tr');

            // Handle null/empty violation data
            const violationType = item.violationType || '<span style="color:#94a3b8; font-style:italic;">No Record</span>';
            const status = item.status ? item.status.toUpperCase() : 'CLEAN';
            const isPending = status === 'PENDING';
            const violationID = item.violationID || '';

            // Status styling
            let statusClass = 'status-badge';
            if (status === 'RESOLVED' || status === 'PAID') {
                statusClass += ' status-active';
            } else if (status === 'CLEAN') {
                statusClass += ' status-active';
            } else {
                statusClass += ' status-pending';
            }

            const resolveBtn = isPending && violationID
                ? `<td><button type="button" class="btn-resolve-violation" data-violation-id="${violationID}">Resolve</button></td>`
                : '<td></td>';

            row.innerHTML = `
                <td><strong>${item.plateNum}</strong></td>
                <td>
                    <div class="owner-info">
                        <strong>${item.fName} ${item.lName}</strong>
                    </div>
                </td>
                <td>
                    <div class="owner-info">
                        <span>${item.email}</span>
                        <span>${item.contact_num}</span>
                    </div>
                </td>
                <td>${item.color || ''} ${item.manufacturer || ''} ${item.model} (${item.vehicleType})</td>
                <td>${violationType}</td>
                <td><span class="${statusClass}">${status}</span></td>
                <td>${item.formatted_date}</td>
                ${resolveBtn}
            `;
            resultsBody.appendChild(row);
        });

        // Resolve button handlers
        resultsBody.querySelectorAll('.btn-resolve-violation').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-violation-id');
                if (!id) return;
                if (!confirm('Mark this violation as resolved?')) return;
                fetch('ajax/resolve_violation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'violationID=' + encodeURIComponent(id)
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert('Violation marked as resolved.');
                            performSearch();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to resolve'));
                        }
                    })
                    .catch(err => { console.error(err); alert('Request failed.'); });
            });
        });
    }
});
