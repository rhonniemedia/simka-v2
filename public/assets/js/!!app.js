// ============================================
// GLOBAL HTMX + ALPINE.JS UTILITIES
// Production Ready - Universal untuk Semua Module
// ============================================

/**
 * Alpine.js Global Store - Centralized State Management
 */
document.addEventListener('alpine:init', () => {
    Alpine.store('app', {
        modals: {},
        loading: false,
        
        /**
         * Initialize Bootstrap Modal
         * @param {string} id - Modal ID
         * @returns {bootstrap.Modal|null}
         */
        initModal(id) {
            try {
                if (!this.modals[id]) {
                    const modalEl = document.getElementById(id);
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        this.modals[id] = new bootstrap.Modal(modalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        
                        // Cleanup on modal hidden
                        modalEl.addEventListener('hidden.bs.modal', () => {
                            const modalContent = modalEl.querySelector('[id$="-content"]');
                            if (modalContent) {
                                // Clear content untuk prevent memory leak
                                setTimeout(() => {
                                    modalContent.innerHTML = '';
                                }, 300);
                            }
                        });
                    }
                }
                return this.modals[id] || null;
            } catch (error) {
                console.error(`❌ Error initializing modal ${id}:`, error);
                return null;
            }
        },
        
        /**
         * Show Modal
         * @param {string} id - Modal ID
         */
        showModal(id) {
            try {
                const modal = this.initModal(id);
                if (modal) {
                    modal.show();
                    console.log(`🔓 Modal ${id} opened`);
                } else {
                    console.warn(`⚠️ Modal ${id} not found`);
                }
            } catch (error) {
                console.error(`❌ Error showing modal ${id}:`, error);
            }
        },
        
        /**
         * Hide Modal
         * @param {string} id - Modal ID
         */
        hideModal(id) {
            try {
                if (this.modals[id]) {
                    this.modals[id].hide();
                    console.log(`🔒 Modal ${id} closed`);
                }
            } catch (error) {
                console.error(`❌ Error hiding modal ${id}:`, error);
            }
        },
        
        /**
         * Hide All Modals
         */
        hideAllModals() {
            try {
                Object.keys(this.modals).forEach(key => {
                    if (this.modals[key]) {
                        this.modals[key].hide();
                    }
                });
                console.log('🔒 All modals closed');
            } catch (error) {
                console.error('❌ Error hiding all modals:', error);
            }
        },
        
        /**
         * Show Loading Indicator
         */
        showLoading() {
            this.loading = true;
            const loadingEl = document.getElementById('loading');
            if (loadingEl) {
                loadingEl.classList.add('show-loading');
            }
        },
        
        /**
         * Hide Loading Indicator
         */
        hideLoading() {
            this.loading = false;
            const loadingEl = document.getElementById('loading');
            if (loadingEl) {
                loadingEl.classList.remove('show-loading');
            }
        }
    });
});

/**
 * ============================================
 * HTMX GLOBAL EVENT HANDLERS
 * ============================================
 */

/**
 * Auto-show modal after HTMX loads content
 */
document.body.addEventListener('htmx:afterSwap', (e) => {
    try {
        const targetId = e.detail.target.id;
        console.log('🔄 HTMX afterSwap:', targetId);
        
        // Pattern: {modalId}-content -> show {modalId}
        if (targetId.endsWith('-content')) {
            const modalId = targetId.replace('-content', '');
            
            // Small delay to ensure DOM is ready
            setTimeout(() => {
                Alpine.store('app').showModal(modalId);
            }, 50);
        }
        
        // Re-initialize Alpine components if any
        if (window.Alpine && typeof Alpine.initTree === 'function') {
            Alpine.initTree(e.detail.target);
        }
    } catch (error) {
        console.error('❌ Error in htmx:afterSwap:', error);
    }
});

/**
 * Handle server responses before swapping
 */
