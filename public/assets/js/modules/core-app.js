/**
 * Global CRUD Module (Alpine.js)
 * Versi Final: Event Delegation untuk Pagination (Stabil) + Loading Fix
 */

// --- GLOBAL MODAL HANDLER ---
// Berjalan otomatis untuk semua modal di aplikasi (Global)
if (!window.modalGlobalInitialized) {
    document.addEventListener("show.bs.modal", (e) => {
        const content = document.getElementById(e.target.id + "-content");
        if (content) {
            content.innerHTML =
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        }
    });

    document.addEventListener("hidden.bs.modal", (e) => {
        const content = document.getElementById(e.target.id + "-content");
        if (content) content.innerHTML = "";

        // Bersihkan sisa backdrop
        document.querySelectorAll(".modal-backdrop").forEach((b) => b.remove());
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "";
    });
    window.modalGlobalInitialized = true;
}

function coreApp(config = {}) {
    return {
        // Konfigurasi
        baseUrl: config.baseUrl || "",
        tableId: config.tableId || "#data-table",
        modalId: config.modalId || "mainModal",
        successEvents: config.successEvents || [],
        detailId: config.detailId || "detailModal",
        eventName: config.eventName || "entity",

        bsModal: null,
        detailModal: null,
        currentPage: 1,

        // Filter State
        filters: {
            search: "",
            per_page: "10",
            ...config.additionalFilters,
        },

        // Snapshot untuk fitur Reset
        initialState: null,

        init() {
            console.log(`✅ CRUD Module initialized for: ${this.baseUrl}`);

            // 1. Simpan snapshot filter awal (Deep Copy)
            this.initialState = JSON.parse(JSON.stringify(this.filters));

            // 2. Init Modals
            if (window.initModal) {
                this.bsModal = window.initModal(this.modalId);
                this.detailModal = window.initModal(this.detailId);
            }

            // AUTO-CLOSE MODAL (Dinamis berdasarkan list event khusus)
            this.successEvents.forEach((evt) => {
                document.body.addEventListener(evt, () => {
                    const modalEl = document.getElementById(this.modalId);
                    if (modalEl) {
                        const instance =
                            bootstrap.Modal.getInstance(modalEl) ||
                            new bootstrap.Modal(modalEl);
                        instance.hide();
                    }
                });
            });

            // 3. Setup
            this.loadFiltersFromURL();
            this.setupEventListeners();

            // 4. Setup Pagination dengan Event Delegation
            // Delay sedikit untuk memastikan DOM sudah siap
            this.$nextTick(() => {
                this.setupPaginationDelegation();
            });
        },

        setupEventListeners() {
            // HTMX After Swap: Handle Modal + Re-attach Pagination
            document.body.addEventListener("htmx:afterSwap", (e) => {
                const targetId = e.detail.target.id;

                // Handle Modal
                if (targetId === `${this.modalId}-content`) {
                    this.bsModal?.show();
                }
                if (targetId === `${this.detailId}-content`) {
                    this.detailModal?.show();
                }

                // ✅ Re-attach pagination setelah table di-swap
                if (targetId === this.tableId.replace("#", "")) {
                    setTimeout(() => {
                        this.setupPaginationDelegation();
                    }, 50);
                }
            });

            // Handle Events dari Controller
            document.body.addEventListener(`${this.eventName}Updated`, () => {
                this.bsModal?.hide();
                this.detailModal?.hide();
                this.loadData(false);
            });

            document.body.addEventListener(`${this.eventName}Saved`, () => {
                this.bsModal?.hide();
                this.currentPage = 1;
                this.loadData(false);
            });
        },

        setupPaginationDelegation() {
            // Kita pasang listener di CONTAINER tabel, bukan di linknya langsung.
            // Container tidak ikut ter-swap, jadi listener ini abadi.
            const tableContainer = document.querySelector(this.tableId);

            if (!tableContainer) {
                console.warn(
                    `⚠️ Table container ${this.tableId} not found yet, retrying...`,
                );
                // Retry setelah 100ms jika belum ada
                setTimeout(() => this.setupPaginationDelegation(), 100);
                return;
            }

            // ✅ Hapus listener lama untuk mencegah duplikasi
            const oldListener = tableContainer._paginationListener;
            if (oldListener) {
                tableContainer.removeEventListener("click", oldListener);
            }

            // ✅ Buat listener baru
            const paginationListener = (e) => {
                // Cek apakah yang diklik adalah link pagination (atau anak elemennya)
                const link = e.target.closest(".pagination a");

                if (link) {
                    e.preventDefault(); // Matikan reload standar
                    e.stopPropagation(); // Cegah propagasi ganda

                    const url = new URL(link.href);
                    const page = url.searchParams.get("page");

                    if (page && page !== this.currentPage.toString()) {
                        this.currentPage = page;
                        console.log(`📄 Navigasi ke halaman: ${page}`);
                        this.loadData(true); // TRUE = Tampilkan Loading
                    }
                }
            };

            // ✅ Simpan referensi listener untuk cleanup
            tableContainer._paginationListener = paginationListener;
            tableContainer.addEventListener("click", paginationListener);

            console.log(`✅ Pagination listener attached to ${this.tableId}`);
        },

        loadFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);
            for (const key in this.filters) {
                if (params.has(key)) {
                    this.filters[key] = params.get(key);
                }
            }
            this.currentPage = parseInt(params.get("page")) || 1;
        },

        applyFilter() {
            this.currentPage = 1;
            this.loadData(true);
        },

        resetFilter() {
            // Kembalikan filter ke kondisi awal
            this.filters = JSON.parse(JSON.stringify(this.initialState));
            this.currentPage = 1;
            this.loadData(true);
        },

        loadData(showLoading = true) {
            const params = new URLSearchParams({
                page: this.currentPage,
            });

            // Map filters ke URL
            for (const [key, value] of Object.entries(this.filters)) {
                if (value && value.toString().trim() !== "") {
                    params.set(key, value.toString().trim());
                }
            }

            const url = `${this.baseUrl}?${params.toString()}`;

            // --- LOADING LOGIC ---
            if (showLoading) {
                if (window.showLoading) {
                    console.log("🔄 Showing loading...");
                    window.showLoading();
                } else {
                    const loadingEl = document.getElementById("loading");
                    if (loadingEl) {
                        loadingEl.classList.add("show-loading");
                        console.log("🔄 Loading class added");
                    } else {
                        console.warn("⚠️ Loading element not found");
                    }
                }
            }

            // Safety timeout
            const loadingTimeout = setTimeout(() => {
                const loading = document.getElementById("loading");
                if (loading) {
                    loading.classList.remove("show-loading");
                    console.warn("⚠️ Loading timeout - forced close");
                }
            }, 5000);

            // HTMX Request
            htmx.ajax("GET", url, {
                target: this.tableId,
                swap: "innerHTML",
                indicator: "#loading",
            })
                .then(() => {
                    clearTimeout(loadingTimeout);
                    const loading = document.getElementById("loading");
                    if (loading) {
                        loading.classList.remove("show-loading");
                        console.log("✅ Loading hidden after success");
                    }
                })
                .catch((error) => {
                    clearTimeout(loadingTimeout);
                    const loading = document.getElementById("loading");
                    if (loading) {
                        loading.classList.remove("show-loading");
                    }
                    console.error(`❌ Failed to load data:`, error);
                });

            window.history.pushState({}, "", url);
        },

        confirmDelete(el, name) {
            // <-- Parameter pertama 'el' (element)
            Swal.fire({
                title: "Hapus Data?",
                text: `Apakah Anda yakin ingin menghapus "${name}"?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    // ✅ KUNCI: Kirim event 'confirmed' ke tombol agar HTMX jalan
                    el.dispatchEvent(new CustomEvent("confirmed"));
                }
            });
        },
    };
}
