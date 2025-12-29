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

    // Generate report based on selected period
    if (generateReportBtn) {
        generateReportBtn.addEventListener("click", () => {
            const period = document.getElementById("reportPeriod").value;
            const customDate = document.getElementById("customDate").value;
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

    // Preview report
    if (previewBtn) {
        previewBtn.addEventListener("click", previewReport);
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

    function previewReport() {
        const period = document.getElementById('reportPeriod').value;
        const customDate = document.getElementById('customDate').value;

        fetch('preview_report.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ period: period, customDate: customDate })
        })
            .then(response => response.json())
            .then(data => {
                const previewWindow = window.open('', '_blank', 'width=1000,height=700');
                previewWindow.document.write(data.html);
                previewWindow.document.close();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating preview');
            });
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
});
