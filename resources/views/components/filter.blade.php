<!-- Filter Component -->
<div class="card border bg-light mb-3">
    <div class="card-body filter-section pb-2">
        <form id="filter-form" @submit.prevent="applyFilter()">
            <div class="row">
                {{ $slot }}

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button"
                        class="btn btn-secondary w-100"
                        @click="resetFilter()"
                        title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>