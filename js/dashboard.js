let trafficChart;
let visitorChart;

document.addEventListener('DOMContentLoaded', () => {
  // Auto-refresh occupancy data every 30 seconds
  setInterval(refreshOccupancyData, 30000);
  
  // Traffic button functionality for gate traffic only
  document.querySelector('#gate-traffic').querySelectorAll('.traffic-btn').forEach((btn, idx, btns) => {
    btn.addEventListener('click', function() {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const slider = btn.parentElement.querySelector('.traffic-btn-slider');
      slider.style.width = btn.offsetWidth + 'px';
      slider.style.left = btn.offsetLeft + 'px';
      
      // Update traffic chart based on selected period
      updateTrafficChart(btn.textContent.toLowerCase());
    });
  });
  
  // Traffic button functionality for visitor trend only
  document.querySelector('#daily-visitor-trend').querySelectorAll('.traffic-btn').forEach((btn, idx, btns) => {
    btn.addEventListener('click', function() {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const slider = btn.parentElement.querySelector('.traffic-btn-slider');
      slider.style.width = btn.offsetWidth + 'px';
      slider.style.left = btn.offsetLeft + 'px';
      
      // Update visitor chart based on selected period
      updateVisitorChart(btn.textContent.toLowerCase());
    });
  });

  // Set slider to active buttons for both charts
  document.querySelectorAll('.traffic-btn.active').forEach(activeBtn => {
    const slider = activeBtn.parentElement.querySelector('.traffic-btn-slider');
    slider.style.width = activeBtn.offsetWidth + 'px';
    slider.style.left = activeBtn.offsetLeft + 'px';
  });

  // Initialize charts
  if (window.dashboardData) {
    initCharts();
  }
});

function initCharts() {
  const data = window.dashboardData;

  // Vehicle Types Pie Chart
  const vehicleCtx = document.getElementById('vehicleTypesChart');
  if (vehicleCtx && data.vehicleTypes.length > 0) {
    new Chart(vehicleCtx, {
      type: 'pie',
      data: {
        labels: data.vehicleTypes.map(v => v.vehicleType),
        datasets: [{
          data: data.vehicleTypes.map(v => v.count),
          backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
      }
    });
  }

  // Daily Visitor Trend Line Chart
  const trendCtx = document.getElementById('visitorTrendChart');
  if (trendCtx && data.dayVisitorTraffic) {
    visitorChart = new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: data.dayVisitorTraffic.map(d => d.label),
        datasets: [{
          label: 'Visitor Entries',
          data: data.dayVisitorTraffic.map(d => d.entries),
          borderColor: '#FF6384',
          backgroundColor: 'rgba(255, 99, 132, 0.1)',
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

  // Gate Traffic Line Chart
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
  const data = window.dashboardData;
  let trafficData;
  
  switch(period) {
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

function updateVisitorChart(period) {
  const data = window.dashboardData;
  let trafficData;
  
  switch(period) {
    case 'day':
      trafficData = data.dayVisitorTraffic;
      break;
    case 'week':
      trafficData = data.weekVisitorTraffic;
      break;
    case 'month':
      trafficData = data.monthVisitorTraffic;
      break;
    default:
      trafficData = data.dayVisitorTraffic;
  }
  
  if (visitorChart && trafficData) {
    visitorChart.data.labels = trafficData.map(d => d.label);
    visitorChart.data.datasets[0].data = trafficData.map(d => d.entries);
    visitorChart.update();
  }
}

function refreshOccupancyData() {
  fetch('ajax/get_occupancy_data.php')
    .then(response => response.json())
    .then(data => {
      // Update campus occupancy display
      const occupancyElement = document.querySelector('.card-num-occ');
      if (occupancyElement) {
        occupancyElement.textContent = `${data.total_occupied} / ${data.total_capacity}`;
      }
      
      // Update progress bar
      const progressElement = document.querySelector('.occupancy-progress');
      if (progressElement) {
        progressElement.style.width = `${data.occupancy_percentage}%`;
      }
      
      // Update status
      const statusElement = document.querySelector('.occupancy-status');
      if (statusElement) {
        const isNearlyFull = data.occupancy_percentage > 90;
        statusElement.textContent = isNearlyFull ? 'Nearly Full' : 'Parking Available';
        statusElement.className = `occupancy-status ${isNearlyFull ? 'full' : 'available'}`;
      }
      

    })
    .catch(error => {
      console.log('Error refreshing occupancy data:', error);
    });
}