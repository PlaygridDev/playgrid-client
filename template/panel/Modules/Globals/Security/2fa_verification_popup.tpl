<div id="twoFactorVerificationPopup">
    <div class="d-flex flex-column align-items-center justify-content-center">
        <h5 class="mb-4 text-dark">{$two_factor_verification_popup_subtitle}</h5>
        <div class="btn-group btn-group-toggle text-capitalize mb-3" data-toggle="buttons" v-if="!isRecoveryMode">
            <label
                v-for="(methodParams, method) in methods"
                v-if="!methodParams.recovery"
                :class="selectedMethod == method ? 'btn-primary' : 'btn-secondary'"
                @click="setSelectedmethod(method)"
                class="btn"
                style="cursor: pointer;"
            >
                [[ methodParams.label ]]
            </label>
        </div>
        <div class="d-flex justify-content-center mb-3 align-items-start" v-if="selectedMethod">
            <button
                v-if="!showCodeInput"
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
                    :placeholder="codePlaceholder"
                >
                <div class="mt-1" v-if="methods[selectedMethod].send_required">
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
                <div class="mt-1" v-else-if="!methods[selectedMethod].recovery">
                    <small class="text-muted">{$two_factor_verification_totp_hint}</small>
                </div>
                <div class="mt-1" v-else>
                    <small class="text-muted">{$two_factor_verification_recovery_hint}</small>
                </div>
            </div>
        </div>
        <span class="text-center mb-3" :class="alert.type" v-if="alert.type !== null && alert.message !== null">
            [[ alert.message ]]
        </span>
        <div class="text-center mb-3" v-if="methods.recovery_code">
            <small>
                <a href="javascript:void(0);" v-if="!isRecoveryMode" @click="setSelectedmethod('recovery_code')">
                    {$two_factor_verification_use_recovery_code}
                </a>
                <a href="javascript:void(0);" v-else @click="backToMethods()">
                    {$two_factor_verification_back_to_methods}
                </a>
            </small>
        </div>
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

            isRecoveryMode() {
                return this.selectedMethod !== null
                    && !!this.methods[this.selectedMethod]
                    && !!this.methods[this.selectedMethod].recovery;
            },

            codePlaceholder() {
                return this.isRecoveryMode
                    ? '{$two_factor_verification_recovery_placeholder}'
                    : '{$two_factor_verification_code_input_placeholder}';
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

                if (this.isRecoveryMode) {

                    if (code) {
                        value = code.toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 10);
                        if (value.length > 5) {
                            value = value.slice(0, 5) + '-' + value.slice(5);
                        }
                    }

                    this.code = value;

                    if (value.length === 11) {
                        this.checkCode(value);
                    }

                    return;
                }

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
                this.showCodeInput = !this.methods[method].send_required;
                this.code = null;
                this.selectedMethod = method;
                this.setAlert(null, null);
                if (this.showCodeInput) {
                    this.$nextTick(() => {
                        $('#verificationCode').focus();
                    });
                }
            },

            backToMethods: function() {
                this.syncTimer(0);
                this.showCodeInput = false;
                this.code = null;
                this.selectedMethod = null;
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