document.body.addEventListener('htmx:beforeSwap', (e) => {
    try {
        const xhr = e.detail.xhr;
        const status = xhr.status;
        
        console.log(`📡 Server response: ${status}`);
        
        // Handle 204 No Content (success without body)
        if (status === 204) {
            const triggerHeader = xhr.getResponseHeader('HX-Trigger');
            
            if (triggerHeader) {
                console.log('📬 HX-Trigger received:', triggerHeader);
                
                // Hide all modals first
                Alpine.store('app').hideAllModals();
                
                try {
                    // Try to parse as JSON (for showAlert)
                    const triggerData = JSON.parse(triggerHeader);
                    
                    // Handle each trigger
                    Object.keys(triggerData).forEach(eventName => {
                        if (eventName === 'showAlert') {
                            // Dispatch showAlert event
                            document.body.dispatchEvent(new CustomEvent('showAlert', {
                                detail: triggerData.showAlert,
                                bubbles: true
                            }));
                        } else {
                            // Dispatch custom event (e.g., pensiunSaved, pensiunUpdated)
                            document.body.dispatchEvent(new CustomEvent(eventName, {
                                detail: triggerData[eventName],
                                bubbles: true
                            }));
                            console.log(`✅ Event triggered: ${eventName}`);
                        }
                    });
                } catch (parseError) {
                    // Not JSON, treat as comma-separated event names
                    const events = triggerHeader.split(',').map(t => t.trim());
                    events.forEach(eventName => {
                        if (eventName) {
                            document.body.dispatchEvent(new CustomEvent(eventName, {
                                bubbles: true
                            }));
                            console.log(`✅ Event triggered: ${eventName}`);
                        }
                    });
                }
            }
            
            // Prevent HTMX from trying to swap empty content
            e.detail.shouldSwap = false;
        }
        
        // Handle validation errors (422 Unprocessable Entity)
        if (status === 422) {
            // Let HTMX swap the error response (form with validation errors)
            e.detail.shouldSwap = true;
            console.log('⚠️ Validation errors received');
        }
        
        // Handle server errors (500, 503, etc.)
        if (status >= 500) {
            e.detail.shouldSwap = false;
            Alpine.store('app').hideLoading();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: `Terjadi kesalahan server (${status}). Silakan coba lagi.`,
                    confirmButtonColor: '#dc3545'
                });
            } else {
                alert(`Terjadi kesalahan server (${status}). Silakan coba lagi.`);
            }
        }
    } catch (error) {
        console.error('❌ Error in htmx:beforeSwap:', error);
    }
});

/**
 * Global loading indicator - show before request
 */
document.body.addEventListener('htmx:beforeRequest', (e) => {
    try {
        Alpine.store('app').showLoading();
        console.log('⏳ Request started');
        
        // Timeout fallback (5 seconds)
        const requestId = Date.now();
        e.detail.requestId = requestId;
        
        setTimeout(() => {
            if (Alpine.store('app').loading) {
                console.warn('⚠️ Request timeout - forcing hide loading');
                Alpine.store('app').hideLoading();
            }
        }, 5000);
    } catch (error) {
        console.error('❌ Error in htmx:beforeRequest:', error);
    }
});

/**
 * Global loading indicator - hide after request settles
 */
document.body.addEventListener('htmx:afterSettle', (e) => {
    try {
        Alpine.store('app').hideLoading();
        console.log('✅ Request completed');
    } catch (error) {
        console.error('❌ Error in htmx:afterSettle:', error);
    }
});

/**
 * Handle HTMX response errors
 */
document.body.addEventListener('htmx:responseError', (e) => {
    try {
        Alpine.store('app').hideLoading();
        
        console.error('❌ HTMX Response Error:', {
            status: e.detail.xhr.status,
            statusText: e.detail.xhr.statusText,
            url: e.detail.pathInfo.requestPath
        });
        
        // Don't show alert for 422 (validation errors) - handled by form
        if (e.detail.xhr.status === 422) {
            return;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: `Error ${e.detail.xhr.status}: ${e.detail.xhr.statusText}`,
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert(`Terjadi kesalahan (${e.detail.xhr.status}). Silakan coba lagi.`);
        }
    } catch (error) {
        console.error('❌ Error in htmx:responseError handler:', error);
    }
});

/**
 * Handle HTMX send errors (network issues)
 */
document.body.addEventListener('htmx:sendError', (e) => {
    try {
        Alpine.store('app').hideLoading();
        
        console.error('❌ HTMX Send Error:', e.detail);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
        }
    } catch (error) {
        console.error('❌ Error in htmx:sendError handler:', error);
    }
});

/**
 * Global CSRF Token injection for all HTMX requests
 */
document.body.addEventListener('htmx:configRequest', (e) => {
    try {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            e.detail.headers['X-CSRF-TOKEN'] = token.content;
        } else {
            console.warn('⚠️ CSRF token meta tag not found');
        }
    } catch (error) {
        console.error('❌ Error in htmx:configRequest:', error);
    }
});

/**
 * ============================================
 * GLOBAL PAGINATION HANDLER
 * ============================================
 */
document.addEventListener('click', function (e) {
    try {
        // Handle pagination links
        if (e.target.matches('.pagination a, .pagination a *')) {
            const link = e.target.closest('a');
            
            // Skip if no href or href is #
            if (!link || !link.href || link.getAttribute('href') === '#') {
                return;
            }
            
            e.preventDefault();
            
            const url = new URL(link.href);
            const page = url.searchParams.get('page');
            
            if (!page) return;
            
            // Find closest Alpine component
            const wrapper = link.closest('[x-data]');
            
            if (wrapper && window.Alpine) {
                const app = Alpine.$data(wrapper);
                
                if (app && app.currentPage !== undefined) {
                    app.currentPage = parseInt(page);
                    
                    console.log(`📄 Pagination: Going to page ${page}`);
                    
                    // Try different load methods based on what's available
                    if (typeof app.loadData === 'function') {
                        app.loadData(true);
                    } else if (typeof app.applyFilter === 'function') {
                        app.applyFilter();
                    } else if (typeof app.loadProducts === 'function') {
                        app.loadProducts(true);
                    } else if (typeof app.loadPegawais === 'function') {
                        app.loadPegawais(true);
                    } else if (typeof app.loadPensiuns === 'function') {
                        app.loadPensiuns(true);
                    } else {
                        console.warn('⚠️ No load method found in Alpine component');
                    }
                } else {
                    console.warn('⚠️ Alpine component has no currentPage property');
                }
            }
        }
    } catch (error) {
        console.error('❌ Error in pagination click handler:', error);
    }
});

