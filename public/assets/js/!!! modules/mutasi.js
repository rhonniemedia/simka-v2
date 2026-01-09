/**
 * Mutasi Module
 * Handles mutasi-specific logic
 */
function mutasiApp() {
    return {
        bsModal: null,
        detailModal: null,
        currentPage: 1,
        selectedSlug: "",
        filters: {
            search: "",
            mutasi: "",
            jp: "",
            per_page: "10",
        },

        init() {
            console.log("✅ Mutasi Module initialized");

            // Modal untuk CRUD
            this.bsModal = window.initModal("mainModal");

            if (!this.bsModal) {
                console.error("❌ Failed to initialize mainModal");
                return;
            }

            // Modal untuk Detail
            this.detailModal = window.initModal("detailModal");

            this.loadFiltersFromURL();
            this.setupEventListeners();
            this.attachPaginationListeners();
        },

        setupEventListeners() {
            // Open modal after HTMX loads content untuk mainModal
            document.body.addEventListener("htmx:afterSwap", (e) => {
                console.log("🔄 HTMX afterSwap:", e.detail.target.id);

                if (e.detail.target.id === "mainModal-content") {
                    console.log("🔓 Opening mainModal");

                    // Show modal
                    if (this.bsModal) {
                        this.bsModal.show();
                    } else {
                        console.error("❌ bsModal is not initialized");
                    }
                }

                // untuk detail modal
                if (e.detail.target.id === "detailModal-content") {
                    if (this.detailModal) {
                        this.detailModal.show();
                    }
                }

                // ✅ Re-attach pagination setiap kali table di-swap
                if (e.detail.target.id === "mutasi-table") {
                    setTimeout(() => this.attachPaginationListeners(), 50);
                }
            });

            // Handle HTMX errors
            document.body.addEventListener("htmx:responseError", (e) => {
                console.error("❌ HTMX Error:", e.detail);
                alert("Terjadi kesalahan saat memuat data. Silakan coba lagi.");
            });

            // Refresh after update/delete
            document.body.addEventListener("mutasiUpdated", () => {
                console.log("🔄 mutasiUpdated event received");
                if (this.bsModal) this.bsModal.hide();
                if (this.detailModal) this.detailModal.hide();
                this.loadMutasis(false);
            });

            // Refresh after create
            document.body.addEventListener("mutasiSaved", () => {
                console.log("💾 mutasiSaved event received");
                if (this.bsModal) this.bsModal.hide();
                this.currentPage = 1;
                this.loadMutasis(false);
            });
        },

        attachPaginationListeners() {
            document
                .querySelectorAll("#mutasi-table .pagination a")
                .forEach((link) => {
                    link.addEventListener("click", (e) => {
                        e.preventDefault();
                        const url = new URL(link.href);
                        const page = url.searchParams.get("page");

                        if (page) {
                            this.currentPage = page;
                            this.loadMutasis(true);
                        }
                    });
                });
        },

        loadFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);
            this.filters.search = params.get("search") || "";
            this.filters.mutasi = params.get("status") || "";
            this.filters.jp = params.get("jp_id") || "";
            this.filters.per_page = params.get("per_page") || "10";
            this.currentPage = parseInt(params.get("page")) || 1;
        },

        applyFilter() {
            this.currentPage = 1;
            this.loadMutasis(true);
        },

        resetFilter() {
            this.filters = {
                search: "",
                mutasi: "",
                jp: "",
                per_page: "10",
            };
            this.currentPage = 1;
            this.loadMutasis(true);
        },

        loadMutasis(showLoading = true) {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.filters.per_page,
            });

            // ✅ Tambahkan filter search (dengan trim)
            if (this.filters.search && this.filters.search.trim()) {
                params.set("search", this.filters.search.trim());
            }

            // ✅ Tambahkan filter status mutasi
            if (this.filters.mutasi) {
                params.set("status", this.filters.mutasi);
            }

            // ✅ Tambahkan filter jenis mutasi
            if (this.filters.jp) {
                params.set("jp_id", this.filters.jp);
            }

            const url = `/employees/mutations?${params.toString()}`;

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
                target: "#mutasi-table",
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
                    console.error("❌ Failed to load mutasis:", error);
                });

            window.history.pushState({}, "", url);
        },

        confirmDelete(id, name) {
            window.confirmDelete(id, name, `/employees/mutations/${id}`);
        },
    };
}

console.log("✅ Mutasi module loaded");
