document.addEventListener('DOMContentLoaded', () => {
    const addCollegeBtn = document.getElementById('addCollegeBtn');
    const addCollegeModal = document.getElementById('addCollegeModal');
    const closeBtns = document.querySelectorAll('.close');
    const saveAddCollege = document.getElementById('saveAddCollege');
    const addCollegeMessage = document.getElementById('addCollegeMessage');
    
    // Setup generic modal close
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // Add College
    if (addCollegeBtn) {
        addCollegeBtn.addEventListener('click', () => {
            document.getElementById('addCollegeForm').reset();
            addCollegeMessage.classList.add('hidden');
            addCollegeModal.style.display = 'block';
        });
    }

    // Manage Courses logic
    const manageCoursesBtns = document.querySelectorAll('.btn-courses');
    const manageCoursesModal = document.getElementById('manageCoursesModal');
    const manageCoursesTitle = document.getElementById('manageCoursesTitle');
    const manageCollegeID = document.getElementById('manageCollegeID');
    const existingCoursesList = document.getElementById('existingCoursesList');
    const addNewCourseBtn = document.getElementById('addNewCourseBtn');
    const newCourseNameInput = document.getElementById('newCourseName');
    const manageCoursesMessage = document.getElementById('manageCoursesMessage');

    manageCoursesBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const collegeId = btn.getAttribute('data-id');
            const collegeName = btn.getAttribute('data-name');
            
            manageCoursesTitle.textContent = `Manage Courses - ${collegeName}`;
            manageCollegeID.value = collegeId;
            newCourseNameInput.value = '';
            manageCoursesMessage.classList.add('hidden');
            
            await loadExistingCourses(collegeId);
            manageCoursesModal.style.display = 'block';
        });
    });

    async function loadExistingCourses(collegeId) {
        try {
            existingCoursesList.innerHTML = '<p style="color: #6b7280; font-style: italic;">Loading courses...</p>';
            
            const formData = new FormData();
            formData.append('college_id', collegeId);
            
            let response = await fetch('ajax/get_college_courses_admin.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                // if it fails or doesn't return exactly ok, could be empty
                existingCoursesList.innerHTML = '<p style="color: #6b7280;">Error loading courses.</p>';
                return;
            }
            
            let data = await response.json();
            
            if (data.success) {
                if (data.courses && data.courses.length > 0) {
                    let html = '';
                    data.courses.forEach(course => {
                        html += `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; border-bottom: 1px solid #e5e7eb;">
                                <span>${course.name}</span>
                                <button class="btn-delete-course" data-id="${course.id}" style="color: #dc2626; background: none; border: none; cursor: pointer; font-size: 16px;">&times;</button>
                            </div>
                        `;
                    });
                    existingCoursesList.innerHTML = html;
                    
                    // Attach delete listeners
                    document.querySelectorAll('.btn-delete-course').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            const courseId = e.target.getAttribute('data-id');
                            if(confirm('Are you sure you want to delete this course?')) {
                                await deleteCourse(courseId, collegeId);
                            }
                        });
                    });
                } else {
                    existingCoursesList.innerHTML = '<p style="color: #6b7280; font-style: italic;">No courses found for this college.</p>';
                }
            } else {
                existingCoursesList.innerHTML = `<p style="color: #dc2626;">${data.message || 'Failed to load courses.'}</p>`;
            }
        } catch (error) {
            console.error('Error fetching courses:', error);
            existingCoursesList.innerHTML = '<p style="color: #6b7280;">Error loading courses.</p>';
        }
    }

    if (addNewCourseBtn) {
        addNewCourseBtn.addEventListener('click', async () => {
            const courseName = newCourseNameInput.value.trim();
            const collegeId = manageCollegeID.value;
            
            if (!courseName) {
                showMessage(manageCoursesMessage, 'Please enter a course name.', 'error');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('college_id', collegeId);
                formData.append('name', courseName);
                
                let response = await fetch('ajax/add_college_course.php', {
                    method: 'POST',
                    body: formData
                });
                
                let result = await response.json();
                
                if (result.success) {
                    newCourseNameInput.value = '';
                    showMessage(manageCoursesMessage, 'Course added successfully.', 'success');
                    await loadExistingCourses(collegeId);
                    
                    // Optional: reload the page to update the main table list after 1.5s
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(manageCoursesMessage, result.message || 'Failed to add course.', 'error');
                }
            } catch (error) {
                showMessage(manageCoursesMessage, 'Error adding course.', 'error');
            }
        });
    }

    async function deleteCourse(courseId, collegeId) {
        try {
            const formData = new FormData();
            formData.append('course_id', courseId);
            
            let response = await fetch('ajax/delete_college_course.php', {
                method: 'POST',
                body: formData
            });
            
            let result = await response.json();
            
            if (result.success) {
                showMessage(manageCoursesMessage, 'Course deleted successfully.', 'success');
                await loadExistingCourses(collegeId);
                setTimeout(() => location.reload(), 1500);
            } else {
                showMessage(manageCoursesMessage, result.message || 'Failed to delete course.', 'error');
            }
        } catch (error) {
            showMessage(manageCoursesMessage, 'Error deleting course.', 'error');
        }
    }

    // Dynamic Course Inputs in Add College modal
    const dynamicCoursesContainer = document.getElementById('dynamicCoursesContainer');
    const addAnotherCourseBtn = document.getElementById('addAnotherCourseBtn');

    if (addAnotherCourseBtn) {
        addAnotherCourseBtn.addEventListener('click', () => {
            const courseGroup = document.createElement('div');
            courseGroup.className = 'form-group course-input-group';
            courseGroup.style.display = 'flex';
            courseGroup.style.gap = '10px';
            courseGroup.style.marginBottom = '10px';
            
            courseGroup.innerHTML = `
                <input type="text" name="courses[]" placeholder="Course Name (e.g. BS in IT)" class="course-input" required style="flex: 1;">
                <button type="button" class="btn-remove-course" style="background-color: #ef4444; color: white; border: none; padding: 0 10px; border-radius: 4px; cursor: pointer;">&times;</button>
            `;
            
            dynamicCoursesContainer.appendChild(courseGroup);

            // Add remove event loader
            courseGroup.querySelector('.btn-remove-course').addEventListener('click', function() {
                courseGroup.remove();
            });
        });
    }

    if (saveAddCollege) {
        saveAddCollege.addEventListener('click', async () => {
            const code = document.getElementById('addCollegeCode').value.trim();
            const name = document.getElementById('addCollegeName').value.trim();
            const adminPassword = document.getElementById('addCollegeAdminPassword').value;

            // Collect courses
            const courseInputs = document.querySelectorAll('.course-input');
            let courses = [];
            let hasEmptyCourse = false;
            
            courseInputs.forEach(input => {
                const val = input.value.trim();
                if (val) {
                    courses.push(val);
                } else {
                    hasEmptyCourse = true;
                }
            });

            if (!code || !name || !adminPassword || hasEmptyCourse) {
                showMessage(addCollegeMessage, 'Please fill in all fields (including courses).', 'error');
                return;
            }

            try {
                // Verify admin first
                const verifyFormData = new FormData();
                verifyFormData.append('password', adminPassword);
                
                let verifyResponse = await fetch('ajax/verify_admin.php', {
                    method: 'POST',
                    body: verifyFormData
                });
                let verifyData = await verifyResponse.json();

                if (!verifyData.success) {
                    showMessage(addCollegeMessage, 'Incorrect admin password.', 'error');
                    return;
                }

                // Add College
                const addData = new FormData();
                addData.append('code', code);
                addData.append('name', name);
                addData.append('courses', JSON.stringify(courses)); // Send as JSON string

                let addResponse = await fetch('ajax/add_college.php', {
                    method: 'POST',
                    body: addData
                });
                let addResult = await addResponse.json();

                if (addResult.success) {
                    showMessage(addCollegeMessage, 'College added successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(addCollegeMessage, addResult.message, 'error');
                }

            } catch (error) {
                showMessage(addCollegeMessage, 'Error adding college.', 'error');
            }
        });
    }

    // Toggle Status College
    // Fix: Exclude the confirm button itself which might share the btn-toggle-status class for styling
    const toggleBtns = document.querySelectorAll('.btn-toggle-status:not(#confirmToggleCollegeBtn)');
    const toggleModal = document.getElementById('toggleCollegeModal');
    const cancelToggleBtn = document.getElementById('cancelToggleCollegeBtn');
    const confirmToggleCollegeBtn = document.getElementById('confirmToggleCollegeBtn');

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('toggleCollegeID').value = btn.getAttribute('data-id');
            document.getElementById('toggleCollegeCurrent').value = btn.getAttribute('data-status') || '1';
            document.getElementById('toggleCollegeAdminPassword').value = '';
            toggleModal.style.display = 'block';
        });
    });

    if (cancelToggleBtn) {
        cancelToggleBtn.addEventListener('click', () => {
            toggleModal.style.display = 'none';
        });
    }

    if (confirmToggleCollegeBtn) {
        confirmToggleCollegeBtn.addEventListener('click', async () => {
            const id = document.getElementById('toggleCollegeID').value;
            const currentStatus = document.getElementById('toggleCollegeCurrent').value;
            const adminPassword = document.getElementById('toggleCollegeAdminPassword').value;

            // Determine new status: if current is 1, new is 0, else 1
            const newStatus = (currentStatus == '1') ? '0' : '1';

            if (!adminPassword) {
                alert('Please enter your admin password.');
                return;
            }

            try {
                // Verify admin first
                const verifyFormData = new FormData();
                verifyFormData.append('password', adminPassword);
                
                let verifyResponse = await fetch('ajax/verify_admin.php', {
                    method: 'POST',
                    body: verifyFormData
                });
                let verifyData = await verifyResponse.json();

                if (!verifyData.success) {
                    alert('Incorrect admin password.');
                    return;
                }

                // Toggle College Status
                const toggleData = new FormData();
                toggleData.append('id', id);
                toggleData.append('status', newStatus);

                let toggleResponse = await fetch('ajax/toggle_college_status.php', {
                    method: 'POST',
                    body: toggleData
                });
                let toggleResult = await toggleResponse.json();

                if (toggleResult.success) {
                    location.reload();
                } else {
                    alert(toggleResult.message);
                }
            } catch (error) {
                alert('Error changing college status.');
            }
        });
    }

    // Search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                if(row.querySelector('.no-data')) return;
                
                const text = row.textContent.toLowerCase();
                if (text.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    function showMessage(element, text, type) {
        element.textContent = text;
        element.className = `form-message ${type}`;
    }
});
