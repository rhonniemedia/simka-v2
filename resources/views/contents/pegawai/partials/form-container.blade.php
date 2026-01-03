<div class="modal-content" id="form-wizard-wrapper">
    <div class="modal-header">
        <h5 class="modal-title">
            <i class="mdi mdi-account-plus me-2"></i>Tambah Pegawai:
            <span id="step-label" class="fw-normal">Data Pribadi</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="progress rounded-0" style="height: 6px;">
        <div id="form-progress-bar"
            class="progress-bar bg-info progress-bar-striped progress-bar-animated"
            style="width: 25%;">
        </div>
    </div>

    <div id="step-content-placeholder">
        @include('contents.pegawai.partials.form-step1')
    </div>
</div>