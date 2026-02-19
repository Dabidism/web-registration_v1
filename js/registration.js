document.addEventListener('DOMContentLoaded', function () {
    // === File Upload Logic ===
    function initUploadArea(area) {
        const fileInput = area.querySelector('input[type="file"]');
        const browse = area.querySelector('.browse');

        area.addEventListener('dragover', function (e) {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', function (e) {
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', function (e) {
            e.preventDefault();
            area.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                showFileName(area, e.dataTransfer.files[0].name);
            }
        });

        browse.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function (e) {
            if (e.target.files.length) {
                showFileName(area, e.target.files[0].name);
            }
        });
    }

    function showFileName(area, fileName) {
        const existing = area.querySelector('.file-name');
        if (existing) existing.remove();

        const fileDiv = document.createElement('div');
        fileDiv.className = 'file-name file-preview-text';
        fileDiv.textContent = 'Selected: ' + fileName;
        area.appendChild(fileDiv);
    }

    document.querySelectorAll('.upload-area').forEach(initUploadArea);

    // === User Type Logic ===
    const userTypeRadios = document.querySelectorAll('input[name="userType"]');

    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            const courseField = document.getElementById('courseField');
            const academicYearField = document.getElementById('academicYearField');
            const yearLevelField = document.getElementById('yearLevelField');
            const sectionField = document.getElementById('sectionField');

            const employmentTypeField = document.getElementById('employmentTypeField');

            if (this.value === 'faculty' || this.value === 'non-teaching') {
                if (courseField) courseField.style.display = 'none';
                if (academicYearField) academicYearField.style.display = 'none';
                if (yearLevelField) yearLevelField.style.display = 'none';
                if (sectionField) sectionField.style.display = 'none';
                if (employmentTypeField) employmentTypeField.style.display = 'block';

                const courseSelect = document.querySelector('select[name="course"]');
                const academicYearSelect = document.querySelector('select[name="academicYear"]');
                const employmentTypeSelect = document.querySelector('select[name="employment_type"]');

                if (courseSelect) courseSelect.removeAttribute('required');
                if (academicYearSelect) academicYearSelect.removeAttribute('required');
                if (employmentTypeSelect) employmentTypeSelect.setAttribute('required', 'required');
            } else {
                if (courseField) courseField.style.display = 'block';
                if (academicYearField) academicYearField.style.display = 'block';
                if (yearLevelField) yearLevelField.style.display = 'block';
                if (sectionField) sectionField.style.display = 'block';
                if (employmentTypeField) employmentTypeField.style.display = 'none';

                const courseSelect = document.querySelector('select[name="course"]');
                const academicYearSelect = document.querySelector('select[name="academicYear"]');
                const employmentTypeSelect = document.querySelector('select[name="employment_type"]');

                if (courseSelect) courseSelect.setAttribute('required', 'required');
                if (academicYearSelect) academicYearSelect.setAttribute('required', 'required');
                if (employmentTypeSelect) employmentTypeSelect.removeAttribute('required');
            }
        });
    });

    // === College-Course Mapping ===
    let collegeCourses = {};

    // Fetch college courses from server
    fetch('ajax/get_colleges_courses.php')
        .then(response => response.json())
        .then(data => {
            collegeCourses = data;
        })
        .catch(error => console.error('Error fetching courses:', error));

    // Handle college change
    const collegeSelect = document.querySelector('select[name="college"]');
    if (collegeSelect) {
        collegeSelect.addEventListener('change', function () {
            const courseSelect = document.getElementById('courseSelect');
            const selectedCollege = this.value;

            // Clear existing options
            courseSelect.innerHTML = '<option value="" disabled selected>Select Course</option>';

            // Populate courses based on selected college
            if (selectedCollege && collegeCourses[selectedCollege]) {
                collegeCourses[selectedCollege].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
            }
        });
    }

    // === Vehicle Section Logic ===
    const addVehicleBtn = document.getElementById('addVehicleBtn');
    if (addVehicleBtn) {
        addVehicleBtn.addEventListener('click', function () {
            const vehicleSections = document.getElementById('vehicle-sections');
            const lastSection = vehicleSections.querySelector('.vehicle-section:last-child');
            const newSection = lastSection.cloneNode(true);

            newSection.querySelectorAll('input, select').forEach(function (el) {
                if (el.type === 'file') {
                    el.value = '';
                } else if (el.tagName.toLowerCase() === 'select') {
                    el.selectedIndex = 0;
                } else {
                    el.value = '';
                }
            });

            newSection.querySelectorAll('.file-name').forEach(fn => fn.remove());
            vehicleSections.appendChild(newSection);
            newSection.querySelectorAll('.upload-area').forEach(initUploadArea);

            // Add delete functionality to the new section
            const deleteBtn = newSection.querySelector('.btn-delete-vehicle');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', handleDeleteVehicle);
            }

            // Show all delete buttons when adding a new section
            updateDeleteButtonVisibility();
        });
    }

    // Handle delete vehicle functionality
    function handleDeleteVehicle() {
        const vehicleSections = document.getElementById('vehicle-sections');
        const sections = vehicleSections.querySelectorAll('.vehicle-section');

        // Don't delete if it's the only section
        if (sections.length <= 1) {
            return;
        }

        // Confirm deletion
        if (confirm('Are you sure you want to remove this vehicle?')) {
            const section = this.closest('.vehicle-section');
            section.classList.add('removing');

            setTimeout(() => {
                section.remove();
                updateDeleteButtonVisibility();
            }, 300);
        }
    }

    // Add delete functionality to existing buttons
    document.querySelectorAll('.btn-delete-vehicle').forEach(btn => {
        btn.addEventListener('click', handleDeleteVehicle);
    });

    // Hide delete buttons if only one vehicle section exists
    function updateDeleteButtonVisibility() {
        const vehicleSections = document.getElementById('vehicle-sections');
        if (!vehicleSections) return;

        const sections = vehicleSections.querySelectorAll('.vehicle-section');
        const deleteButtons = vehicleSections.querySelectorAll('.btn-delete-vehicle');

        if (sections.length <= 1) {
            deleteButtons.forEach(btn => btn.classList.add('hidden'));
        } else {
            deleteButtons.forEach(btn => btn.classList.remove('hidden'));
        }
    }

    // Check button visibility on page load
    updateDeleteButtonVisibility();


    // === Real-time Field Validation (blur events) ===
    const emailInput = document.getElementById('emailInput');
    const emailError = document.getElementById('emailError');
    if (emailInput && emailError) {
        emailInput.addEventListener('blur', function () {
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRe.test(this.value.trim())) {
                emailError.textContent = 'Invalid email address. Please enter a valid email.';
                emailError.classList.remove('hidden');
            } else {
                emailError.classList.add('hidden');
            }
        });
        emailInput.addEventListener('input', function () {
            if (emailError && !emailError.classList.contains('hidden')) {
                emailError.classList.add('hidden');
            }
        });
    }

    const contactInput = document.getElementById('contactNumInput');
    const contactNumError = document.getElementById('contactNumError');
    if (contactInput && contactNumError) {
        contactInput.addEventListener('blur', function () {
            const contact = this.value.trim().replace(/\s/g, '');
            if (this.value && (contact.length < 10 || contact.length > 15 || !/^[0-9+\-]+$/.test(contact))) {
                contactNumError.textContent = 'Invalid phone number. Use 10-15 digits.';
                contactNumError.classList.remove('hidden');
            } else {
                contactNumError.classList.add('hidden');
            }
        });
        contactInput.addEventListener('input', function () {
            if (contactNumError && !contactNumError.classList.contains('hidden')) {
                contactNumError.classList.add('hidden');
            }
        });
    }

    // === Form Submission Logic ===
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const emailError = document.getElementById('emailError');
            const contactNumError = document.getElementById('contactNumError');
            if (emailError) emailError.classList.add('hidden');
            if (contactNumError) contactNumError.classList.add('hidden');

            // Email format validation (visible message containing "invalid")
            const emailInput = document.getElementById('emailInput');
            if (emailInput && emailInput.value) {
                const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRe.test(emailInput.value.trim())) {
                    if (emailError) {
                        emailError.textContent = 'Invalid email address. Please enter a valid email.';
                        emailError.classList.remove('hidden');
                    }
                    return;
                }
            }

            // Phone format validation (visible message containing "invalid" and "Phone" or "phone")
            const contactInput = document.getElementById('contactNumInput');
            if (contactInput && contactInput.value) {
                const contact = contactInput.value.trim().replace(/\s/g, '');
                if (contact.length < 10 || contact.length > 15 || !/^[0-9+\-]+$/.test(contact)) {
                    if (contactNumError) {
                        contactNumError.textContent = 'Invalid phone number. Use 10-15 digits.';
                        contactNumError.classList.remove('hidden');
                    }
                    return;
                }
            }

            // Validate form
            const requiredFields = this.querySelectorAll('[required]');
            let allValid = true;

            requiredFields.forEach(field => {
                // Skip validation for disabled fields
                if (field.disabled) {
                    return;
                }
                if (!field.value) {
                    allValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });

            // Validate additional driver fields
            const additionalDriverName = document.querySelector('input[name="additionalDriverName"]');
            const additionalDriverRelationship = document.querySelector('select[name="additionalDriverRelationship"]');

            if (additionalDriverName && additionalDriverRelationship) {
                if (additionalDriverName.value && !additionalDriverRelationship.value) {
                    allValid = false;
                    additionalDriverRelationship.style.borderColor = 'red';
                    alert('Please select the relationship for the additional driver');
                    return;
                }
            }

            if (!allValid) {
                alert('Please fill in all required fields');
                return;
            }

            // Check if terms are agreed
            const termsCheckbox = document.getElementById('termsCheckbox');
            if (termsCheckbox && (!termsCheckbox.value || termsCheckbox.value !== "1")) {
                alert('Please read and agree to the terms and conditions by clicking the link.');
                return;
            }

            // Submit form with AJAX
            const formData = new FormData(this);

            // Enable disabled fields before form submission data collection
            // Note: FormData handles this, but disabled fields are typically not included.
            // The original code manually accumulated data from disabled fields by temporarily enabling them or checking them.
            // Let's replicate the logic of enabling content for FormData capture.

            const disabledSelects = this.querySelectorAll('select[name="numWheels[]"][disabled]');
            const disabledInputs = this.querySelectorAll('input[name="cubicCapacity[]"][disabled]');

            disabledSelects.forEach(select => select.disabled = false);
            disabledInputs.forEach(input => input.disabled = false);

            // Re-create FormData to include previously disabled fields
            const finalFormData = new FormData(this);

            // Re-disable the fields
            disabledSelects.forEach(select => select.disabled = true);
            disabledInputs.forEach(input => input.disabled = true);

            // Set null values for hidden fields if faculty or non-teaching
            const selectedUserType = document.querySelector('input[name="userType"]:checked')?.value;
            if (selectedUserType === 'faculty' || selectedUserType === 'non-teaching') {
                finalFormData.set('course', '');
                finalFormData.set('academicYear', '');
                finalFormData.set('yearLevel', '');
                finalFormData.set('section', '');
            }

            fetch('process_registration.php', {
                method: 'POST',
                body: finalFormData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success popup
                        const successPopup = document.getElementById('successPopup');
                        if (successPopup) successPopup.style.display = 'flex';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the form');
                });
        });
    }

    // === Popup & Modal Logic ===

    // Terms modal functions
    const termsLink = document.getElementById('termsLink');
    if (termsLink) {
        termsLink.addEventListener('click', function (e) {
            e.preventDefault();
            const termsModal = document.getElementById('termsModal');
            if (termsModal) termsModal.style.display = 'flex';
        });
    }

    window.closeTermsModal = function () {
        const termsModal = document.getElementById('termsModal');
        if (termsModal) termsModal.style.display = 'none';

        const modalTermsCheckbox = document.getElementById('modalTermsCheckbox');
        if (modalTermsCheckbox) {
            modalTermsCheckbox.checked = false;
            updateCheckboxStyle(modalTermsCheckbox);
        }
    };

    window.updateCheckboxStyle = function (checkbox) {
        // This function is for the modal checkbox
        const agreeBtn = document.getElementById('termsAgreeBtn');

        if (checkbox.checked) {
            checkbox.classList.add('checked'); // Visual style if needed
            if (agreeBtn) {
                agreeBtn.disabled = false;
                agreeBtn.classList.add('active');
            }
        } else {
            checkbox.classList.remove('checked');
            if (agreeBtn) {
                agreeBtn.disabled = true;
                agreeBtn.classList.remove('active');
            }
        }
    };

    window.agreeToTerms = function () {
        const modalCheckbox = document.getElementById('modalTermsCheckbox');
        const mainCheckbox = document.getElementById('termsCheckbox');
        const termsStatusText = document.getElementById('termsStatusText');
        const termsAcceptedIcon = document.getElementById('termsAcceptedIcon');
        const termsLink = document.getElementById('termsLink');

        if (modalCheckbox.checked) {
            if (mainCheckbox) {
                mainCheckbox.value = "1"; // Set hidden input value
                // Since it's hidden, we don't 'check' it visually, but we update the UI
            }

            if (termsStatusText) termsStatusText.style.display = 'none';
            if (termsLink) termsLink.style.display = 'none'; // Optional: hide link after acceptance? Or keep it?
            // User said "get rid of the check box outside the underlined words", implying the text might stay?
            // "so that they need to read the terms to check the checkbox" -> checking happens in modal.
            // Let's hide the "Please read" and show "Accepted"

            if (termsAcceptedIcon) {
                termsAcceptedIcon.classList.remove('hidden');
                termsAcceptedIcon.style.display = 'inline-flex';
            }

            closeTermsModal();
        }
    };

    window.closePopup = function () {
        const successPopup = document.getElementById('successPopup');
        if (successPopup) successPopup.style.display = 'none';
        location.reload();
    };

    // === Vehicle Type Change Logic ===
    function handleVehicleTypeChange(selectElement) {
        const vehicleSection = selectElement.closest('.vehicle-section');
        const numWheelsSelect = vehicleSection.querySelector('.num-wheels-select');
        const numWheelsInput = vehicleSection.querySelector('.num-wheels-input');
        const cubicCapacityInput = vehicleSection.querySelector('input[name="cubicCapacity[]"]');
        const otherTypeInput = vehicleSection.querySelector('input[name="otherVehicleType[]"]');

        if (selectElement.value === 'Car') {
            // Show Select, Hide Input
            numWheelsSelect.classList.remove('hidden');
            numWheelsSelect.disabled = true; // Still disabled as it is auto-set
            numWheelsSelect.value = '4';

            numWheelsInput.classList.add('hidden');
            numWheelsInput.disabled = true;
            numWheelsInput.value = '';

            cubicCapacityInput.value = '';
            cubicCapacityInput.disabled = true;
            cubicCapacityInput.required = false;
            cubicCapacityInput.placeholder = "Not applicable";

            if (otherTypeInput) {
                otherTypeInput.classList.add('hidden');
                otherTypeInput.disabled = true;
                otherTypeInput.required = false;
                otherTypeInput.value = '';
            }
        } else if (selectElement.value === 'Motorcycle') {
            // Show Select, Hide Input
            numWheelsSelect.classList.remove('hidden');
            numWheelsSelect.disabled = true;
            numWheelsSelect.value = '2';

            numWheelsInput.classList.add('hidden');
            numWheelsInput.disabled = true;
            numWheelsInput.value = '';

            cubicCapacityInput.disabled = false;
            cubicCapacityInput.required = true;
            cubicCapacityInput.placeholder = "Enter cubic capacity (cc)";

            if (otherTypeInput) {
                otherTypeInput.classList.add('hidden');
                otherTypeInput.disabled = true;
                otherTypeInput.required = false;
                otherTypeInput.value = '';
            }
        } else if (selectElement.value === 'Other') {
            // Hide Select, Show Input
            numWheelsSelect.classList.add('hidden');
            numWheelsSelect.disabled = true;
            numWheelsSelect.value = '';

            numWheelsInput.classList.remove('hidden');
            numWheelsInput.disabled = false;
            numWheelsInput.required = true;
            numWheelsInput.value = '';

            // Enable cubic capacity (optional)
            cubicCapacityInput.disabled = false;
            cubicCapacityInput.required = false;
            cubicCapacityInput.placeholder = "Enter cubic capacity (if applicable)";

            // Show Other Type Input
            if (otherTypeInput) {
                otherTypeInput.classList.remove('hidden');
                otherTypeInput.disabled = false;
                otherTypeInput.required = true;
                otherTypeInput.focus();
            }
        }
    }

    // Bind existing selects
    document.querySelectorAll('select[name="vehicleType[]"]').forEach(select => {
        select.addEventListener('change', function () {
            handleVehicleTypeChange(this);
        });
    });

    // Delegate change event for dynamically added sections
    document.getElementById('vehicle-sections').addEventListener('change', function (e) {
        if (e.target.name === 'vehicleType[]') {
            handleVehicleTypeChange(e.target);
        }
    });

    // === Additional Driver Logic ===
    const addDriverBtn = document.getElementById('addDriverBtn');
    const removeDriverBtn = document.getElementById('removeDriverBtn');
    const additionalDriverSection = document.getElementById('additionalDriverSection');
    const additionalDriverNameInput = document.getElementById('additionalDriverName');
    const additionalDriverRelSelect = document.getElementById('additionalDriverRelationship');

    if (addDriverBtn && additionalDriverSection) {
        addDriverBtn.addEventListener('click', function () {
            additionalDriverSection.classList.remove('hidden');
            addDriverBtn.classList.add('hidden');

            // Make fields required when shown? Or keep optional?
            // User request implies if they add it, they should fill it.
            // But originally it was optional. Let's make it intuitive: if open and empty on submit, validation might catch it if we add required.
            // For now, let's just show/hide.
        });
    }

    if (removeDriverBtn && additionalDriverSection) {
        removeDriverBtn.addEventListener('click', function () {
            additionalDriverSection.classList.add('hidden');
            addDriverBtn.classList.remove('hidden');

            // Clear inputs
            if (additionalDriverNameInput) additionalDriverNameInput.value = '';
            if (additionalDriverRelSelect) additionalDriverRelSelect.value = '';
        });
    }

});
