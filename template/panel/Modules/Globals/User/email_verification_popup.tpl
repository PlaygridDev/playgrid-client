<div id="emailVerificationPopup">
    <div class="alert" :class="alert.type" v-if="alert.type !== null && alert.message !== null">
        <p class="mb-0" v-html="alert.message">
        </p>
    </div>
    <div class="form-group row mb-3">
        <div class="col-12 col-lg-12">
            <div class="input-group">
                <input type="email" class="form-control" id="verificationEmail" name="email" placeholder="Введите Email" v-model="inputEmail" :disabled="codeSent || isSending">
                <div class="input-group-append">
                    <button type="button" class="btn btn-alt-primary" id="sendCodeButton" :disabled="codeSent || isSending" @click="sendCode()">
                        <span v-if="!codeSent">{$send_code}</span>
                        <span v-else>[[ timer ]]</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center mb-3 align-items-start" v-if="showCodeInput">
        <div class="form-material pt-0 mb-3 w-75">
            <input type="text" class="form-control form-control-lg text-center" id="verificationCode" name="code" placeholder="{$type_code}" v-model="code">
        </div>
    </div>
    <small v-if="userEmail !== inputEmail">
        {$emails_not_match}
    </small>
</div>
<script>

    var emailVerificationPopup = new Vue({
        el: '#emailVerificationPopup',
        delimiters: ['[[', ']]'],
        data: {
            inputEmail: '{$.site.session->getEmail()}',
            userEmail: '{$.site.session->getEmail()}',
            showCodeInput: false,
            isSending: false,
            code: null,
            timerInterval: null,
            remainingSeconds: 0,
            alert: {
                type: null,
                message: null
            },
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
                        this.showCodeInput = false;
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
                        'module_form': "Modules\\Globals\\User\\User",
                        'module': 'email_verification_send_code',
                        'email': this.inputEmail,
                    }),
                }).then(response => {
                    if(response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Error: ' + response.statusText);
                    }
                }).then(data => {
                    if('status' in data && 'text' in data) {
                        this.syncTimer(data.retry_after || 0);
                        switch(data.status) {
                            case 'success':
                                this.setAlert('alert-success', data.text);
                                this.showCodeInput = true;
                                this.code = null;
                                this.$nextTick(() => {
                                    $('#verificationCode').focus();
                                });
                                break;
                            case 'danger':
                                if(data.retry_after) {
                                    this.showCodeInput = true;
                                }
                                this.setAlert('alert-danger', data.text);
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
                    this.setAlert('alert-danger', 'Error, try again later.');
                }).finally(() => {
                    this.isSending = false;
                });

            },

            checkCode: function(code) {
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
                        'module_form': "Modules\\Globals\\User\\User",
                        'module': 'email_verification_confirm',
                        'code': this.code,
                        'email': this.inputEmail,
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
                                this.setAlert('alert-danger', data.text);
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

            setAlert: function(type, message) {
                this.alert.type = type;
                this.alert.message = message;
            }

        },
    });
</script>