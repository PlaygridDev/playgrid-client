<div id="twoFactorRecoveryCodesRegeneratePopup">
    <div v-if="!showRecoveryCodes" class="d-flex flex-column align-items-center">
        {if $has_recovery_codes}
        <div class="alert alert-warning text-center w-100">
            {$two_factor_recovery_codes_regenerate_warning}
        </div>
        {/if}
        <div class="form-group w-100 mb-3">
            <label for="recoveryCodesPassword">{$two_factor_recovery_codes_password_label}</label>
            <input
                type="password"
                class="form-control"
                id="recoveryCodesPassword"
                name="password"
                autocomplete="current-password"
                placeholder="{$two_factor_recovery_codes_password_label}"
                v-model="password"
                @keyup.enter="regenerate()"
            >
        </div>
        <button
            type="button"
            class="btn btn-hero btn-sm btn-alt-primary text-uppercase mb-3"
            @click="regenerate()"
            :disabled="isSending"
        >
            {if $has_recovery_codes}{$two_factor_recovery_codes_regenerate_button}{else}{$two_factor_recovery_codes_create_button}{/if}
        </button>
        <span class="text-center mb-3" :class="alert.type" v-if="alert.type !== null && alert.message !== null">
            [[ alert.message ]]
        </span>
    </div>
    {include $.php.get_tpl_file('security/recovery_codes_list.tpl', "Modules\Globals\Settings\Settings")}
</div>
{include $.php.get_tpl_file('security/recovery_codes_mixin.tpl', "Modules\Globals\Settings\Settings")}
<script>
    window.twoFactorRecoveryCodesRegeneratePopup = new Vue({
        el: '#twoFactorRecoveryCodesRegeneratePopup',
        delimiters: ['[[', ']]'],
        mixins: [window.recoveryCodesMixin],
        data: {
            password: '',
            isSending: false,
            alert: {
                type: null,
                message: null
            }
        },
        mounted: function() {
            this.$nextTick(() => {
                $('#recoveryCodesPassword').focus();
            });
        },
        methods: {
            setAlert: function(type, message) {
                this.alert.type = type;
                this.alert.message = message;
            },
            regenerate: function() {

                if (this.isSending) {
                    return;
                }

                if (!this.password) {
                    this.setAlert('text-danger', '{$two_factor_recovery_codes_password_empty}');
                    return;
                }

                this.isSending = true;
                this.setAlert(null, null);

                fetch('/input', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        'module_form': "Modules\\Globals\\Settings\\Settings",
                        'module': 'regenerate_two_factor_recovery_codes',
                        'password': this.password,
                    }),
                }).then(response => {
                    if(response.ok) {
                        return response.json();
                    } else {
                        throw new Error('[regenerate] Error: ' + response.statusText);
                    }
                }).then(data => {
                    if('status' in data && 'text' in data) {
                        switch(data.status) {
                            case 'success':
                                this.password = '';
                                if (!this.showRecoveryCodesList(data.recovery_codes)) {
                                    this.setAlert('text-danger', data.text);
                                }
                                break;
                            case 'danger':
                                this.setAlert('text-danger', data.text);
                                this.password = '';
                                break;
                            default:
                                throw new Error('Unexpected status: ' + data.status);
                                break;
                        }
                    } else {
                        throw new Error('[regenerate] Unexpected response format');
                    }
                }).catch(error => {
                    console.error(error);
                    this.setAlert('text-danger', 'Error, try again later.');
                }).finally(() => {
                    this.isSending = false;
                });

            }
        }
    });
</script>