/**
 * ============================================
 * GLOBAL ALERT HANDLER
 * ============================================
 */
document.body.addEventListener('showAlert', (e) => {
    try {
        const alertData = e.detail || {};
        
        console.log('🔔 Alert triggered:', alertData);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: alertData.icon || 'info',
                title: alertData.title || 'Notifikasi',
                text: alertData.text || '',
                html: alertData.html || undefined,
                confirmButtonColor: '#0d6efd',
                timer: alertData.timer || 3000,
                timerProgressBar: true,
                showConfirmButton: alertData.showConfirmButton !== false
            });
        } else {
            alert(alertData.text || alertData.title || 'Notifikasi');
        }
    } catch (error) {
        console.error('❌ Error in showAlert handler:', error);
    }
});

/**
 * ============================================
 * HELPER FUNCTIONS (Backward Compatibility)
 * ============================================
 */

/**
 * Initialize Bootstrap Modal
 * @deprecated Use Alpine.store('app').initModal() instead
 */
window.initModal = function (modalId = 'mainModal') {
    return Alpine.store('app').initModal(modalId);
};

/**
 * Show Loading Indicator
 */
window.showLoading = function () {
    Alpine.store('app').showLoading();
};

/**
 * Hide Loading Indicator
 */
window.hideLoading = function () {
    Alpine.store('app').hideLoading();
};

/**
 * Global Confirm Delete with SweetAlert
 * @param {number|string} id - Record ID
 * @param {string} name - Record name
 * @param {string} url - Delete endpoint URL
 * @param {string} eventName - Event to trigger after delete (e.g., 'pensiunUpdated')
 */
window.confirmDelete = function (id, name, url, eventName = null) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            console.error('❌ CSRF token not found');
            alert('CSRF token tidak ditemukan. Refresh halaman dan coba lagi.');
            return;
        }
        
        const performDelete = () => {
            Alpine.store('app').showLoading();
            
            htmx.ajax('DELETE', url, {
                target: 'body',
                swap: 'none',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(() => {
                Alpine.store('app').hideLoading();
                
                console.log(`🗑️ Delete successful: ${name}`);
                
                // Trigger custom event for table refresh
                if (eventName) {
                    document.body.dispatchEvent(new CustomEvent(eventName, {
                        bubbles: true,
                        cancelable: true
                    }));
                }
                
                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `${name} berhasil dihapus`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }).catch((error) => {
                Alpine.store('app').hideLoading();
                console.error('❌ Delete error:', error);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menghapus data',
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        };
        
        // Use SweetAlert if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Data?',
                text: `Yakin ingin menghapus ${name}? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete();
                }
            });
        } else {
            // Fallback to native confirm
            if (confirm(`Yakin ingin menghapus ${name}?`)) {
                performDelete();
            }
        }
    } catch (error) {
        console.error('❌ Error in confirmDelete:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
};

/**
 * Build query string from filters object
 * @param {Object} filters - Filter object
 * @param {number} page - Current page
 * @returns {string} Query string
 */
window.buildQueryString = function (filters = {}, page = 1) {
    try {
        const params = new URLSearchParams();
        
        // Add page if > 1
        if (page && page > 1) {
            params.set('page', page);
        }
        
        // Add filters
        Object.keys(filters).forEach(key => {
            const value = filters[key];
            
            // Skip empty values
            if (value === null || value === undefined || value === '') {
                return;
            }
            
            // Trim string values
            const finalValue = typeof value === 'string' ? value.trim() : value;
            
            if (finalValue) {
                params.set(key, finalValue);
            }
        });
        
        return params.toString();
    } catch (error) {
        console.error('❌ Error building query string:', error);
        return '';
    }
};

/**
 * Parse URL query string to filters object
 * @param {string} search - URL search string
 * @returns {Object} Filters object
 */
window.parseQueryString = function (search = window.location.search) {
    try {
        const params = new URLSearchParams(search);
        const filters = {};
        
        for (const [key, value] of params.entries()) {
            filters[key] = value;
        }
        
        return filters;
    } catch (error) {
        console.error('❌ Error parsing query string:', error);
        return {};
    }
};

/**
 * Debounce function for input events
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @returns {Function} Debounced function
 */
window.debounce = function (func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// ============================================
// INITIALIZATION
// ============================================
console.log('✅ Global HTMX + Alpine utilities loaded (Production Ready)');

// Expose Alpine store for debugging (development only)
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    window.appStore = () => Alpine.store('app');
    console.log('🔧 Debug mode: Use appStore() in console to access Alpine store');
}