<!-- Filter Component -->
<div class="filter-section">
    <form id="filter-form" @submit.prevent="applyFilter()">
        <div class="row g-1">
            {{ $slot }}

            <!-- Actions -->
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-secondary" @click="resetFilter()">
                    {{ $resetText ?? 'Reset' }}
                </button>
            </div>
        </div>
    </form>
</div>