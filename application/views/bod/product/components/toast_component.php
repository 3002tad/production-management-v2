<!-- 
╔══════════════════════════════════════════════════════════════════════════════╗
║  Toast Notification Component - Material Design 3.0                          ║
║  Usage: Include trong views cần toast messages                               ║
╚══════════════════════════════════════════════════════════════════════════════╝

FEATURES:
- ✅ Gradient backgrounds (success, warning, error)
- ✅ Auto-hide với progress bar
- ✅ Refresh-proof (URL parameter + sessionStorage)
- ✅ Slide-in/out animations
- ✅ Material Icons
-->

<style>
/* Toast Container */
.toast-container {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
}

/* Toast Base */
.custom-toast {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    position: relative;
    overflow: hidden;
    animation: slideInRight 0.4s ease-out;
    font-family: 'Poppins', sans-serif;
}

/* Toast Variants */
.custom-toast.toast-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.custom-toast.toast-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.custom-toast.toast-error {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

/* Toast Content */
.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toast-icon {
    font-size: 24px;
    opacity: 0.9;
}

.toast-message {
    flex: 1;
    font-size: 14px;
    line-height: 1.5;
}

.toast-close {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.8;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-close:hover {
    opacity: 1;
}

/* Progress Bar */
.toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: rgba(255, 255, 255, 0.3);
    animation: progress linear forwards;
}

/* Animations */
@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}

@keyframes progress {
    from {
        width: 100%;
    }
    to {
        width: 0;
    }
}
</style>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
/**
 * Show Toast Notification
 * 
 * @param {string} type - success, warning, error
 * @param {string} message - Toast message
 * @param {number} duration - Duration in milliseconds (default: auto)
 */
function showToast(type, message, duration = null) {
    // Auto duration based on type
    if (!duration) {
        duration = type === 'success' ? 3000 : (type === 'warning' ? 5000 : 6000);
    }
    
    // Icon map
    const icons = {
        'success': 'check_circle',
        'warning': 'warning',
        'error': 'error'
    };
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `custom-toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="material-icons-round toast-icon">${icons[type] || 'info'}</i>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                <i class="material-icons-round">close</i>
            </button>
        </div>
        <div class="toast-progress" style="animation-duration: ${duration}ms;"></div>
    `;
    
    // Add to container
    const container = document.getElementById('toastContainer');
    container.appendChild(toast);
    
    // Auto remove after duration
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.4s ease-out forwards';
        setTimeout(() => toast.remove(), 400);
    }, duration);
}

/**
 * Show toast from URL parameter + sessionStorage (refresh-proof)
 * 
 * Usage: redirect('controller/method?msg=success&text=Lưu thành công!');
 */
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasMsg = urlParams.has('msg');
    const storageKey = 'toast_shown_' + window.location.pathname;
    const toastShown = sessionStorage.getItem(storageKey);
    
    if (hasMsg && !toastShown) {
        const type = urlParams.get('msg'); // success, warning, error
        const text = urlParams.get('text') || 'Thao tác thành công!';
        
        showToast(type, decodeURIComponent(text));
        
        // Mark as shown
        sessionStorage.setItem(storageKey, 'true');
        
        // Clean URL
        window.history.replaceState({}, '', window.location.pathname);
    }
});

// Clear toast flag on navigation
window.addEventListener('beforeunload', function() {
    sessionStorage.removeItem('toast_shown_' + window.location.pathname);
});
</script>
