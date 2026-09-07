<script>
    window.recoveryCodesConfig = {
        projectName: {$.php.json_encode($.site.config.project.name)},
        title: {$.php.json_encode($two_factor_recovery_codes_title)}
    };
    {ignore}
    window.recoveryCodesMixin = {
        data: function () {
            return {
                recoveryCodes: [],
                showRecoveryCodes: false
            };
        },
        methods: {
            showRecoveryCodesList: function (codes) {
                this.recoveryCodes = Object.values(codes || {});
                this.showRecoveryCodes = this.recoveryCodes.length > 0;
                if (this.showRecoveryCodes) {
                    $('#modal-ajax').on('hide.bs.modal.recoveryCodes', function (event) {
                        event.preventDefault();
                    });
                }
                return this.showRecoveryCodes;
            },
            downloadRecoveryCodes: function () {
                var config = window.recoveryCodesConfig;
                var projectName = config.projectName || 'playgrid';
                var fileName = projectName.replace(/[\\\/:*?"<>|\s]+/g, '_') + '_recovery_codes.txt';
                var content = projectName + ' - ' + config.title + '\r\n\r\n' + this.recoveryCodes.join('\r\n') + '\r\n';
                var blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                setTimeout(function () {
                    URL.revokeObjectURL(url);
                }, 1000);
            },
            finish: function () {
                $('#modal-ajax').off('hide.bs.modal.recoveryCodes').modal('hide');
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
        }
    };
    {/ignore}
</script>
