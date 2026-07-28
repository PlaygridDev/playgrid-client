<div id="twoFactorVerificationPopup">
    <div class="d-flex flex-column align-items-center justify-content-center">
        <h5 class="mb-4 text-dark">{$two_factor_verification_popup_subtitle}</h5>
        <div class="btn-group btn-group-toggle text-capitalize mb-3" data-toggle="buttons">
            <label
                v-for="(methodParams, method) in methods"
                :class="selectedMethod == method ? 'btn-primary' : 'btn-secondary'"
                @click="setSelectedmethod(method)"
                class="btn"
                style="cursor: pointer;"
            >
                [[ methodParams.label ]]
            </label>
        </div>
        <div class="d-flex justify-content-center mb-3 align-items-start">
            <button
                v-if="!showCodeInput && selectedMethod"
                type="button"
                class="btn btn-hero btn-sm btn-alt-primary text-uppercase"
                @click="startVerificationProcess(selectedMethod)"
                :disabled="isSending"
            >
                {$two_factor_verification_send_code_button}
            </button>
            <div class="form-material pt-0 text-center" v-if="showCodeInput">
                <input
                    v-model="code"
                    type="text"
                    class="form-control form-control-lg text-center"
                    id="verificationCode"
                    name="code"
                    placeholder="{$two_factor_verification_code_input_placeholder}"
                >
                <div class="mt-1">
                    <small>
                        <div v-if="remainingSeconds > 0" class="text-muted">
                            {$two_factor_verification_retry_send_after}
                            [[ timer ]]
                        </div>
                        <div v-else class="text-primary" style="cursor: pointer" @click="showCodeInput = false">
                            {$two_factor_verification_retry_send_button}
                        </div>
                    </small>
                </div>
            </div>
        </div>
        <span class="text-center mb-3" :class="alert.type" v-if="alert.type !== null && alert.message !== null">
            [[ alert.message ]]
        </span>
    </div>
</div>

<script>

    window.twoFactorVerificationPopup = new Vue({

        el: '#twoFactorVerificationPopup',

        delimiters: ['[[', ']]'],

        data: {
            methods: {$methods},
            selectedMethod: null,
            remainingSeconds: 0,
            action: '{$action}',
            code: null,
            showCodeInput: false,
            isSending: false,
            alert: {
                type: null,
                message: null
            },
            timerInterval: null,
            module: '{$module}',
            moduleForm: {$module_form},
        },

        computed: {

            codeSent() {
                return this.remainingSeconds > 0;
            },

            timer() {
                const minutes = Math.floor(this.remainingSeconds / 60);
                const seconds = this.remainingSeconds % 60;
                {ignore}
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
                {/ignore}
            }

        },

        watch: {
            code(code) {
                var value = '';

                if(code) {
                    value = code.replace(/\D/g, '').slice(0, 6);
                }

                this.code = value;

                if (value.length === 6) {
                    this.checkCode(value);
                }
            }
        },

        methods: {

            syncTimer(seconds) {

                clearInterval(this.timerInterval);
                this.timerInterval = null;

                this.remainingSeconds = Math.max(0, Number(seconds) || 0);

                if (!this.remainingSeconds) {
                    return;
                }

                this.timerInterval = setInterval(() => {

                    if (--this.remainingSeconds <= 0) {

                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                        this.remainingSeconds = 0;
                        this.setAlert(null, null);

                    }

                }, 1000);

            },

            setAlert: function(type, message) {
                this.alert.type = type;
                this.alert.message = message;
            },

            setSelectedmethod: function(method) {
                if(this.selectedMethod === method) {
                    return;
                }
                this.syncTimer(0);
                this.showCodeInput = false;
                this.code = null;
                this.selectedMethod = method;
                this.setAlert(null, null);
            },

            startVerificationProcess: function() {

                if (this.isSending) {
                    return;
                }

                this.isSending = true;

                fetch('/input', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        'module_form': "Modules\\Globals\\Security\\Security",
                        'module': 'start_two_factor_verification',
                        'method': this.selectedMethod,
                        'action': this.action,
                    }),
                }).then(response => {
                    if(response.ok) {
                        return response.json();
                    } else {
                        throw new Error('[sendCode] Error: ' + response.statusText);
                    }
                }).then(data => {
                    if('status' in data && 'text' in data) {
                        this.syncTimer(data.retry_after || 0);
                        switch(data.status) {
                            case 'success':
                                this.setAlert('text-success', data.text);
                                this.code = null;
                                this.showCodeInput = true;
                                this.$nextTick(() => {
                                    $('#verificationCode').focus();
                                });
                                break;
                            case 'danger':
                                this.setAlert('text-danger', data.text);
                                if(data.retry_after) {
                                    this.showCodeInput = true;
                                }
                                this.code = null;
                                break;
                            default:
                                throw new Error('Unexpected status: ' + data.status);
                                break;
                        }
                    } else {
                        throw new Error('[sendCode] Unexpected response format');
                    }
                }).catch(error => {
                    console.error(error);
                    this.setAlert('text-danger', 'Error, try again later.');
                }).finally(() => {
                    this.isSending = false;
                });
            },
            checkCode(code) {
                this.setAlert(null, null);
                $('#verificationCode').attr('disabled', true);
                fetch('/input', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        'module_form': this.moduleForm,
                        'module': this.module,
                        '2fa[code]': this.code,
                        '2fa[method]': this.selectedMethod,
                    }),
                }).then(response => {
                    if(response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error: ' + response.statusText);
                    }
                }).then(data => {
                    if('status' in data && 'text' in data) {
                        switch(data.status) {
                            case 'success':
                                $('#modal-ajax').modal('hide');
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                                break;
                            case 'danger':
                                this.setAlert('text-danger', data.text);
                                this.code = null;
                                break;
                            default:
                                throw new Error('Unexpected status: ' + data.status);
                                break;
                        }

                    } else {
                        throw new Error('[checkCode] Unexpected response format');
                    }
                }).catch(error => {
                    console.error(error);
                    this.setAlert('alert-danger', 'Error, try again later.');
                }).finally(() => {
                    $('#verificationCode').attr('disabled', false);
                });
            },
        }

    });

</script>