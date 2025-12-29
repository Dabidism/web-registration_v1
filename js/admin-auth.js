// Admin Authentication Modal
class AdminAuth {
    constructor() {
        this.createModal();
        this.currentAction = null;
        this.currentData = null;
    }

    createModal() {
        const modalHTML = `
            <div id="adminAuthModal" class="modal">
                <div class="modal-content small">
                    <div class="modal-header">
                        <svg class="text-gray" style="margin-right:10px;color:#f59e0b;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <h3 class="text-red" style="margin:0;">Admin Verification Required</h3>
                    </div>
                    <p class="mb-4 text-gray">Please enter your admin password to confirm this action:</p>
                    <input type="password" id="adminPassword" placeholder="Enter admin password" class="form-input">
                    <div class="modal-footer">
                        <button id="adminAuthCancel" class="btn btn-secondary">Cancel</button>
                        <button id="adminAuthConfirm" class="btn btn-danger">Confirm</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.bindEvents();
    }

    bindEvents() {
        const modal = document.getElementById('adminAuthModal');
        const cancelBtn = document.getElementById('adminAuthCancel');
        const confirmBtn = document.getElementById('adminAuthConfirm');
        const passwordInput = document.getElementById('adminPassword');

        cancelBtn.addEventListener('click', () => this.close());
        
        confirmBtn.addEventListener('click', () => this.verifyAndExecute());
        
        passwordInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.verifyAndExecute();
        });

        // Close on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) this.close();
        });
    }

    show(action, data = null) {
        console.log('Setting action:', action, typeof action);
        // Store the action function with proper binding
        this.currentAction = typeof action === 'function' ? action : null;
        this.currentData = data;
        document.getElementById('adminPassword').value = '';
        document.getElementById('adminAuthModal').style.display = 'block';
        document.getElementById('adminPassword').focus();
    }

    close() {
        document.getElementById('adminAuthModal').style.display = 'none';
        this.currentAction = null;
        this.currentData = null;
    }

    async verifyAndExecute() {
        const password = document.getElementById('adminPassword').value;
        const confirmBtn = document.getElementById('adminAuthConfirm');
        
        if (!password) {
            alert('Please enter your password');
            return;
        }

        // Disable button to prevent multiple clicks
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Verifying...';

        try {
            console.log('Sending verification request...');
            const response = await fetch('ajax/verify_admin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `password=${encodeURIComponent(password)}`
            });

            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON:', responseText);
                throw new Error('Invalid response format');
            }
            
            console.log('Verification result:', result);
            
            if (result.success) {
                console.log('Password verified, executing action');
                this.close();
                if (this.currentAction && typeof this.currentAction === 'function') {
                    console.log('Calling action function');
                    try {
                        // Execute immediately without setTimeout to avoid scope issues
                        this.currentAction.call(this, this.currentData);
                    } catch (e) {
                        console.error('Error executing action:', e);
                        alert('Error executing action: ' + e.message);
                    }
                } else {
                    console.log('No action function found. Current action:', this.currentAction, typeof this.currentAction);
                    alert('No action function defined');
                }
            } else {
                alert(result.message || 'Invalid password');
                document.getElementById('adminPassword').value = '';
                document.getElementById('adminPassword').focus();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to verify password: ' + error.message);
        } finally {
            // Re-enable button
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm';
        }
    }
}

// Initialize global admin auth instance
window.adminAuth = new AdminAuth();