/**
 * Global Module Handler for SIMKA
 * Menggantikan: mutasi.js, pegawai.js, product.js, dan pensiun.js
 */
window.initModule = function (config) {
    return {
        bsModal: null,
        detailModal: null,
        currentPage: 1,
        endpoint: config.endpoint,
        tableId: config.tableId,
        filters: {
            search: "",
            per_page: "10",
            ...config.initialFilters,
        },

        init() {
            console.log(`✅ ${config.moduleName} Module Initialized`);

            // Inisialisasi modal menggunakan fungsi global yang sudah ada
            this.bsModal = window.initModal("mainModal");
            this.detailModal = window.initModal("detailModal");

            this.loadFiltersFromURL();
            this.setupEventListeners();
            this.attachPaginationListeners();
        },

        setupEventListeners() {
            document.body.addEventListener("htmx:afterSwap", (e) => {
                // Otomatis buka modal jika konten berhasil dimuat via HTMX
                if (e.detail.target.id === "mainModal-content")
                    this.bsModal?.show();
                if (e.detail.target.id === "detailModal-content")
                    this.detailModal?.show();

                // Pasang kembali listener pagination jika tabel diperbarui
                if (e.detail.target.id === this.tableId) {
                    setTimeout(() => this.attachPaginationListeners(), 50);
                }
            });

            // Menangani event refresh setelah simpan/update
            const refreshEvents = [config.saveEvent, config.updateEvent];
            refreshEvents.forEach((eventName) => {
                document.body.addEventListener(eventName, () => {
                    this.bsModal?.hide();
                    this.detailModal?.hide();
                    if (eventName === config.saveEvent) this.currentPage = 1;
                    this.loadData(false);
                });
            });
        },

        attachPaginationListeners() {
            // Mengambil link pagination khusus untuk tabel ini
            document
                .querySelectorAll(`#${this.tableId} .pagination a`)
                .forEach((link) => {
                    link.addEventListener("click", (e) => {
                        e.preventDefault();
                        const page = new URL(link.href).searchParams.get(
                            "page"
                        );
                        if (page) {
                            this.currentPage = page;
                            this.loadData(true);
                        }
                    });
                });
        },

        loadFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);
            Object.keys(this.filters).forEach((key) => {
                if (params.has(key)) this.filters[key] = params.get(key);
            });
            this.currentPage = params.get("page") || 1;
        },

        loadData(showLoading = true) {
            const params = new URLSearchParams({
                page: this.currentPage,
            });

            // Otomatis memasukkan semua filter ke dalam URL request
            Object.keys(this.filters).forEach((key) => {
                if (this.filters[key]) {
                    params.set(key, String(this.filters[key]).trim());
                }
            });

            const url = `${this.endpoint}?${params.toString()}`;
            if (showLoading) window.showLoading();

            htmx.ajax("GET", url, {
                target: `#${this.tableId}`,
                swap: "innerHTML",
            }).then(() => {
                const loading = document.getElementById("loading");
                loading?.classList.remove("show-loading");
            });

            // Sinkronisasi URL browser agar bisa di-copy/paste atau di-refresh
            window.history.pushState({}, "", url);
        },

        applyFilter() {
            this.currentPage = 1;
            this.loadData(true);
        },

        resetFilter() {
            Object.keys(this.filters).forEach((key) => {
                this.filters[key] = key === "per_page" ? "10" : "";
            });
            this.currentPage = 1;
            this.loadData(true);
        },

        confirmDelete(id, name) {
            window.confirmDelete(id, name, `${this.endpoint}/${id}`);
        },
    };
};
