document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('allocationForm');
    const inputs = form.querySelectorAll('input[type="number"]');
    const totalAllocatedSpan = document.getElementById('totalAllocated');
    const remainingSpan = document.getElementById('remaining');

    // Auto-refresh occupancy data every 30 seconds
    setInterval(refreshAllocationData, 30000);

    function updateSummary() {
        const totalCapacity = parseInt(document.getElementById('totalCapacity').value) || 0;
        const students = parseInt(document.getElementById('allocatedStudents').value) || 0;
        const faculty = parseInt(document.getElementById('allocatedFaculty').value) || 0;
        const staff = parseInt(document.getElementById('allocatedStaff').value) || 0;
        const guests = parseInt(document.getElementById('allocatedGuests').value) || 0;

        const totalAllocated = students + faculty + staff + guests;
        const remaining = totalCapacity - totalAllocated;

        totalAllocatedSpan.textContent = totalAllocated;
        remainingSpan.textContent = remaining;

        // Update color based on remaining spaces
        if (remaining < 0) {
            remainingSpan.style.color = '#d32f2f';
            remainingSpan.parentElement.style.background = '#ffebee';
        } else if (remaining === 0) {
            remainingSpan.style.color = '#f57c00';
            remainingSpan.parentElement.style.background = '#fff3e0';
        } else {
            remainingSpan.style.color = '#388e3c';
            remainingSpan.parentElement.style.background = '#e8f5e8';
        }
    }

    // Add event listeners to all number inputs
    inputs.forEach(input => {
        input.addEventListener('input', updateSummary);
    });

    // Form validation
    form.addEventListener('submit', function (e) {
        const totalCapacity = parseInt(document.getElementById('totalCapacity').value) || 0;
        const totalAllocated = parseInt(totalAllocatedSpan.textContent) || 0;

        if (totalAllocated > totalCapacity) {
            e.preventDefault();
            alert('Total allocated spaces cannot exceed total capacity!');
        }
    });

    // Initialize summary
    updateSummary();
});

function refreshAllocationData() {
    fetch('ajax/get_occupancy_data.php')
        .then(response => response.json())
        .then(data => {
            // Update allocation cards with current occupancy
            const roles = ['students', 'faculty', 'staff', 'guests'];
            roles.forEach(role => {
                const card = document.querySelector(`.allocation-card.${role}`);
                if (card) {
                    const occupiedSpan = card.querySelector('.card-occupied');
                    const progressBar = card.querySelector('.progress-bar');
                    const availabilitySpan = card.querySelector('.card-availability');

                    if (occupiedSpan) {
                        occupiedSpan.textContent = `${data.occupancy_by_role[role]} occupied`;
                    }

                    if (progressBar && data.allocations[role] > 0) {
                        const percentage = (data.occupancy_by_role[role] / data.allocations[role]) * 100;
                        progressBar.style.width = `${percentage}%`;
                    }

                    if (availabilitySpan) {
                        const available = Math.max(0, data.allocations[role] - data.occupancy_by_role[role]);
                        const isNearlyFull = (data.occupancy_by_role[role] / Math.max(data.allocations[role], 1)) > 0.9;
                        availabilitySpan.textContent = `${available} available`;
                        availabilitySpan.className = `card-availability ${isNearlyFull ? 'full' : 'available'}`;
                    }
                }
            });
        });
}

function calculateDistributions() {
    const total = parseInt(document.getElementById('totalCapacity').value) || 0;

    // 80% for Faculty/Staff/Guests (combined for now, distributed roughly)
    // 20% for Students
    const studentAlloc = Math.round(total * 0.20);
    const othersAlloc = total - studentAlloc; // 80% remaining

    // Further split the 80% (just an example distribution, can be adjusted)
    // Let's say: 50% Faculty, 30% Staff, 20% Guests OF THE 80%
    const facultyAlloc = Math.round(othersAlloc * 0.60); // Bulk goes to Faculty
    const staffAlloc = Math.round(othersAlloc * 0.25);
    const guestsAlloc = othersAlloc - facultyAlloc - staffAlloc;

    document.getElementById('allocatedStudents').value = studentAlloc;
    document.getElementById('allocatedFaculty').value = facultyAlloc;
    document.getElementById('allocatedStaff').value = staffAlloc;
    document.getElementById('allocatedGuests').value = guestsAlloc;

    // Trigger the update for key totals if needed by firing input event
    // The existing code listens to input event on number inputs
    const event = new Event('input');
    document.getElementById('allocatedStudents').dispatchEvent(event);
    document.getElementById('allocatedFaculty').dispatchEvent(event);
    document.getElementById('allocatedStaff').dispatchEvent(event);
    document.getElementById('allocatedGuests').dispatchEvent(event);
}