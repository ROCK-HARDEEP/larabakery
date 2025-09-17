{{-- Custom Filament Layout Enhancements --}}
@vite(['resources/css/filament-fixes.css'])
<style>
/* Additional custom styles to enhance Filament admin panel */
.fi-sidebar-nav-item {
    position: relative;
    overflow: hidden;
}

.fi-sidebar-nav-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(180deg, #f59e0b, #d97706);
    transform: scaleY(0);
    transition: transform 0.2s ease;
    transform-origin: bottom;
}

.fi-sidebar-nav-item.fi-active::before {
    transform: scaleY(1);
}

/* Enhanced Page Header */
.fi-header {
    position: sticky;
    top: 0;
    z-index: 40;
}

.fi-page-heading {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
}

/* Table Row Hover Effects */
.fi-ta-row:hover {
    background: linear-gradient(90deg, rgba(245, 158, 11, 0.05), rgba(245, 158, 11, 0.02));
    transform: scale(1.002);
    transition: all 0.2s ease;
}

/* Form Section Headers */
.fi-section-header {
    position: relative;
}

.fi-section-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #f59e0b, transparent);
}

/* Enhanced Input Focus States */
input:focus, textarea:focus, select:focus {
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
    border-color: #f59e0b !important;
    outline: none;
}

/* Custom Scrollbar for Sidebar */
.fi-sidebar::-webkit-scrollbar {
    width: 6px;
}

.fi-sidebar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.fi-sidebar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #f59e0b, #d97706);
    border-radius: 3px;
}

.fi-sidebar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #d97706, #b45309);
}

/* Loading States */
.fi-loading-overlay {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
}

.dark .fi-loading-overlay {
    background: rgba(0, 0, 0, 0.8);
}

/* Enhanced Notifications */
.fi-no-title {
    font-weight: 600;
    color: #374151;
}

.dark .fi-no-title {
    color: #f9fafb;
}

.fi-no-body {
    color: #6b7280;
}

.dark .fi-no-body {
    color: #d1d5db;
}

/* Custom Badge Styles */
.fi-badge-color-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
}

.fi-badge-color-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
}

.fi-badge-color-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
}

/* Enhanced Form Layout */
.fi-fo-field-wrp {
    position: relative;
}

.fi-fo-field-wrp label {
    font-weight: 500;
    color: #374151;
    transition: color 0.2s ease;
}

.dark .fi-fo-field-wrp label {
    color: #d1d5db;
}

.fi-fo-field-wrp:focus-within label {
    color: #f59e0b;
}

/* Animation for page transitions */
@keyframes pageSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fi-page {
    animation: pageSlideIn 0.4s ease-out;
}

/* Enhanced Modal Styling */
.fi-modal-window {
    border: none;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(245, 158, 11, 0.1);
}

.fi-modal-header {
    border-bottom: 1px solid rgba(245, 158, 11, 0.1);
}

.fi-modal-heading {
    color: #f59e0b;
    font-weight: 600;
}

/* Status Indicators */
.fi-status-online {
    background: linear-gradient(135deg, #10b981, #059669);
}

.fi-status-offline {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.fi-status-busy {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

/* Enhanced Charts and Widgets */
.fi-wi-stats-overview-stat-chart {
    filter: drop-shadow(0 4px 6px rgba(245, 158, 11, 0.1));
}

.fi-wi-stats-overview-stat-description {
    color: #6b7280;
    font-size: 0.875rem;
}

.dark .fi-wi-stats-overview-stat-description {
    color: #9ca3af;
}
</style>

{{-- Enhanced JavaScript for better interactions --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add subtle animations to buttons
    document.querySelectorAll('.fi-btn').forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px) scale(1.02)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Add loading state to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.style.opacity = '0.7';
                submitBtn.style.pointerEvents = 'none';
                
                // Reset after 3 seconds (fallback)
                setTimeout(() => {
                    submitBtn.style.opacity = '1';
                    submitBtn.style.pointerEvents = 'auto';
                }, 3000);
            }
        });
    });
    
    // Enhanced sidebar interactions
    const sidebarItems = document.querySelectorAll('.fi-sidebar-nav-item');
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(245, 158, 11, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// CSS animation for ripple effect
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>