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

    // Filter by role
    const filterRole = document.getElementById('filterRole');
    if (filterRole) {
        filterRole.addEventListener('change', function () {
            const filterValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');

            if (filterValue === '') {
                rows.forEach(row => row.style.display = '');
                return;
            }

            rows.forEach(row => {
                const roleCell = row.querySelector('.role-badge');
                if (roleCell) {
                    const role = roleCell.textContent.toLowerCase().trim();
                    const match = (filterValue === 'non-teaching' && role === 'non-teaching') ||
                        role === filterValue;
                    row.style.display = match ? '' : 'none';
                }
            });
        });
    }

    // View owner functionality
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function () {
            const ownerID = this.getAttribute('data-id');
            fetch('ajax/get_owner.php?ownerID=' + ownerID)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const owner = data.owner;
                        document.getElementById('viewOwnerContent').innerHTML = `
                            <p><strong>Owner ID:</strong> ${owner.OwnerID}</p>
                            <p><strong>Name:</strong> ${owner.fName} ${owner.lName} ${owner.mName || ''}</p>
                            <p><strong>Role:</strong> ${owner.role}</p>
                            <p><strong>Email:</strong> ${owner.email}</p>
                            <p><strong>Contact:</strong> ${owner.contact_num}</p>
                            <p><strong>College:</strong> ${owner.college}</p>
                            <p><strong>Course:</strong> ${owner.course || 'N/A'}</p>
                            <p><strong>Year:</strong> ${owner.year || 'N/A'}</p>
                            <p><strong>Section:</strong> ${owner.section || 'N/A'}</p>
                            <p><strong>Academic Year:</strong> ${owner.academicYear || 'N/A'}</p>
                            ${owner.employment_type ? `<p><strong>Employment Type:</strong> ${owner.employment_type}</p>` : ''}
                            <p><strong>Status:</strong> ${owner.registrationStatus}</p>
                        `;
                        document.getElementById('viewOwnerModal').style.display = 'flex'; // Changed to flex for centering if CSS supports it, or block
                        // Ensure modal display style matches CSS
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load owner data');
                });
        });
    });

    // Edit owner functionality
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const ownerID = this.getAttribute('data-id');
            fetch('ajax/get_owner.php?ownerID=' + ownerID)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const owner = data.owner;
                        document.getElementById('editOwnerID').value = owner.OwnerID;
                        document.getElementById('editFName').value = owner.fName;
                        document.getElementById('editLName').value = owner.lName;
                        document.getElementById('editMName').value = owner.mName || '';
                        document.getElementById('editEmail').value = owner.email;
                        document.getElementById('editContact').value = owner.contact_num;
                        document.getElementById('editCollege').value = owner.college;
                        document.getElementById('editCourse').value = owner.course || '';

                        // Show employment type field for faculty/non-teaching
                        const employmentField = document.getElementById('editEmploymentTypeField');
                        if (owner.role === 'faculty' || owner.role === 'non-teaching') {
                            if (employmentField) employmentField.classList.remove('hidden'); // Use class
                            if (employmentField) employmentField.style.display = 'block'; // Fallback
                            const select = document.getElementById('editEmploymentType');
                            if (select) select.value = owner.employment_type || '';
                        } else {
                            if (employmentField) employmentField.classList.add('hidden');
                            if (employmentField) employmentField.style.display = 'none';
                        }

                        const editModal = document.getElementById('editOwnerModal');
                        if (editModal) editModal.style.display = 'flex';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load owner data');
                });
        });
    });

    // Close modals
    document.querySelectorAll('.close, .btn-cancel').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = this.closest('.modal');
            if (modal) modal.style.display = 'none';

            // Clear passwords
            const pwFields = document.querySelectorAll('input[type="password"]');
            pwFields.forEach(f => f.value = '');
        });
    });

    // Close on click outside
    window.addEventListener('click', function (event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });

    // Save owner edit
    const saveOwnerEditBtn = document.getElementById('saveOwnerEdit');
    if (saveOwnerEditBtn) {
        saveOwnerEditBtn.addEventListener('click', function () {
            const adminPassword = document.getElementById('editOwnerAdminPassword').value;

            if (!adminPassword) {
                alert('Please enter your admin password');
                return;
            }

            // Verify admin password first
            verifyAdminPassword(adminPassword)
                .then(success => {
                    if (success) {
                        // Admin verified, proceed with updating owner
                        const formData = new FormData(document.getElementById('editOwnerForm'));

                        fetch('ajax/update_owner.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Owner updated successfully!');
                                    document.getElementById('editOwnerModal').style.display = 'none';
                                    location.reload();
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Failed to update owner');
                            });
                    }
                });
        });
    }

    // Delete owner functionality (Opening Modal)
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const ownerID = this.getAttribute('data-id');
            document.getElementById('deleteOwnerID').value = ownerID;
            document.getElementById('deleteOwnerModal').style.display = 'flex';
        });
    });

    // Confirm Delete Action
    const confirmDeleteBtn = document.getElementById('confirmDeleteOwnerBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', performDeleteOwner);
    }
});

// Helper validation function
function verifyAdminPassword(password) {
    return fetch('ajax/verify_admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `password=${encodeURIComponent(password)}`
    })
        .then(response => response.json())
        .then(result => {
            if (!result.success) {
                alert(result.message || 'Invalid admin password');
                return false;
            }
            return true;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to verify admin password');
            return false;
        });
}

// Function to attach delete logic (called from DOMContentLoaded or accessible global)
function performDeleteOwner() {
    const adminPassword = document.getElementById('deleteOwnerAdminPassword').value;
    const ownerID = document.getElementById('deleteOwnerID').value;

    if (!adminPassword) {
        alert('Please enter your admin password');
        return;
    }

    verifyAdminPassword(adminPassword)
        .then(success => {
            if (success) {
                const formData = new FormData();
                formData.append('ownerID', ownerID);

                fetch('ajax/delete_owner.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Owner deleted successfully!');
                            document.getElementById('deleteOwnerModal').style.display = 'none';
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete owner');
                    });
            }
        });
}
