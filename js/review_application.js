// JavaScript for review_application.php

document.addEventListener('DOMContentLoaded', function () {
    // Image preview enhancement
    const documentImages = document.querySelectorAll('.document-preview img');
    documentImages.forEach(img => {
        img.addEventListener('click', function () {
            window.open(this.src, '_blank');
        });
        img.style.cursor = 'pointer';
        img.title = 'Click to view full size';
    });
});

function showRejectModal() {
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.getElementById('rejectReasonText').value = '';
}

function submitRejection() {
    const reason = document.getElementById('rejectReasonText').value.trim();
    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }

    const btn = document.getElementById('confirmRejectBtn');
    btn.disabled = true;
    btn.textContent = 'Processing...';

    document.getElementById('rejectionReason').value = reason;
    closeRejectModal();

    // Trigger loading animation
    if (window.loadingAnimation) {
        window.loadingAnimation.show('reject');
    }

    // Create a hidden input for the reject action
    const form = document.getElementById('reviewForm');
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'reject';
    form.appendChild(actionInput);

    form.submit();
}