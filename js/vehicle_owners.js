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

    // Add Owner button
    const addOwnerBtn = document.getElementById('addOwnerBtn');
    if (addOwnerBtn) {
        addOwnerBtn.addEventListener('click', function () {
            document.getElementById('addOwnerMessage').classList.add('hidden');
            document.getElementById('addOwnerForm').reset();
            const empField = document.getElementById('addEmploymentTypeField');
            if (empField) { empField.classList.add('hidden'); empField.style.display = 'none'; }
            document.getElementById('addOwnerModal').style.display = 'flex';
        });
    }

    // Add Owner: role change to show employment type
    const addRole = document.getElementById('addRole');
    if (addRole) {
        addRole.addEventListener('change', function () {
            const empField = document.getElementById('addEmploymentTypeField');
            if (!empField) return;
            if (this.value === 'faculty' || this.value === 'non-teaching') {
                empField.classList.remove('hidden');
                empField.style.display = 'block';
            } else {
                empField.classList.add('hidden');
                empField.style.display = 'none';
            }
        });
    }

    // Contact number validation (10-15 digits, optional + - space)
    function validateContact(value) {
        const v = (value || '').trim();
        if (v.length < 10 || v.length > 15) return false;
        return /^[0-9+\s\-]+$/.test(v);
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
                            <p><strong>School ID:</strong> ${owner.schoolID || 'N/A'}</p>
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
                        document.getElementById('editSchoolID').value = owner.schoolID || '';
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

    // Save owner edit (with contact validation)
    const saveOwnerEditBtn = document.getElementById('saveOwnerEdit');
    if (saveOwnerEditBtn) {
        saveOwnerEditBtn.addEventListener('click', function () {
            const contactVal = document.getElementById('editContact').value;
            const editContactError = document.getElementById('editContactError');
            if (editContactError) editContactError.classList.add('hidden');
            if (!validateContact(contactVal)) {
                if (editContactError) {
                    editContactError.classList.remove('hidden');
                }
                return;
            }
            const adminPassword = document.getElementById('editOwnerAdminPassword').value;

            if (!adminPassword) {
                alert('Please enter your admin password');
                return;
            }

            verifyAdminPassword(adminPassword)
                .then(success => {
                    if (success) {
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

    // Save Add Owner
    const saveAddOwnerBtn = document.getElementById('saveAddOwner');
    if (saveAddOwnerBtn) {
        saveAddOwnerBtn.addEventListener('click', function () {
            const msgEl = document.getElementById('addOwnerMessage');
            const contactVal = document.getElementById('addContact').value;
            const addContactError = document.getElementById('addContactError');
            if (addContactError) addContactError.classList.add('hidden');
            if (msgEl) msgEl.classList.add('hidden');

            if (!validateContact(contactVal)) {
                if (addContactError) {
                    addContactError.classList.remove('hidden');
                }
                if (msgEl) {
                    msgEl.textContent = 'Invalid phone/contact format. Use 10-15 digits (e.g. 09XXXXXXXXX).';
                    msgEl.classList.remove('hidden');
                    msgEl.classList.add('error');
                }
                return;
            }

            const adminPassword = document.getElementById('addOwnerAdminPassword').value;
            if (!adminPassword) {
                if (msgEl) { msgEl.textContent = 'Please enter admin password.'; msgEl.classList.remove('hidden'); msgEl.classList.add('error'); }
                return;
            }

            verifyAdminPassword(adminPassword)
                .then(success => {
                    if (success) {
                        const formData = new FormData(document.getElementById('addOwnerForm'));
                        saveAddOwnerBtn.disabled = true;
                        fetch('ajax/add_owner.php', { method: 'POST', body: formData })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Owner added successfully!');
                                    document.getElementById('addOwnerModal').style.display = 'none';
                                    location.reload();
                                } else {
                                    if (msgEl) { msgEl.textContent = data.message || 'Failed to add owner.'; msgEl.classList.remove('hidden'); msgEl.classList.add('error'); }
                                    saveAddOwnerBtn.disabled = false;
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                if (msgEl) { msgEl.textContent = 'Failed to add owner.'; msgEl.classList.remove('hidden'); msgEl.classList.add('error'); }
                                saveAddOwnerBtn.disabled = false;
                            });
                    }
                });
        });
    }

    // Toggle owner status functionality (Opening Modal)
    document.querySelectorAll('.btn-toggle-status:not(#confirmToggleStatusBtn)').forEach(btn => {
        btn.addEventListener('click', function () {
            const ownerID = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status');
            document.getElementById('toggleStatusOwnerID').value = ownerID;
            document.getElementById('toggleStatusCurrent').value = currentStatus;
            const toggleModal = document.getElementById('toggleStatusOwnerModal');
            if (toggleModal) toggleModal.style.display = 'flex';
        });
    });

    // Confirm Toggle Action
    const confirmToggleBtn = document.getElementById('confirmToggleStatusBtn');
    if (confirmToggleBtn) {
        confirmToggleBtn.addEventListener('click', performToggleStatusOwner);
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

// Function to attach toggle logic 
function performToggleStatusOwner() {
    const adminPassword = document.getElementById('toggleStatusAdminPassword').value;
    const ownerID = document.getElementById('toggleStatusOwnerID').value;
    const currentStatus = document.getElementById('toggleStatusCurrent').value;

    if (!adminPassword) {
        alert('Please enter your admin password');
        return;
    }

    verifyAdminPassword(adminPassword)
        .then(success => {
            if (success) {
                const formData = new FormData();
                formData.append('id', ownerID);
                formData.append('current_status', currentStatus);

                fetch('ajax/toggle_owner_status.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Owner status updated successfully!');
                            document.getElementById('toggleStatusOwnerModal').style.display = 'none';
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to update owner status');
                    });
            }
        });
}
