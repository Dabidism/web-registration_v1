// JavaScript for registration_applications.php

document.addEventListener('DOMContentLoaded', function() {
    // Status filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        window.location.href = 'registration_applications.php?status=' + status;
    });
    
    // Role filter functionality
    document.getElementById('roleFilter').addEventListener('change', function() {
        const filterValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('.application-row');
        
        if (filterValue === 'all') {
            rows.forEach(row => row.style.display = '');
            return;
        }
        
        rows.forEach(row => {
            const roleData = row.getAttribute('data-role');
            if (roleData) {
                row.style.display = roleData === filterValue ? '' : 'none';
            }
        });
    });
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('.application-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.applicant-name').textContent.toLowerCase();
            const dept = row.querySelector('.applicant-dept').textContent.toLowerCase();
            row.style.display = (name.includes(searchValue) || dept.includes(searchValue)) ? '' : 'none';
        });
    });
});