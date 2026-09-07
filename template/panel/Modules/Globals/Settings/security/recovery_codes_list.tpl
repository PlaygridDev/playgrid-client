<div v-if="showRecoveryCodes" class="text-center">
    <h5 class="text-dark mb-2">{$two_factor_recovery_codes_title}</h5>
    <p class="text-muted mb-3">{$two_factor_recovery_codes_save_warning}</p>
    <div class="row no-gutters justify-content-center mb-3">
        <div class="col-6 col-md-4 py-1" v-for="recoveryCode in recoveryCodes">
            <span class="font-size-h5">[[ recoveryCode ]]</span>
        </div>
    </div>
    <div class="d-flex justify-content-center mb-3">
        <button type="button" class="btn btn-alt-secondary mr-2" @click="downloadRecoveryCodes()">
            <i class="fa fa-download mr-1"></i> {$two_factor_recovery_codes_download_button}
        </button>
        <button type="button" class="btn btn-alt-primary" @click="finish()">
            <i class="fa fa-check mr-1"></i> {$two_factor_recovery_codes_saved_button}
        </button>
    </div>
</div>
