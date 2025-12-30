<!-- Filter Component -->
<div class="filter-section">
    <form id="filter-form" @submit.prevent="applyFilter()">
        <div class="row g-3">
            {{ $slot }}

            <!-- Actions -->
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-1">
                    {{ $submitText ?? 'Filter' }}
                </button>
                <button type="button" class="btn btn-secondary" @click="resetFilter()">
                    {{ $resetText ?? 'Reset' }}
                </button>
            </div>
        </div>
    </form>
</div>