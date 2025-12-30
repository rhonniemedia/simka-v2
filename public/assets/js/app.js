// ============================================
// GLOBAL HTMX + ALPINE.JS UTILITIES
// ============================================

/**
 * Global Pagination Handler with Event Delegation
 * Works with any paginated table that uses Alpine.js
 */
document.addEventListener('click', function(e) {
    // Handle pagination links
    if (e.target.matches('.pagination a, .pagination a *')) {
        const link = e.target.closest('a');
        if (!link || link.getAttribute('href') === '#') return;
        
        e.preventDefault();
        
        const url = new URL(link.href);
        const page = url.searchParams.get('page');
        
        // Find closest Alpine component
        const wrapper = link.closest('[x-data]');
        if (wrapper && window.Alpine) {
            const app = Alpine.$data(wrapper);
            if (app && typeof app.loadData === 'function') {
                app.currentPage = page;
                app.loadData(true);
            } else if (app && typeof app.loadProducts === 'function') {
                app.currentPage = page;
                app.loadProducts(true);
            }
        }
    }
});

/**
 * Global HTMX Loading Indicator Handler
 */
document.body.addEventListener('htmx:afterSettle', (e) => {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.classList.remove('show-loading');
    }
});

/**
 * Global CSRF Token for HTMX
 */
document.body.addEventListener('htmx:configRequest', (e) => {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        e.detail.headers['X-CSRF-TOKEN'] = token.content;
    }
});

/**
 * Global Alert Handler
 */
document.body.addEventListener('showAlert', (e) => {
    const alertData = e.detail;
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: alertData.icon || 'info',
            title: alertData.title || 'Notification',
            text: alertData.text || '',
            confirmButtonColor: '#0d6efd',
            timer: alertData.timer || 3000
        });
    }
});

/**
 * Helper: Initialize Bootstrap Modal
 */
window.initModal = function(modalId = 'mainModal') {
    const modalEl = document.getElementById(modalId);
    if (modalEl && typeof bootstrap !== 'undefined') {
        return new bootstrap.Modal(modalEl);
    }
    return null;
};

/**
 * Helper: Global Confirm Delete
 */
window.confirmDelete = function(id, name, url, refreshCallback) {
    if (typeof Swal === 'undefined') {
        if (confirm(`Yakin hapus ${name}?`)) {
            htmx.ajax('DELETE', url, { target: 'body', swap: 'none' });
        }
        return;
    }

    Swal.fire({
        title: 'Hapus?',
        text: `Yakin hapus ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            htmx.ajax('DELETE', url, {
                target: 'body',
                swap: 'none'
            });
            if (typeof refreshCallback === 'function') {
                setTimeout(refreshCallback, 300);
            }
        }
    });
};

/**
 * Helper: Show Loading
 */
window.showLoading = function() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.classList.add('show-loading');
    }
};

/**
 * Helper: Hide Loading
 */
window.hideLoading = function() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.classList.remove('show-loading');
    }
};

console.log('✅ Global HTMX + Alpine utilities loaded');