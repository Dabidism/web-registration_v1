// Loading Animation for Application Processing
class LoadingAnimation {
    constructor() {
        this.overlay = null;
        this.init();
    }

    init() {
        this.createOverlay();
        this.bindEvents();
    }

    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.className = 'loading-overlay';
        this.overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="status-icon success">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.801 10A10 10 0 1 1 17 3.335"/>
                        <path d="m9 11 3 3L22 4"/>
                    </svg>
                </div>
                <div class="status-icon error">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m15 9-6 6"/>
                        <path d="m9 9 6 6"/>
                    </svg>
                </div>
                <div class="loading-text">Processing Application...</div>
                <div class="loading-subtext">Please wait while we process your request</div>
            </div>
        `;
        document.body.appendChild(this.overlay);
    }

    bindEvents() {
        const form = document.getElementById('reviewForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                const action = e.submitter?.value || document.querySelector('input[name="action"]')?.value;
                if (action) {
                    this.show(action);
                }
            });
        }
        
        // Make instance globally available
        window.loadingAnimation = this;
    }

    show(action = 'approve') {
        const content = this.overlay.querySelector('.loading-content');
        const text = this.overlay.querySelector('.loading-text');
        const subtext = this.overlay.querySelector('.loading-subtext');
        
        // Reset states
        content.className = 'loading-content';
        
        if (action === 'approve') {
            text.textContent = 'Approving Application...';
            subtext.textContent = 'Transferring data and sending notification email';
        } else {
            text.textContent = 'Processing Rejection...';
            subtext.textContent = 'Updating status and sending notification email';
        }
        
        this.overlay.style.display = 'flex';
        
        // Auto-complete after form submission
        setTimeout(() => {
            this.showResult(action === 'approve' ? 'success' : 'error', action);
        }, 2000);
    }

    showResult(type, action) {
        const content = this.overlay.querySelector('.loading-content');
        const text = this.overlay.querySelector('.loading-text');
        const subtext = this.overlay.querySelector('.loading-subtext');
        
        content.className = `loading-content ${type}`;
        
        if (action === 'approve') {
            text.textContent = 'Application Approved!';
            subtext.textContent = 'Email notification sent successfully';
        } else {
            text.textContent = 'Application Rejected';
            subtext.textContent = 'Email notification sent successfully';
        }
        
        setTimeout(() => {
            this.hide();
        }, 2000);
    }

    hide() {
        this.overlay.style.display = 'none';
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LoadingAnimation();
});