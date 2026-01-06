<div class="modal-content" id="form-wizard-wrapper">
    <div class="modal-header">
        <h5 class="modal-title">
            <i class="mdi mdi-{{ (isset($pegawai) && $pegawai->exists) ? 'account-edit' : 'account-plus' }} me-2"></i>

            {{-- Perubahan di sini --}}
            {{ (isset($pegawai) && $pegawai->exists) ? 'Lanjutkan Pengisian' : 'Tambah Pegawai' }}:

            <span id="step-label" class="fw-normal">
                {{ (isset($pegawai) && $pegawai->exists) ? $pegawai->nama : 'Data Pribadi' }}
            </span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="progress rounded-0" style="height: 6px;">
        <div id="form-progress-bar"
            class="progress-bar bg-info progress-bar-striped progress-bar-animated"
            @style([ 'width: ' . (($currentStep ?? 1) * 25) . '%'
            ])>
        </div>
    </div>

    <div id="step-content-placeholder">
        @if(isset($currentStep))
        @if($currentStep == 1)
        @include('contents.pegawai.partials.form-step1', ['pegawai' => $pegawai])
        @elseif($currentStep == 2)
        @include('contents.pegawai.partials.form-step2', ['pegawai' => $pegawai])
        @elseif($currentStep == 3)
        @include('contents.pegawai.partials.form-step3', ['pegawai' => $pegawai])
        @else
        @include('contents.pegawai.partials.form-step4', ['pegawai' => $pegawai])
        @endif
        @else
        @include('contents.pegawai.partials.form-step1')
        @endif
    </div>
</div>