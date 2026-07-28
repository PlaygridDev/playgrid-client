<div id="twoFactorAuthMethodEnablePopup">
    {if $.site.config.security.two_factor_authentication_mode === 'required' && !$.site.session->get2FAStatus()}
    <h5 class="text-danger text-center">
        {$two_factor_auth_method_enable_popup_required_info}
    </h5>
    {/if}
    <h6 class="text-center text-dark">
        {$two_factor_auth_method_enable_popup_info}
    </h6>
    <div class="btn-group w-100" role="group" aria-label="twoFactorAuthMethodButtons">
        <button
            v-for="(methodParams, method) in methods"
            type="button"
            class="btn text-capitalize"
            :class="method === selectedMethod ? 'btn-primary' : 'btn-secondary'"
            @click="setSelectedMethod(method)"
        >
            [[ methodParams.label ]]
        </button>
    </div>
    <div class="form-group d-flex flex-column justify-content-center align-items-center py-30 mb-0">
        <div class="alert alert-primary" v-if="!methods[selectedMethod].verified">
            <p class="mb-0">
                {$two_factor_auth_method_enable_not_available_info}
                <a
                    href="{$.php.set_url('panel/settings')}"
                    class="text-priamry"
                >
                    {$two_factor_auth_method_enable_not_available_link}
                </a>
            </p>
        </div>
        <div v-else>
            <button
                v-if="!showCodeInput"
                type="button"
                class="btn btn-hero btn-sm btn-alt-primary text-uppercase mb-10"
                @click="sendCode(selectedMethod)"
                :disabled="isSending"
            >
                {$two_factor_auth_method_enable_send_code_to} [[ methods[selectedMethod].label ]]
            </button>
        </div>
        <div class="d-flex justify-content-center mb-3 align-items-start" v-if="showCodeInput">
            <div class="form-material pt-0 text-center">
                <input type="text" class="form-control form-control-lg text-center" id="verificationCode" name="code" placeholder="Введите код" v-model="code">
                <div class="mt-1">
                    <small>
                        <div v-if="remainingSeconds > 0" class="text-muted">{$two_factor_auth_method_enable_retry_send_after} [[ timer ]]</div>
                        <div v-else class="text-primary" style="cursor: pointer" @click="showCodeInput = false">{$two_factor_auth_method_enable_retry_send_button}</div>
                    </small>
                </div>
            </div>
        </div>
        <span class="text-center" :class="alert.type" v-if="alert.type !== null && alert.message !== null">
            [[ alert.message ]]
        </span>
    </div>
</div>
<script>
    var twoFactorAuthMethodEnablePopup = new Vue({
        el: '#twoFactorAuthMethodEnablePopup',
        delimiters: ['[[', ']]'],
        data: {
            methods: {$methods},
            selectedMethod: 'email',
            code: null,
            alert: {
                type: null,
                message: null
            },
            timerInterval: null,
            showCodeInput: false,
            remainingSeconds: 0,
            isSending: false,
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
        mounted: function() {
            if (Object.keys(this.methods).length > 0) {
                this.selectedMethod = Object.keys(this.methods)[0];
            }
        },
        methods: {

            setSelectedMethod(method) {
                this.syncTimer(0);
                this.showCodeInput = false;
                this.code = null;
                this.selectedMethod = method;
                this.setAlert(null, null);
            },

            setAlert(type, message) {
                this.alert.type = type;
                this.alert.message = message;
            },

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

            sendCode: function() {

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
                        'module_form': "Modules\\Globals\\Settings\\Settings",
                        'module': 'enable_two_factor_auth_method_send_code',
                        'method': this.selectedMethod,
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
                        'module_form': "Modules\\Globals\\Settings\\Settings",
                        'module': 'enable_two_factor_auth_method_confirm',
                        'code': this.code,
                        'method': this.selectedMethod,
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

        },
    });
</script>