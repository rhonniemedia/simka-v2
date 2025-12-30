/**
 * Product Module
 * Handles product-specific logic
 */
function productApp() {
    return {
        bsModal: null,
        currentPage: 1,
        filters: {
            search: '',
            min_price: '',
            max_price: '',
            per_page: '10'
        },

        init() {
            console.log('✅ Product Module initialized');
            
            this.bsModal = window.initModal('mainModal');
            this.loadFiltersFromURL();
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Open modal after HTMX loads content
            document.body.addEventListener('htmx:afterSwap', (e) => {
                if (e.detail.target.id === 'modal-content') {
                    this.bsModal.show();
                }
            });

            // Refresh after update/delete
            document.body.addEventListener('productUpdated', () => {
                this.bsModal.hide();
                this.loadProducts(false);
            });

            // Refresh after create
            document.body.addEventListener('productSaved', () => {
                this.bsModal.hide();
                this.currentPage = 1;
                this.loadProducts(false);
            });
        },

        loadFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);
            this.filters.search = params.get('search') || '';
            this.filters.min_price = params.get('min_price') || '';
            this.filters.max_price = params.get('max_price') || '';
            this.filters.per_page = params.get('per_page') || '10';
            this.currentPage = parseInt(params.get('page')) || 1;
        },

        applyFilter() {
            this.currentPage = 1;
            this.loadProducts(true);
        },

        resetFilter() {
            this.filters = {
                search: '',
                min_price: '',
                max_price: '',
                per_page: '10'
            };
            this.currentPage = 1;
            this.loadProducts(true);
        },

        loadProducts(showLoading = true) {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.filters.per_page
            });

            if (this.filters.search) params.set('search', this.filters.search);
            if (this.filters.min_price) params.set('min_price', this.filters.min_price);
            if (this.filters.max_price) params.set('max_price', this.filters.max_price);

            const url = `/products?${params.toString()}`;

            if (showLoading) {
                window.showLoading();
            }

            htmx.ajax('GET', url, {
                target: '#product-table',
                swap: 'innerHTML'
            });

            window.history.pushState({}, '', url);
        },

        confirmDelete(id, name) {
            window.confirmDelete(id, name, `/products/${id}`);
        }
    }
}

console.log('✅ Product module loaded');