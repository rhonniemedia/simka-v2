/**
 * Pegawai Module
 * Handles pegawai-specific logic
 */
function pegawaiApp() {
    return {
        bsModal: null,
        detailModal: null,
        currentPage: 1,
        filters: {
            search: "",
            min_price: "",
            max_price: "",
            per_page: "10",
        },

        init() {
            console.log("✅ Pegawai Module initialized");

            // Modal untuk CRUD
            this.bsModal = window.initModal("mainModal");

            // Modal untuk Detail
            this.detailModal = window.initModal("detailModal");

            this.loadFiltersFromURL();
            this.setupEventListeners();
            this.attachPaginationListeners(); // ✅ Panggil pertama kali
        },

        setupEventListeners() {
            // Open modal after HTMX loads content untuk mainModal
            document.body.addEventListener("htmx:afterSwap", (e) => {
                if (e.detail.target.id === "mainModal-content") {
                    this.bsModal.show();
                }

                // untuk detail modal
                if (e.detail.target.id === "detailModal-content") {
                    this.detailModal.show();
                }

                // ✅ Re-attach pagination setiap kali table di-swap
                if (e.detail.target.id === "pegawai-table") {
                    setTimeout(() => this.attachPaginationListeners(), 50);
                }
            });

            // Refresh after update/delete
            document.body.addEventListener("pegawaiUpdated", () => {
                this.bsModal.hide();
                this.detailModal.hide();
                this.loadPegawais(false);
            });

            // Refresh after create
            document.body.addEventListener("pegawaiSaved", () => {
                this.bsModal.hide();
                this.currentPage = 1;
                this.loadPegawais(false);
            });
        },

        // ✅ METHOD BARU - Attach pagination listeners
        attachPaginationListeners() {
            document.querySelectorAll('#pegawai-table .pagination a').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    
                    if (page) {
                        this.currentPage = page;
                        this.loadPegawais(true);
                    }
                });
            });
        },

        loadFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);
            this.filters.search = params.get("search") || "";
            this.filters.min_price = params.get("min_price") || "";
            this.filters.max_price = params.get("max_price") || "";
            this.filters.per_page = params.get("per_page") || "10";
            this.currentPage = parseInt(params.get("page")) || 1;
        },

        applyFilter() {
            this.currentPage = 1;
            this.loadPegawais(true);
        },

        resetFilter() {
            this.filters = {
                search: "",
                min_price: "",
                max_price: "",
                per_page: "10",
            };
            this.currentPage = 1;
            this.loadPegawais(true);
        },

        loadPegawais(showLoading = true) {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.filters.per_page,
            });

            if (this.filters.search) params.set("search", this.filters.search);
            if (this.filters.min_price)
                params.set("min_price", this.filters.min_price);
            if (this.filters.max_price)
                params.set("max_price", this.filters.max_price);

            const url = `/pegawais?${params.toString()}`;

            if (showLoading) {
                window.showLoading();
            }

            // Fallback timeout: hide loading after 5 seconds if stuck
            const loadingTimeout = setTimeout(() => {
                const loading = document.getElementById("loading");
                if (loading) {
                    loading.classList.remove("show-loading");
                    console.warn("⚠️ Loading timeout - forced close");
                }
            }, 5000);

            // Make HTMX request with error handling
            htmx.ajax("GET", url, {
                target: "#pegawai-table",
                swap: "innerHTML",
            })
                .then(() => {
                    // Success: hide loading
                    clearTimeout(loadingTimeout);
                    const loading = document.getElementById("loading");
                    if (loading) {
                        loading.classList.remove("show-loading");
                    }
                })
                .catch((error) => {
                    // Error: hide loading and show error
                    clearTimeout(loadingTimeout);
                    const loading = document.getElementById("loading");
                    if (loading) {
                        loading.classList.remove("show-loading");
                    }
                    console.error("❌ Failed to load pegawais:", error);
                });

            window.history.pushState({}, "", url);
        },

        confirmDelete(id, name) {
            window.confirmDelete(id, name, `/pegawais/${id}`);
        },
    };
}

console.log("✅ Pegawai module loaded");